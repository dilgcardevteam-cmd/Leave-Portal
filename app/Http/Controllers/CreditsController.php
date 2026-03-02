<?php

namespace App\Http\Controllers;

use App\Models\UserLeaveCredit;
use App\Models\UserCreditHold;
use Illuminate\Support\Facades\Auth;

class CreditsController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $baseline = UserLeaveCredit::firstOrCreate(
            ['user_id' => $userId],
            ['vl_total' => 0, 'sl_total' => 0, 'credits_total' => 100, 'updated_by' => null]
        );
        $holds = UserCreditHold::with('leave')->where('user_id', $userId)->latest()->take(20)->get();
        return view('user.credits', compact('baseline', 'holds'));
    }
}
