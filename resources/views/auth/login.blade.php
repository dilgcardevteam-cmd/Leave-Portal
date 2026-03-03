<x-guest-layout>
    <div class="text-center">
        <h1 class="text-[22px] font-semibold text-gray-900">Hello, friend!</h1>
        <p class="mt-1 text-[11px] text-gray-500">Please sign in to continue.</p>
    </div>

    <x-auth-session-status class="mt-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-[11px] text-gray-500 mb-1">Email</label>
            <div class="glow-field">
                <span class="glow-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path fill="currentColor" d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 2c-4.2 0-8 2.1-8 5v1h16v-1c0-2.9-3.8-5-8-5Z"/></svg>
                </span>
                <input id="email" class="auth-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="alex.jordan@gmail.com">
            </div>
            @if ($errors->has('email'))
                <div class="mt-2 text-[11px] text-red-600">{{ $errors->first('email') }}</div>
            @endif
        </div>

        <div>
            <label for="password" class="block text-[11px] text-gray-500 mb-1">Password</label>
            <div class="glow-field">
                <span class="glow-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path fill="currentColor" d="M17 8h-1V6a4 4 0 0 0-8 0v2H7a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1ZM9 6a3 3 0 0 1 6 0v2H9Zm6 12H9v-5h6Z"/></svg>
                </span>
                <input id="password" class="auth-input" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
            </div>
            @if ($errors->has('password'))
                <div class="mt-2 text-[11px] text-red-600">{{ $errors->first('password') }}</div>
            @endif
        </div>

        <div class="flex items-center justify-between">
            @if (Route::has('password.request'))
                <a class="text-[11px] font-medium auth-link" style="color:#002C76" href="{{ route('password.request') }}" data-auth-switch="forgot">Forgot password?</a>
            @endif
        </div>

        <div class="flex items-center justify-between text-[11px] text-gray-700">
            <label class="inline-flex items-center gap-2">
                <input id="remember_me" type="checkbox" name="remember" style="accent-color:#002C76" />
                <span>Remember sign in details</span>
            </label>
        </div>

        <button type="submit" class="auth-primary-btn">Log in</button>

        <div class="auth-divider">OR</div>

        <button type="button" class="auth-google-btn">
            <span class="inline-flex h-4 w-4 items-center justify-center rounded-full" aria-hidden="true">
                <svg viewBox="0 0 48 48" class="h-4 w-4">
                    <path fill="#EA4335" d="M24 9.5c3.2 0 5.4 1.4 6.6 2.5l4.5-4.5C32.5 5 28.7 3.5 24 3.5 14.6 3.5 6.7 9 3.2 16.9l5.2 4c1.2-4.8 5.6-8.4 10.6-8.4z"/>
                    <path fill="#34A853" d="M46 24.5c0-1.4-.1-2.4-.3-3.5H24v6.6h12.5c-.6 3-2.4 5.5-5.2 7.1l5.1 4c3-2.8 4.6-6.9 4.6-12.2z"/>
                    <path fill="#4285F4" d="M8.4 28.9c-.3-1-.5-2.1-.5-3.4s.2-2.4.5-3.4l-5.2-4C1.8 19.7 1 22.5 1 25.5s.8 5.8 2.2 8.4l5.2-4z"/>
                    <path fill="#FBBC05" d="M24 45c6.2 0 11.4-2 15.2-5.4l-5.1-4c-1.4 1-3.4 1.7-6.1 1.7-4.9 0-9.1-3.3-10.6-7.8l-5.2 4C14.6 41 18.9 45 24 45z"/>
                </svg>
            </span>
            Continue with Google
        </button>

        <div class="mt-4 text-center text-[11px] text-gray-700">
            Don’t have an account?
            <a href="{{ route('register') }}" class="font-semibold" style="color:#002C76" data-auth-switch="register">Sign up</a>
        </div>
    </form>
</x-guest-layout>
