<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;

class LeavePdfController extends Controller
{
    public function download(Leave $leave, Request $request)
    {
        if (!$this->canAccess($leave)) {
            abort(403);
        }
        $path = self::generateAndStore($leave);
        $leave->final_pdf_path = $path;
        $leave->save();
        DB::table('leave_pdf_access_logs')->insert([
            'leave_id' => $leave->id,
            'user_id' => Auth::id(),
            'role' => Auth::user()?->role ?? null,
            'ip' => $request->ip(),
            'accessed_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        $abs = Storage::disk('local')->path($leave->final_pdf_path);
        return response()->file($abs, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Application-for-Leave-'.$leave->id.'.pdf"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function view(Leave $leave, Request $request)
    {
        if (!$this->canAccess($leave)) {
            abort(403);
        }
        $imagePath = self::generateFormImage($leave);
        DB::table('leave_pdf_access_logs')->insert([
            'leave_id' => $leave->id,
            'user_id' => Auth::id(),
            'role' => Auth::user()?->role ?? null,
            'ip' => $request->ip(),
            'accessed_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        $abs = Storage::disk('local')->path($imagePath);
        return response()->file($abs, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'inline; filename="LeaveApplicationForm-'.$leave->id.'.png"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
    public static function generateAndStore(Leave $leave): ?string
    {
        $html = view('leaves.pdf', ['leave' => $leave])->render();
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $pdf = $dompdf->output();
        $dir = 'leave_pdfs';
        if (!Storage::disk('local')->exists($dir)) {
            Storage::disk('local')->makeDirectory($dir);
        }
        $path = $dir.'/leave-'.$leave->id.'.pdf';
        Storage::disk('local')->put($path, $pdf, 'private');
        return $path;
    }

    private function canAccess(Leave $leave): bool
    {
        $user = Auth::user();
        if (!$user) return false;
        if ((int)$user->id === (int)$leave->user_id) return true;
        if ((int)$user->id === (int)($leave->hr_approved_by ?? 0)) return true;
        if ((int)$user->id === (int)($leave->dc_approved_by ?? 0)) return true;
        if (in_array($user->role, ['admin', 'hr', 'dc', 'rd', 'ard'])) return true;
        return false;
    }

    private static function generateFormImage(Leave $leave): string
    {
        @ini_set('memory_limit', '1024M');
        $bgCandidates = [
            public_path('images/LeaveApplicationForm.png'),
            public_path('LeaveApplicationForm.png'),
        ];
        $bg = null;
        foreach ($bgCandidates as $c) {
            if (file_exists($c)) { $bg = $c; break; }
        }
        if (!$bg) {
            abort(404, 'LeaveApplicationForm.png not found');
        }

        if (!function_exists('imagecreatefrompng')) {
            $dir = 'leave_images';
            if (!Storage::disk('local')->exists($dir)) {
                Storage::disk('local')->makeDirectory($dir);
            }
            $path = $dir.'/leave-'.$leave->id.'-base.png';
            Storage::disk('local')->put($path, file_get_contents($bg), 'private');
            return $path;
        }

        $im = \imagecreatefrompng($bg);
        if (!$im) abort(500, 'Unable to open base form image');
        \imagesavealpha($im, true);
        $black = \imagecolorallocate($im, 0, 0, 0);

        $font = null;
        $fontCandidates = [
            public_path('fonts/DejaVuSans.ttf'),
            'C:\Windows\Fonts\arial.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
        ];
        foreach ($fontCandidates as $fc) {
            if ($fc && file_exists($fc)) { $font = $fc; break; }
        }

        $w = \imagesx($im);
        $h = \imagesy($im);

        $user = $leave->user;
        $safe = fn(?string $v, string $fallback = ''): string => trim((string)$v) !== '' ? trim((string)$v) : $fallback;
        $dept = $safe($user?->province_office ?? null, $safe($user?->region ?? null, ''));
        $name = $safe($user?->display_name ?? null, $safe($user?->name ?? null, ''));
        $position = $safe($user?->position ?? null, '');
        $salary = $safe($user?->salary ?? null, '');
        $dateFiled = $leave->created_at?->timezone(config('app.timezone'))->format('F j, Y \a\t g:i A') ?? '';

        $write = function(string $text, float $rx, float $ry, int $size = 14) use ($im,$w,$h,$black,$font) {
            $x = (int)round($w * $rx);
            $y = (int)round($h * $ry);
            if ($font) {
                \imagettftext($im, $size, 0, $x, $y, $black, $font, $text);
            } else {
                \imagestring($im, 4, $x, $y - 12, $text, $black);
            }
        };

        // Write top details (relative positions tuned for the provided template)
        $write(strtoupper($dept), 0.17, 0.23, 16);        // 1. OFFICE/DEPARTMENT
        $write(strtoupper($name), 0.62, 0.23, 16);        // 2. NAME
        $write($dateFiled,        0.22, 0.27, 12);        // 3. Date of Filing
        $write(strtoupper($position), 0.62, 0.27, 12);    // 4. POSITION
        $write($salary !== '' ? ('P'.number_format((float)$salary, 2)) : '', 0.83, 0.27, 12); // 5. Salary

        // Extract checks from details_json
        $dj = is_array($leave->details_json ?? null) ? $leave->details_json : [];
        $typeName = trim((string)($dj['type_of_leave']['name'] ?? $leave->category?->name ?? ''));
        $typeLower = mb_strtolower($typeName);
        $hasType = function (array $needles) use ($typeLower): bool {
            foreach ($needles as $needle) {
                if (str_contains($typeLower, mb_strtolower($needle))) return true;
            }
            return false;
        };
        $vac = (array)($dj['details_of_leave']['vacation'] ?? []);
        $sick = (array)($dj['details_of_leave']['sick'] ?? []);
        $commutation = mb_strtolower(trim((string)($dj['commutation'] ?? 'not_requested')));
        $reco = (array)($dj['dc_recommendation'] ?? []);
        $recoDecision = (string)($reco['decision'] ?? '');

        $checks = [
            'vacation' => $hasType(['vacation']),
            'mandatory' => $hasType(['mandatory', 'forced']),
            'sick' => $hasType(['sick']),
        ];
        $mark = function(float $rx, float $ry) use ($write) {
            $write('✓', $rx, $ry, 16);
        };

        // Approximate checkbox positions (relative to template)
        if (!empty($checks['vacation'])) $mark(0.06, 0.37);
        if (!empty($checks['mandatory'])) $mark(0.06, 0.41);
        if (!empty($checks['sick'])) $mark(0.06, 0.45);
        if (!empty($vac['within_ph'])) $mark(0.58, 0.37);
        if (!empty($vac['abroad'])) $mark(0.58, 0.41);
        if (!empty($sick['hospital'])) $mark(0.58, 0.45);
        if (!empty($sick['outpatient'])) $mark(0.58, 0.49);
        if ($commutation === 'requested') $mark(0.58, 0.60); else $mark(0.51, 0.60);
        if ($recoDecision === 'approval') $mark(0.74, 0.74);

        $dir = 'leave_images';
        if (!Storage::disk('local')->exists($dir)) {
            Storage::disk('local')->makeDirectory($dir);
        }
        $path = $dir.'/leave-'.$leave->id.'.png';
        $abs = Storage::disk('local')->path($path);
        \imagepng($im, $abs, 1);
        \imagedestroy($im);
        return $path;
    }
}
