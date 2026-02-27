<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Mail\OtpMail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'mobile_number' => ['nullable', 'string', 'max:50'],
            'sex' => ['nullable', 'string', 'max:10'],
            'region' => ['nullable', 'string', 'max:255'],
            'province_office' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'id_no' => ['nullable', 'string', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $otp = (string) random_int(100000, 999999);
        $expiresAt = now()->addMinutes(10);

        // derive a unique username from email local part
        $baseUsername = Str::of($validated['email'])->before('@')->slug('_')->limit(30, '');
        $username = (string) $baseUsername;
        $suffix = 1;
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername.'_'.$suffix;
            $suffix++;
        }

        $user = User::create([
            'username' => $username,
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'name' => trim($validated['first_name'].' '.($validated['middle_name'] ?? '').' '.$validated['last_name']),
            'email' => $validated['email'],
            'mobile_number' => $validated['mobile_number'] ?? null,
            'sex' => $validated['sex'] ?? null,
            'region' => $validated['region'] ?? null,
            'province_office' => $validated['province_office'] ?? null,
            'position' => $validated['position'] ?? null,
            'id_no' => $validated['id_no'] ?? null,
            'role' => 'user',
            'otp_code' => $otp,
            'otp_expires_at' => $expiresAt,
            'password' => Hash::make($validated['password']),
        ]);

        event(new Registered($user));

        if (config('mail.default')) {
            Mail::to($user->email)->send(new OtpMail($otp));
        }

        return redirect()->route('otp.show')->with([
            'email' => $user->email,
            'message' => 'We sent a 6-digit OTP to your email.',
        ]);
    }
}
