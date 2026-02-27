<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\User;
use App\Models\UserLeaveCredit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class HrController extends Controller
{
    public function index()
    {
        $stats = [
            'requests' => Leave::count(),
            'pending' => Leave::where('status', 'pending')->count(),
            'approved' => Leave::where('status', 'approved')->count(),
            'rejected' => Leave::where('status', 'rejected')->count(),
        ];
        return view('hr.index', compact('stats'));
    }

    public function leaves(Request $request)
    {
        $query = Leave::with(['user', 'category'])->when(!$request->filled('status'), function ($q) {
            $q->where('workflow_state', 'hr_pending');
        })->latest();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($search) use ($q) {
                $search
                    ->whereHas('user', function ($sub) use ($q) {
                        $sub->where('name', 'like', '%'.$q.'%')
                            ->orWhere('username', 'like', '%'.$q.'%')
                            ->orWhere('email', 'like', '%'.$q.'%');
                    })
                    ->orWhereHas('category', function ($sub) use ($q) {
                        $sub->where('name', 'like', '%'.$q.'%');
                    })
                    ->orWhere('status', 'like', '%'.$q.'%')
                    ->orWhere('days', 'like', '%'.$q.'%')
                    ->orWhere('start_date', 'like', '%'.$q.'%')
                    ->orWhere('end_date', 'like', '%'.$q.'%');
            });
        }
        $leaves = $query->paginate(5)->withQueryString();
        return view('hr.leaves', compact('leaves'));
    }

    public function downloads(Request $request)
    {
        $query = Leave::with(['user', 'category'])->where('status', 'approved')->latest();
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($search) use ($q) {
                $search
                    ->whereHas('user', function ($sub) use ($q) {
                        $sub->where('name', 'like', '%'.$q.'%')
                            ->orWhere('username', 'like', '%'.$q.'%')
                            ->orWhere('email', 'like', '%'.$q.'%');
                    })
                    ->orWhereHas('category', function ($sub) use ($q) {
                        $sub->where('name', 'like', '%'.$q.'%');
                    })
                    ->orWhere('start_date', 'like', '%'.$q.'%')
                    ->orWhere('end_date', 'like', '%'.$q.'%');
            });
        }
        $leaves = $query->paginate(5)->withQueryString();
        return view('staff.approved_downloads', [
            'leaves' => $leaves,
            'sidebarPartial' => 'hr.partials.sidebar',
            'title' => 'Approved Requests Downloads',
            'routePrefix' => 'hr',
        ]);
    }

    public function showLeave(Leave $leave)
    {
        $leave->load(['user', 'category', 'approver']);
        $baseline = \App\Models\UserLeaveCredit::firstOrCreate(
            ['user_id' => $leave->user_id],
            ['vl_total' => 0, 'sl_total' => 0, 'credits_total' => 100, 'updated_by' => null]
        );
        return view('hr.leave_show', compact('leave', 'baseline'));
    }

    public function updateLeaveCredits(Request $request, Leave $leave)
    {
        $data = $request->validate([
            'vl_total' => ['nullable', 'numeric'],
            'vl_less' => ['nullable', 'numeric'],
            'sl_total' => ['nullable', 'numeric'],
            'sl_less' => ['nullable', 'numeric'],
        ]);
        $user = Auth::user();
        $name = trim((string)($user?->display_name ?? ''));
        if ($name === '') {
            $name = trim((string)($user?->name ?? ''));
        }
        if ($name === '') {
            $parts = [];
            if (!empty($user?->first_name)) $parts[] = $user->first_name;
            if (!empty($user?->middle_name)) $parts[] = mb_substr($user->middle_name, 0, 1).'.';
            if (!empty($user?->last_name)) $parts[] = $user->last_name;
            $name = trim(implode(' ', $parts));
        }
        if ($name === '') {
            $name = (string)($user?->email ?? $user?->username ?? '');
        }
        $credits = [
            'vacation' => [
                'total' => (float)($data['vl_total'] ?? 0),
                'less' => (float)($data['vl_less'] ?? 0),
                'balance' => (float)($data['vl_total'] ?? 0) - (float)($data['vl_less'] ?? 0),
            ],
            'sick' => [
                'total' => (float)($data['sl_total'] ?? 0),
                'less' => (float)($data['sl_less'] ?? 0),
                'balance' => (float)($data['sl_total'] ?? 0) - (float)($data['sl_less'] ?? 0),
            ],
            'meta' => [
                'updated_by_id' => Auth::id(),
                'updated_by_name' => $name,
                'updated_at' => Carbon::now()->toIso8601String(),
            ],
        ];
        $details = (array)$leave->details_json;
        $details['credits'] = $credits;
        $leave->details_json = $details;
        // Sync to dedicated columns for easier reporting
        $leave->vl_total = $credits['vacation']['total'];
        $leave->vl_less = $credits['vacation']['less'];
        $leave->vl_balance = $credits['vacation']['balance'];
        $leave->sl_total = $credits['sick']['total'];
        $leave->sl_less = $credits['sick']['less'];
        $leave->sl_balance = $credits['sick']['balance'];
        $leave->credits_updated_by = Auth::id();
        $leave->credits_updated_at = Carbon::now();
        $leave->save();

        // Sync baseline user credits table as well
        // Use the "total" values as the authoritative VL/SL totals
        $baseline = UserLeaveCredit::firstOrNew(['user_id' => $leave->user_id]);
        $baseline->vl_total = (float)($data['vl_total'] ?? 0);
        $baseline->sl_total = (float)($data['sl_total'] ?? 0);
        $baseline->updated_by = Auth::id();
        $baseline->save();

        return redirect()->route('hr.leaves.show', $leave)->with('status', 'Leave credits saved.')->with('active_tab', 'credits');
    }

    public function updateLeaveStatus(Request $request, Leave $leave)
    {
        $data = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'comment' => ['nullable', 'string', 'max:1000'],
            // Optional credit fields to persist when finalizing
            'vl_total' => ['nullable', 'numeric'],
            'vl_less' => ['nullable', 'numeric'],
            'sl_total' => ['nullable', 'numeric'],
            'sl_less' => ['nullable', 'numeric'],
        ]);
        // If credit inputs are present, persist them just like in updateLeaveCredits
        if ($request->hasAny(['vl_total','vl_less','sl_total','sl_less'])) {
            $user = Auth::user();
            $name = trim((string)($user?->display_name ?? '')) ?: trim((string)($user?->name ?? '')) ?: (string)($user?->email ?? $user?->username ?? '');
            $credits = [
                'vacation' => [
                    'total' => (float)($data['vl_total'] ?? 0),
                    'less' => (float)($data['vl_less'] ?? 0),
                    'balance' => (float)($data['vl_total'] ?? 0) - (float)($data['vl_less'] ?? 0),
                ],
                'sick' => [
                    'total' => (float)($data['sl_total'] ?? 0),
                    'less' => (float)($data['sl_less'] ?? 0),
                    'balance' => (float)($data['sl_total'] ?? 0) - (float)($data['sl_less'] ?? 0),
                ],
                'meta' => [
                    'updated_by_id' => Auth::id(),
                    'updated_by_name' => $name,
                    'updated_at' => Carbon::now()->toIso8601String(),
                ],
            ];
            $details = (array)$leave->details_json;
            $details['credits'] = $credits;
            $leave->details_json = $details;
            $leave->vl_total = $credits['vacation']['total'];
            $leave->vl_less = $credits['vacation']['less'];
            $leave->vl_balance = $credits['vacation']['balance'];
            $leave->sl_total = $credits['sick']['total'];
            $leave->sl_less = $credits['sick']['less'];
            $leave->sl_balance = $credits['sick']['balance'];
            $leave->credits_updated_by = Auth::id();
            $leave->credits_updated_at = Carbon::now();
            // Sync baseline totals too
            $baseline = \App\Models\UserLeaveCredit::firstOrNew(['user_id' => $leave->user_id]);
            $baseline->vl_total = (float)($data['vl_total'] ?? 0);
            $baseline->sl_total = (float)($data['sl_total'] ?? 0);
            $baseline->updated_by = Auth::id();
            $baseline->save();
        }
        if ($leave->workflow_state !== 'hr_pending') {
            return back()->withErrors(['status' => 'This request is not awaiting HR approval.']);
        }
        $leave->hr_approved_by = Auth::id();
        $leave->hr_approved_at = Carbon::now();
        $leave->hr_comment = $data['comment'] ?? null;
        if ($data['status'] === 'approved') {
            $leave->workflow_state = 'dc_pending';
            $leave->status = 'pending';
        } else {
            $leave->workflow_state = 'rejected';
            $leave->status = 'rejected';
        }
        $dj = (array)$leave->details_json;
        $dj['hr_approval'] = [
            'decision' => $data['status'],
            'comment' => $data['comment'] ?? null,
            'meta' => [
                'by' => Auth::id(),
                'at' => Carbon::now()->toIso8601String(),
                'name' => (string)(Auth::user()?->display_name ?? Auth::user()?->name ?? ''),
            ],
        ];
        $leave->details_json = $dj;
        $leave->save();
        \App\Models\LeaveApprovalLog::create([
            'leave_id' => $leave->id,
            'step' => 'hr',
            'action' => $data['status'] === 'approved' ? 'approved' : 'rejected',
            'comment' => $data['comment'] ?? null,
            'acted_by' => Auth::id(),
            'acted_at' => Carbon::now(),
        ]);
        if ($data['status'] === 'approved') {
            $dcs = \App\Models\User::where('role', 'dc')->get();
            \Illuminate\Support\Facades\Notification::send($dcs, new \App\Notifications\LeaveStageNotification(
                $leave,
                'Leave ready for Division Chief review',
                'A leave request has been approved by HR and requires your review.'
            ));
        } else {
            $leave->user?->notify(new \App\Notifications\LeaveStageNotification(
                $leave,
                'Leave request rejected by HR',
                'Your leave request has been rejected by HR.'
            ));
        }
        return redirect()->route('hr.leaves.show', $leave)->with('status', 'HR decision recorded.')->with('active_tab', 'credits');
    }

    public function help()
    {
        return view('hr.help');
    }

    public function settings(Request $request)
    {
        $active = in_array((string)$request->query('view'), ['credit','signatories'], true)
            ? (string)$request->query('view')
            : 'credit';
        $users = User::query()
            ->with('credits')
            ->where('role', 'user')
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = trim((string) $request->query('q'));
                $query->where(function ($search) use ($q) {
                    $search
                        ->where('name', 'like', '%'.$q.'%')
                        ->orWhere('username', 'like', '%'.$q.'%')
                        ->orWhere('email', 'like', '%'.$q.'%')
                        ->orWhere('position', 'like', '%'.$q.'%')
                        ->orWhere('province_office', 'like', '%'.$q.'%');
                });
            })
            ->orderBy('name')
            ->paginate(4, ['*'], 'users_page')
            ->withQueryString();
        // Auto-apply monthly credits based on user created_at (same day-of-month rule)
        $users->getCollection()->transform(function (User $user) {
            $credits = $this->applyAutoMonthlyCredits($user);
            if ($credits) {
                $user->setRelation('credits', $credits);
            }
            return $user;
        });

        $signatories = User::query()
            ->whereIn('role', ['hr', 'dc', 'rd', 'ard'])
            ->when($request->filled('role') && in_array((string)$request->query('role'), ['hr','dc','rd','ard'], true), function ($q) use ($request) {
                $q->where('role', (string)$request->query('role'));
            })
            ->orderByRaw("FIELD(role, 'hr', 'dc', 'rd', 'ard')")
            ->orderBy('name')
            ->paginate(4, ['*'], 'signatories_page')
            ->withQueryString();

        return view('hr.settings', compact('users', 'signatories', 'active'));
    }

    public function updateCreditManagement(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'division' => ['nullable', 'string', 'max:255'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'credits' => ['required', 'numeric', 'min:0'],
        ]);

        $user->name = $data['name'];
        $user->position = $data['position'] ?? null;
        $user->province_office = $data['division'] ?? null;
        if (Schema::hasColumn('users', 'salary')) {
            $user->salary = $data['salary'] ?? null;
        }
        $user->save();

        $credits = UserLeaveCredit::firstOrNew(['user_id' => $user->id]);
        $credits->credits_total = (float) $data['credits'];
        $credits->updated_by = Auth::id();
        $credits->save();

        return redirect()->route('hr.settings', ['q' => $request->query('q')])
            ->with('status', 'Credit management record updated.');
    }

    public function applyMonthlyCredits(Request $request, User $user)
    {
        $data = $request->validate([
            'vl_add' => ['required', 'numeric', 'min:0'],
            'sl_add' => ['required', 'numeric', 'min:0'],
        ]);

        $credits = UserLeaveCredit::firstOrNew(['user_id' => $user->id]);
        $credits->vl_total = (float)($credits->vl_total ?? 0) + (float)$data['vl_add'];
        $credits->sl_total = (float)($credits->sl_total ?? 0) + (float)$data['sl_add'];
        $credits->credits_total = (float)$credits->vl_total + (float)$credits->sl_total;
        $credits->updated_by = Auth::id();
        $credits->save();

        return redirect()->route('hr.settings', ['q' => $request->query('q')])
            ->with('status', 'Monthly credits applied.');
    }

    private function applyAutoMonthlyCredits(User $user): ?UserLeaveCredit
    {
        if (!$user->created_at) return $user->credits;
        $credits = UserLeaveCredit::firstOrCreate(
            ['user_id' => $user->id],
            ['vl_total' => 0, 'sl_total' => 0, 'credits_total' => 0, 'updated_by' => null]
        );

        $now = Carbon::now();
        $base = $credits->last_monthly_credit_at
            ? Carbon::parse($credits->last_monthly_credit_at)
            : Carbon::parse($user->created_at);
        $next = $base->copy()->addMonthsNoOverflow(1);
        $count = 0;
        while ($next->lessThanOrEqualTo($now)) {
            $count++;
            $next->addMonthsNoOverflow(1);
        }
        if ($count < 1) return $credits;

        $add = 1.25 * $count;
        $credits->vl_total = (float)($credits->vl_total ?? 0) + $add;
        $credits->sl_total = (float)($credits->sl_total ?? 0) + $add;
        $credits->credits_total = (float)$credits->vl_total + (float)$credits->sl_total;
        $credits->last_monthly_credit_at = $next->copy()->subMonthNoOverflow();
        $credits->updated_by = Auth::id() ?? $credits->updated_by;
        $credits->save();

        return $credits;
    }

    public function updateSignatory(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'division' => ['nullable', 'string', 'max:255'],
            'signature' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
        ]);

        if (!in_array((string) $user->role, ['hr', 'dc', 'rd', 'ard'], true)) {
            return back()->withErrors(['signatory' => 'Selected account is not a signatory role.']);
        }

        $user->name = (string) $request->input('name');
        $user->position = $request->input('position');
        $user->province_office = $request->input('division');
        if ($request->hasFile('signature')) {
            $path = $request->file('signature')->store('signatures', 'public');
            $user->signature_path = $path;
        }
        $user->save();

        return back()->with('status', 'Signatory updated.');
    }
}
