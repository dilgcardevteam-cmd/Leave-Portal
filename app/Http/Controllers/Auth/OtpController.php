<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use App\Mail\OtpMail;

class OtpController extends Controller
{
    public function show(Request $request): View
    {
        return view('auth.verify-otp', [
            'email' => session('email'),
            'message' => session('message'),
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp_code' => ['required', 'digits:6'],
        ]);

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return back()->withErrors(['email' => 'Account not found.']);
        }

        if (empty($user->otp_code) || $user->otp_code !== $request->otp_code) {
            return back()->withErrors(['otp_code' => 'Invalid code.']);
        }

        if ($user->otp_expires_at && now()->greaterThan($user->otp_expires_at)) {
            return back()->withErrors(['otp_code' => 'Code expired. Click resend.']);
        }

        $user->email_verified_at = now();
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        Auth::login($user);
        if ($user->role === 'admin') {
            return redirect()->route('admin.index');
        } elseif ($user->role === 'hr') {
            return redirect()->route('hr.index');
        } elseif ($user->role === 'ard') {
            return redirect()->route('ard.index');
        } elseif ($user->role === 'rd') {
            return redirect()->route('rd.index');
        } elseif ($user->role === 'lgmed' || $user->role === 'dc') {
            return redirect()->route('dc.index');
        }
        return redirect()->route('dashboard');
    }

    public function resend(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);
        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return back()->withErrors(['email' => 'Account not found.']);
        }
        $otp = (string) random_int(100000, 999999);
        $user->otp_code = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        if (config('mail.default')) {
            Mail::to($user->email)->send(new OtpMail($otp));
        }

        return back()->with('message', 'A new OTP has been sent.');
    }
}
