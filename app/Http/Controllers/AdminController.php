<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\LeaveCategory;
use App\Models\User;
use App\Models\UserLeaveCredit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'categories' => LeaveCategory::count(),
            'pending' => Leave::where('status', 'pending')->count(),
            'approved' => Leave::where('status', 'approved')->count(),
            'rejected' => Leave::where('status', 'rejected')->count(),
            'requests' => Leave::count(),
        ];
        return view('admin.index', compact('stats'));
    }

    public function users()
    {
        $users = User::with('credits')->orderBy('name')->paginate(15);
        return view('admin.users', compact('users'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => ['required', 'in:user,admin,hr,ard,rd,dc,lgmed'],
        ]);
        $user->role = $request->role;
        $user->save();
        return back()->with('status', 'Role updated.');
    }

    public function updateUserCredits(Request $request, User $user)
    {
        $data = $request->validate([
            'vl_total' => ['required','numeric'],
            'sl_total' => ['required','numeric'],
        ]);
        $credits = UserLeaveCredit::firstOrNew(['user_id' => $user->id]);
        $credits->vl_total = (float)$data['vl_total'];
        $credits->sl_total = (float)$data['sl_total'];
        $credits->updated_by = Auth::id();
        $credits->save();
        return back()->with('status', 'User leave credits updated.');
    }

    public function leaves(Request $request)
    {
        $query = Leave::with(['user', 'category'])->latest();
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
        $leaves = $query->paginate(15)->withQueryString();
        return view('admin.leaves', compact('leaves'));
    }

    public function updateLeaveStatus(Request $request, Leave $leave)
    {
        $request->validate([
            'status' => ['required', 'in:pending,approved,rejected'],
        ]);
        $leave->status = $request->status;
        if ($request->status === 'approved') {
            $leave->approved_by = Auth::id();
            $leave->approved_at = Carbon::now();
        } else {
            $leave->approved_by = null;
            $leave->approved_at = null;
        }
        $leave->save();
        return back()->with('status', 'Leave status updated.');
    }

    public function showLeave(Leave $leave)
    {
        $leave->load(['user', 'category', 'approver']);
        return view('admin.leave_show', compact('leave'));
    }
}
