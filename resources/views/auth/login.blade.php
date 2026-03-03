<x-guest-layout>
    <div class="text-center">
        <h1 class="text-[20px] font-semibold text-gray-900">Welcome Back</h1>
        <p class="mt-1 text-[11px] text-gray-500">
            Build your design system effortlessly with our
            <br>powerful component library.
        </p>
    </div>

    <x-auth-session-status class="mt-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-[11px] text-gray-500 mb-1">Email</label>
            <input id="email" class="auth-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="alex.jordan@gmail.com">
            @if ($errors->has('email'))
                <div class="mt-2 text-[11px] text-red-600">{{ $errors->first('email') }}</div>
            @endif
        </div>

        <div>
            <label for="password" class="block text-[11px] text-gray-500 mb-1">Password</label>
            <input id="password" class="auth-input" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
            @if ($errors->has('password'))
                <div class="mt-2 text-[11px] text-red-600">{{ $errors->first('password') }}</div>
            @endif
        </div>

        <div class="flex items-center justify-between">
            @if (Route::has('password.request'))
                <a class="text-[11px] text-[#6c5ce7] font-medium" href="{{ route('password.request') }}">Forgot password?</a>
            @endif
        </div>

        <div class="flex items-center justify-between text-[11px] text-gray-500">
            <span>Remember sign in details</span>
            <label class="relative inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember" class="sr-only peer">
                <div class="h-[18px] w-[34px] rounded-full bg-[#e5e7eb] peer-checked:bg-[#6c5ce7] transition relative">
                    <span class="absolute left-[2px] top-[2px] h-[14px] w-[14px] rounded-full bg-white transition peer-checked:translate-x-[16px]"></span>
                </div>
            </label>
        </div>

        <button type="submit" class="auth-primary-btn">Log in</button>

        <div class="auth-divider">OR</div>

        <button type="button" class="auth-google-btn">
            <span class="inline-flex h-4 w-4 items-center justify-center rounded-full bg-white">
                <svg viewBox="0 0 48 48" class="h-4 w-4">
                    <path fill="#EA4335" d="M24 9.5c3.2 0 5.4 1.4 6.6 2.5l4.5-4.5C32.5 5 28.7 3.5 24 3.5 14.6 3.5 6.7 9 3.2 16.9l5.2 4c1.2-4.8 5.6-8.4 10.6-8.4z"/>
                    <path fill="#34A853" d="M46 24.5c0-1.4-.1-2.4-.3-3.5H24v6.6h12.5c-.6 3-2.4 5.5-5.2 7.1l5.1 4c3-2.8 4.6-6.9 4.6-12.2z"/>
                    <path fill="#4A90E2" d="M8.4 28.9c-.3-1-.5-2.1-.5-3.4s.2-2.4.5-3.4l-5.2-4C1.8 19.7 1 22.5 1 25.5s.8 5.8 2.2 8.4l5.2-4z"/>
                    <path fill="#FBBC05" d="M24 45c6.2 0 11.4-2 15.2-5.4l-5.1-4c-1.4 1-3.4 1.7-6.1 1.7-4.9 0-9.1-3.3-10.6-7.8l-5.2 4C14.6 41 18.9 45 24 45z"/>
                </svg>
            </span>
            Continue with Google
        </button>

        <div class="mt-4 text-center text-[11px] text-gray-500">
            Don’t have an account?
            <a href="{{ route('register') }}" class="text-[#6c5ce7] font-semibold" data-auth-switch="register">Sign up</a>
        </div>
    </form>
</x-guest-layout>
