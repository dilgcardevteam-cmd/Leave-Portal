<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DcController extends Controller
{
    public function index()
    {
        $stats = [
            'requests' => Leave::count(),
            'pending' => Leave::where('status', 'pending')->count(),
            'approved' => Leave::where('status', 'approved')->count(),
            'rejected' => Leave::where('status', 'rejected')->count(),
        ];
        return view('dc.index', compact('stats'));
    }

    public function leaves(Request $request)
    {
        $query = Leave::with(['user', 'category'])->where('workflow_state', 'dc_pending')->latest();
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
        return view('dc.leaves', compact('leaves'));
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
            'sidebarPartial' => 'dc.partials.sidebar',
            'title' => 'Approved Requests Downloads',
            'routePrefix' => 'dc',
        ]);
    }

    public function showLeave(Leave $leave)
    {
        $leave->load(['user', 'category', 'approver']);
        return view('dc.leave_show', compact('leave'));
    }

    public function updateLeaveStatus(Request $request, Leave $leave)
    {
        $data = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'reco_for_approval' => ['nullable', 'boolean'],
            'reco_for_disapproval' => ['nullable', 'boolean'],
            'reco_approval_remarks' => ['nullable', 'string', 'max:500'],
            'reco_disapproval_reason' => ['nullable', 'string', 'max:1000'],
        ]);
        if ($leave->workflow_state !== 'dc_pending') {
            return back()->withErrors(['status' => 'This request is not awaiting DC approval.']);
        }
        try {
            return \Illuminate\Support\Facades\DB::transaction(function () use ($leave, $data) {
                $leave->refresh();
                if ($leave->workflow_state !== 'dc_pending') {
                    return back()->withErrors(['status' => 'This request is not awaiting DC approval.']);
                }
                $dj = (array)$leave->details_json;
                $decisionSeed = null;
                if (!empty($data['reco_for_disapproval'])) {
                    $decisionSeed = 'disapproval';
                } elseif (!empty($data['reco_for_approval'])) {
                    $decisionSeed = 'approval';
                }
                if ($decisionSeed) {
                    $dj['dc_recommendation'] = [
                        'decision' => $decisionSeed,
                        'remarks' => $data['reco_approval_remarks'] ?? null,
                        'reason' => $data['reco_disapproval_reason'] ?? null,
                        'meta' => [
                            'updated_by_id' => Auth::id(),
                            'updated_by_name' => (string)(Auth::user()?->display_name ?? Auth::user()?->name ?? Auth::user()?->email ?? ''),
                            'updated_at' => Carbon::now()->toIso8601String(),
                        ],
                    ];
                }
                $leave->dc_approved_by = Auth::id();
                $leave->dc_approved_at = Carbon::now();
                $leave->dc_comment = $data['comment'] ?? null;
                if ($data['status'] === 'approved') {
                    $leave->workflow_state = 'final_pending';
                    $leave->status = 'pending';
                } else {
                    $leave->workflow_state = 'rejected';
                    $leave->status = 'rejected';
                }
                $dj['dc_approval'] = [
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
                    'step' => 'dc',
                    'action' => $data['status'] === 'approved' ? 'approved' : 'rejected',
                    'comment' => $data['comment'] ?? null,
                    'acted_by' => Auth::id(),
                    'acted_at' => Carbon::now(),
                ]);
                if ($data['status'] === 'approved') {
                    $rds = \App\Models\User::where('role', 'rd')->get();
                    $ards = \App\Models\User::where('role', 'ard')->get();
                    \Illuminate\Support\Facades\Notification::send($rds->merge($ards), new \App\Notifications\LeaveStageNotification(
                        $leave,
                        'Leave ready for final approval',
                        'A leave request has been approved by the Division Chief and requires final approval.'
                    ));
                } else {
                    $hold = \App\Models\UserCreditHold::where('leave_id', $leave->id)->where('status', 'held')->first();
                    if ($hold) { $hold->status = 'released'; $hold->save(); }
                    $leave->user?->notify(new \App\Notifications\LeaveStageNotification(
                        $leave,
                        'Leave request rejected by Division Chief',
                        'Your leave request has been rejected by the Division Chief.'
                    ));
                }
                return redirect()->route('dc.leaves.show', $leave)->with('status', 'DC decision recorded.')->with('active_tab', 'reco');
            });
        } catch (\Throwable $e) {
            \Log::error('DC finalize failed', ['leave_id' => $leave->id, 'error' => $e->getMessage()]);
            return back()->withErrors(['status' => 'Failed to finalize DC decision. Please try again.']);
        }
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
        try {
            return \Illuminate\Support\Facades\DB::transaction(function () use ($leave, $payload) {
                $details = (array)$leave->details_json;
                $details['dc_recommendation'] = $payload;
                $leave->details_json = $details;
                $leave->save();
                return redirect()->route('dc.leaves.show', $leave)->with('status', 'Recommendation saved.')->with('active_tab', 'reco');
            });
        } catch (\Throwable $e) {
            \Log::error('DC recommendation save failed', ['leave_id' => $leave->id, 'error' => $e->getMessage()]);
            return back()->withErrors(['status' => 'Failed to save recommendation. Please try again.'])->withInput();
        }
    }
}
