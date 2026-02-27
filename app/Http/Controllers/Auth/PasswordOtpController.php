<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PasswordOtpController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password', [
            'email' => session('email'),
            'status' => session('status'),
        ]);
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return back()->withErrors(['email' => 'Account not found.'])->withInput();
        }

        $otp = (string) random_int(10000000, 99999999);
        $user->otp_code = $otp;
        $user->otp_expires_at = now()->addMinutes(2);
        $user->save();

        if (config('mail.default')) {
            Mail::to($user->email)->send(new OtpMail($otp));
        }

        return back()
            ->with('status', 'We sent a 6-digit OTP to your email.')
            ->with('email', $user->email)
            ->with('otp_sent_at', now()->timestamp);
    }

    public function resetWithOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp_code' => ['required', 'digits:8'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return back()->withErrors(['email' => 'Account not found.'])->withInput()->with('otp_sent_at', session('otp_sent_at'));
        }

        if (empty($user->otp_code) || $user->otp_code !== $request->otp_code) {
            return back()->withErrors(['otp_code' => 'Invalid code.'])->withInput()->with('otp_sent_at', session('otp_sent_at'));
        }

        if ($user->otp_expires_at && now()->greaterThan($user->otp_expires_at)) {
            return back()->withErrors(['otp_code' => 'Code expired. Click resend.'])->withInput()->with('otp_sent_at', session('otp_sent_at'));
        }

        $user->password = Hash::make($request->password);
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        return redirect()->route('login')->with('status', 'Password reset successfully. You can now log in.');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp_code' => ['required', 'digits:8'],
        ]);

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return back()->withErrors(['email' => 'Account not found.'])->withInput();
        }
        if (empty($user->otp_code) || $user->otp_code !== $request->otp_code) {
            return back()->withErrors(['otp_code' => 'Invalid code.'])->withInput();
        }
        if ($user->otp_expires_at && now()->greaterThan($user->otp_expires_at)) {
            return back()->withErrors(['otp_code' => 'Code expired. Click resend.'])->withInput();
        }

        return back()
            ->withInput($request->only('email', 'otp_code'))
            ->with('otp_verified_email', $user->email)
            ->with('status', 'OTP verified. You can now reset your password.')
            ->with('email', $user->email);
    }
}
