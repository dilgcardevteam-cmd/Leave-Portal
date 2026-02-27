<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\LeaveCategory;
use App\Models\UserLeaveCredit;
use App\Models\UserCreditHold;
use App\Models\User;
use App\Notifications\NewLeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Leave::with('category')
            ->where('user_id', Auth::id())
            ->latest();
        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $q = trim((string)$request->q);
            $query->where(function ($search) use ($q) {
                $search
                    ->whereHas('category', function ($sub) use ($q) {
                        $sub->where('name', 'like', '%'.$q.'%');
                    })
                    ->orWhere('status', 'like', '%'.$q.'%')
                    ->orWhere('workflow_state', 'like', '%'.$q.'%')
                    ->orWhere('days', 'like', '%'.$q.'%')
                    ->orWhere('start_date', 'like', '%'.$q.'%')
                    ->orWhere('end_date', 'like', '%'.$q.'%');
            });
        }
        $leaves = $query->paginate(6)->withQueryString();
        return view('leaves.index', compact('leaves'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Follow the same sequence as the Categories list (insertion order)
        $categories = LeaveCategory::orderBy('id', 'asc')->get();
        return view('leaves.create', compact('categories'));
    }

    public function edit($leave)
    {
        $leave = Leave::where('id', $leave)->where('user_id', Auth::id())->first();
        if (! $leave) {
            return redirect()->route('leaves.index')->withErrors(['status' => 'Leave request not found.']);
        }
        if ($leave->status !== 'pending') {
            return redirect()->route('leaves.index')->withErrors(['status' => 'Only pending requests can be edited.']);
        }
        // Keep the same ordering for consistency with the create view
        $categories = LeaveCategory::orderBy('id', 'asc')->get();
        return view('leaves.edit', compact('leave', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'leave_category_id' => ['required'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['nullable', 'string'],
            'other_leave_name' => ['nullable', 'string', 'max:255'],
            // Optional detail fields
            'detail_vac_within' => ['nullable', 'string', 'max:255'],
            'detail_vac_abroad' => ['nullable', 'string', 'max:255'],
            'detail_sick_hospital' => ['nullable', 'string', 'max:255'],
            'detail_sick_outpatient' => ['nullable', 'string', 'max:255'],
            'detail_women' => ['nullable', 'string', 'max:255'],
            'detail_study_master' => ['nullable', 'boolean'],
            'detail_study_bar' => ['nullable', 'boolean'],
            'detail_other_monetization' => ['nullable', 'boolean'],
            'detail_other_terminal' => ['nullable', 'boolean'],
            // Working days and commutation
            'applied_days' => ['nullable', 'integer', 'min:1'],
            'inclusive_dates_text' => ['nullable', 'string', 'max:255'],
            'commutation' => ['nullable', 'in:requested,not_requested'],
        ]);

        try {
            return DB::transaction(function () use ($request, $validated) {
                // Resolve category id, allowing "other" with custom name
                $categoryId = null;
                $categoryName = null;
                $category = null;
                if ($validated['leave_category_id'] === 'other') {
                    $request->validate([
                        'other_leave_name' => ['required', 'string', 'max:255'],
                    ]);
                    $name = trim((string)$request->input('other_leave_name'));
                    $category = LeaveCategory::firstOrCreate(['name' => $name], ['description' => null, 'default_credits' => 0]);
                    $categoryId = $category->id;
                    $categoryName = $category->name;
                } else {
                    // Ensure provided id exists
                    $request->validate([
                        'leave_category_id' => ['required', 'exists:leave_categories,id'],
                    ]);
                    $categoryId = (int) $validated['leave_category_id'];
                    $category = LeaveCategory::find($categoryId);
                    $categoryName = optional($category)->name;
                }
                $days = isset($validated['applied_days']) && (int)$validated['applied_days'] > 0
                    ? (int)$validated['applied_days']
                    : Carbon::parse($validated['start_date'])->diffInDays(Carbon::parse($validated['end_date'])) + 1;
                $detailsJson = $this->collectDetails($request, $categoryId, $categoryName, $days);
                $leave = Leave::create([
                    'user_id' => Auth::id(),
                    'leave_category_id' => $categoryId,
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],
                    'days' => $days,
                    'reason' => $this->composeReason($request, $validated['reason'] ?? null),
                    'details_json' => $detailsJson,
                    'status' => 'pending',
                    'workflow_state' => 'hr_pending',
                ]);
                // Ensure baseline credits record exists with default 100
                $baseline = UserLeaveCredit::firstOrCreate(
                    ['user_id' => Auth::id()],
                    ['vl_total' => 0, 'sl_total' => 0, 'credits_total' => 100, 'updated_by' => null]
                );
                // Place a credit hold based on leave category defaults * days
                $name = mb_strtolower((string)($category?->name ?? ''));
                if (str_contains($name, 'vacation')) {
                    $perDay = (float)($category?->vl_default_credits ?? $category?->default_credits ?? 0);
                } elseif (str_contains($name, 'sick')) {
                    $perDay = (float)($category?->sl_default_credits ?? $category?->default_credits ?? 0);
                } else {
                    $perDay = (float)($category?->default_credits ?? 0);
                }
                $amount = round($perDay * $days, 3);
                if ($amount > 0) {
                    UserCreditHold::updateOrCreate(
                        ['leave_id' => $leave->id],
                        [
                            'user_id' => Auth::id(),
                            'leave_category_id' => $categoryId,
                            'amount' => $amount,
                            'status' => 'held',
                        ]
                    );
                }
                // Notify HR users about the new leave request
                $hrs = User::where('role', 'hr')->get();
                if ($hrs->isNotEmpty()) {
                    Notification::send($hrs, new NewLeaveRequest($leave));
                }
                Log::info('Leave created', ['leave_id' => $leave->id, 'user_id' => $leave->user_id]);
                return redirect()->route('leaves.index')->with('status', 'Leave request submitted.');
            });
        } catch (\Throwable $e) {
            Log::error('Failed to create leave', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->withInput()->withErrors(['status' => 'Failed to submit request. Please try again.']);
        }
    }

    private function composeReason(Request $request, ?string $base): string
    {
        $lines = [];
        if ($base) {
            $lines[] = $base;
        }
        $sections = [];
        $vac = array_filter([
            'Within the Philippines' => trim((string)$request->input('detail_vac_within', '')),
            'Abroad' => trim((string)$request->input('detail_vac_abroad', '')),
        ], fn($v) => $v !== '');
        if ($vac) {
            $sections[] = 'Vacation/Special Privilege Leave: '.collect($vac)->map(fn($v,$k)=>"$k: $v")->implode('; ');
        }
        $sick = array_filter([
            'In Hospital' => trim((string)$request->input('detail_sick_hospital', '')),
            'Out Patient' => trim((string)$request->input('detail_sick_outpatient', '')),
        ], fn($v) => $v !== '');
        if ($sick) {
            $sections[] = 'Sick Leave: '.collect($sick)->map(fn($v,$k)=>"$k: $v")->implode('; ');
        }
        if ($w = trim((string)$request->input('detail_women', ''))) {
            $sections[] = 'Special Leave Benefits for Women: '.$w;
        }
        $checks = [];
        if ($request->boolean('detail_study_master')) $checks[] = "Study Leave: Completion of Master's Degree";
        if ($request->boolean('detail_study_bar')) $checks[] = 'Study Leave: BAR/Board Examination Review';
        if ($request->boolean('detail_other_monetization')) $checks[] = 'Other Purpose: Monetization of Leave Credits';
        if ($request->boolean('detail_other_terminal')) $checks[] = 'Other Purpose: Terminal Leave';
        if ($checks) {
            $sections = array_merge($sections, $checks);
        }
        // Working days and inclusive dates
        if ($request->filled('applied_days')) {
            $sections[] = 'Number of Working Days Applied For: '.$request->input('applied_days');
        }
        if ($request->filled('inclusive_dates_text')) {
            $sections[] = 'Inclusive Dates: '.$request->input('inclusive_dates_text');
        }
        // Commutation
        if ($request->filled('commutation')) {
            $sections[] = 'Commutation: '.ucwords(str_replace('_',' ', $request->input('commutation')));
        }
        if ($sections) {
            $lines[] = 'Details of Leave — '.implode(' | ', $sections);
        }
        return trim(implode("\n", $lines));
    }

    private function collectDetails(Request $request, int $categoryId, ?string $categoryName, int $days): array
    {
        return [
            'type_of_leave' => [
                'id' => $categoryId,
                'name' => $categoryName,
            ],
            'details_of_leave' => [
                'vacation' => [
                    'within_ph' => trim((string)$request->input('detail_vac_within', '')) ?: null,
                    'abroad' => trim((string)$request->input('detail_vac_abroad', '')) ?: null,
                ],
                'sick' => [
                    'hospital' => trim((string)$request->input('detail_sick_hospital', '')) ?: null,
                    'outpatient' => trim((string)$request->input('detail_sick_outpatient', '')) ?: null,
                ],
                'women' => trim((string)$request->input('detail_women', '')) ?: null,
                'study' => [
                    'master' => (bool)$request->boolean('detail_study_master'),
                    'bar' => (bool)$request->boolean('detail_study_bar'),
                ],
                'other' => [
                    'monetization' => (bool)$request->boolean('detail_other_monetization'),
                    'terminal' => (bool)$request->boolean('detail_other_terminal'),
                ],
            ],
            'working_days' => [
                'applied_days' => (int)($request->input('applied_days') ?: $days),
                'inclusive_dates' => trim((string)$request->input('inclusive_dates_text', '')) ?: null,
            ],
            'commutation' => $request->filled('commutation') ? (string)$request->input('commutation') : null,
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(Leave $leave)
    {
        //
    }

    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $leave)
    {
        $leave = Leave::where('id', $leave)->where('user_id', Auth::id())->first();
        if (! $leave) {
            return redirect()->route('leaves.index')->withErrors(['status' => 'Leave request not found.']);
        }
        if ($leave->status !== 'pending') {
            return redirect()->route('leaves.index')->withErrors(['status' => 'Only pending requests can be updated.']);
        }
        $validated = $request->validate([
            'leave_category_id' => ['required', 'exists:leave_categories,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['nullable', 'string'],
        ]);
        $days = Carbon::parse($validated['start_date'])->diffInDays(Carbon::parse($validated['end_date'])) + 1;
        $leave->update([
            'leave_category_id' => $validated['leave_category_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'days' => $days,
            'reason' => $validated['reason'] ?? null,
        ]);
        return redirect()->route('leaves.index')->with('status', 'Leave request updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($leave)
    {
        $leave = Leave::where('id', $leave)->where('user_id', Auth::id())->first();
        if (! $leave) {
            return redirect()->route('leaves.index')->withErrors(['status' => 'Leave request not found.']);
        }
        if ($leave->status !== 'pending') {
            return back()->withErrors(['status' => 'Only pending requests can be deleted.']);
        }
        $leave->delete();
        return back()->with('status', 'Leave request deleted.');
    }
}
