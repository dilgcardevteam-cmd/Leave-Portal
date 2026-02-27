<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\UserCreditHold;
use App\Models\UserLeaveCredit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class ArdController extends Controller
{
    public function index()
    {
        $stats = [
            'requests' => Leave::count(),
            'pending' => Leave::where('status', 'pending')->count(),
            'approved' => Leave::where('status', 'approved')->count(),
            'rejected' => Leave::where('status', 'rejected')->count(),
        ];
        return view('ard.index', compact('stats'));
    }

    public function leaves(Request $request)
    {
        $query = Leave::with(['user', 'category'])->whereIn('workflow_state', ['final_pending','approved','rejected'])->latest();
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
        return view('ard.leaves', compact('leaves'));
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
            'sidebarPartial' => 'ard.partials.sidebar',
            'title' => 'Approved Requests Downloads',
            'routePrefix' => 'ard',
        ]);
    }

    public function showLeave(Leave $leave)
    {
        $leave->load(['user', 'category', 'approver']);
        return view('ard.leave_show', compact('leave'));
    }

    public function updateLeaveStatus(Request $request, Leave $leave)
    {
        $data = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);
        if ($leave->workflow_state !== 'final_pending') {
            return back()->withErrors(['status' => 'This request is not awaiting final approval.']);
        }
        return \Illuminate\Support\Facades\DB::transaction(function () use ($leave, $data) {
            $leave->refresh();
            if ($leave->workflow_state !== 'final_pending') {
                return back()->withErrors(['status' => 'Request already finalized.']);
            }
            if ($data['status'] === 'approved') {
                $leave->final_approved_by = Auth::id();
                $leave->final_approved_at = Carbon::now();
                $leave->final_approver_role = 'ard';
                $leave->final_comment = $data['comment'] ?? null;
                $leave->status = 'approved';
                $leave->workflow_state = 'approved';
                $hold = UserCreditHold::where('leave_id', $leave->id)->where('status', 'held')->first();
                if ($hold) {
                    $baseline = UserLeaveCredit::firstOrCreate(
                        ['user_id' => $leave->user_id],
                        ['vl_total' => 0, 'sl_total' => 0, 'credits_total' => 100, 'updated_by' => null]
                    );
                    $baseline->credits_total = max(0, (float)$baseline->credits_total - (float)$hold->amount);
                    $baseline->updated_by = Auth::id();
                    $baseline->save();
                    $hold->status = 'applied';
                    $hold->save();
                }
                $path = \App\Http\Controllers\LeavePdfController::generateAndStore($leave);
                $leave->final_pdf_path = $path;
            } else {
                $leave->final_comment = $data['comment'] ?? null;
                $leave->status = 'rejected';
                $leave->workflow_state = 'rejected';
                $hold = UserCreditHold::where('leave_id', $leave->id)->where('status', 'held')->first();
                if ($hold) { $hold->status = 'released'; $hold->save(); }
            }
            $leave->save();
            \App\Models\LeaveApprovalLog::create([
                'leave_id' => $leave->id,
                'step' => 'ard',
                'action' => $data['status'] === 'approved' ? 'approved' : 'rejected',
                'comment' => $data['comment'] ?? null,
                'acted_by' => Auth::id(),
                'acted_at' => \Illuminate\Support\Carbon::now(),
            ]);
            $notifyTitle = $data['status'] === 'approved' ? 'Leave approved' : 'Leave rejected';
            $notifyMsg = $data['status'] === 'approved'
                ? 'Your leave has been approved.'
                : 'Your leave has been rejected at final stage.';
            $leave->user?->notify(new \App\Notifications\LeaveStageNotification($leave, $notifyTitle, $notifyMsg));
            return back()->with('status', 'Final decision recorded.');
        });
    }

    public function saveRecommendation(Request $request, Leave $leave)
    {
        $data = $request->validate([
            'for_approval' => ['nullable', 'boolean'],
            'for_disapproval' => ['nullable', 'boolean'],
            'approval_remarks' => ['nullable', 'string', 'max:500'],
            'disapproval_reason' => ['nullable', 'string', 'max:1000'],
        ]);
        $decision = null;
        if (!empty($data['for_disapproval'])) {
            $decision = 'disapproval';
        } elseif (!empty($data['for_approval'])) {
            $decision = 'approval';
        }
        if (!$decision) {
            return back()->with('status', 'Please select a recommendation option.')->withInput();
        }
        $payload = [
            'decision' => $decision,
            'remarks' => $data['approval_remarks'] ?? null,
            'reason' => $data['disapproval_reason'] ?? null,
            'meta' => [
                'updated_by_id' => Auth::id(),
                'updated_by_name' => (string)(Auth::user()?->display_name ?? Auth::user()?->name ?? Auth::user()?->email ?? ''),
                'updated_at' => Carbon::now()->toIso8601String(),
            ],
        ];
        $details = (array)$leave->details_json;
        $details['ard_recommendation'] = $payload;
        $leave->details_json = $details;
        $leave->save();
        return back()->with('status', 'Recommendation saved.');
    }
}
