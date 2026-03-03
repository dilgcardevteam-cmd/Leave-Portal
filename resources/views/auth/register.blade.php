<x-guest-layout>
    <div class="text-center">
        <h1 class="text-[22px] font-semibold text-gray-900">Hello, friend!</h1>
        <p class="mt-1 text-[11px] text-gray-500">Create your account to get started.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
        @csrf

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="first_name" class="block text-[11px] text-gray-500 mb-1">First Name</label>
                <input id="first_name" class="auth-input" type="text" name="first_name" value="{{ old('first_name') }}" required autofocus>
                @if ($errors->has('first_name'))
                    <div class="mt-2 text-[11px] text-red-600">{{ $errors->first('first_name') }}</div>
                @endif
            </div>

            <div>
                <label for="last_name" class="block text-[11px] text-gray-500 mb-1">Last Name</label>
                <input id="last_name" class="auth-input" type="text" name="last_name" value="{{ old('last_name') }}" required>
                @if ($errors->has('last_name'))
                    <div class="mt-2 text-[11px] text-red-600">{{ $errors->first('last_name') }}</div>
                @endif
            </div>

            <div>
                <label for="middle_name" class="block text-[11px] text-gray-500 mb-1">Middle Name (Opt)</label>
                <input id="middle_name" class="auth-input" type="text" name="middle_name" value="{{ old('middle_name') }}">
                @if ($errors->has('middle_name'))
                    <div class="mt-2 text-[11px] text-red-600">{{ $errors->first('middle_name') }}</div>
                @endif
            </div>

            <div>
                <label for="mobile_number" class="block text-[11px] text-gray-500 mb-1">Mobile Number</label>
                <input id="mobile_number" class="auth-input" type="text" name="mobile_number" value="{{ old('mobile_number') }}">
                @if ($errors->has('mobile_number'))
                    <div class="mt-2 text-[11px] text-red-600">{{ $errors->first('mobile_number') }}</div>
                @endif
            </div>

            <div>
                <label for="sex" class="block text-[11px] text-gray-500 mb-1">Sex</label>
                <select id="sex" name="sex" class="auth-input">
                    <option value="">Select</option>
                    <option value="Male" @selected(old('sex') === 'Male')>Male</option>
                    <option value="Female" @selected(old('sex') === 'Female')>Female</option>
                </select>
                @if ($errors->has('sex'))
                    <div class="mt-2 text-[11px] text-red-600">{{ $errors->first('sex') }}</div>
                @endif
            </div>

            <div>
                <label for="id_no" class="block text-[11px] text-gray-500 mb-1">ID No.</label>
                <input id="id_no" class="auth-input" type="text" name="id_no" value="{{ old('id_no') }}">
                @if ($errors->has('id_no'))
                    <div class="mt-2 text-[11px] text-red-600">{{ $errors->first('id_no') }}</div>
                @endif
            </div>

            <div>
                <label for="region" class="block text-[11px] text-gray-500 mb-1">Region</label>
                <input id="region" class="auth-input" type="text" name="region" value="{{ old('region') }}">
                @if ($errors->has('region'))
                    <div class="mt-2 text-[11px] text-red-600">{{ $errors->first('region') }}</div>
                @endif
            </div>

            <div>
                <label for="province_office" class="block text-[11px] text-gray-500 mb-1">Office/Department</label>
                <input id="province_office" class="auth-input" type="text" name="province_office" value="{{ old('province_office') }}">
                @if ($errors->has('province_office'))
                    <div class="mt-2 text-[11px] text-red-600">{{ $errors->first('province_office') }}</div>
                @endif
            </div>

            <div>
                <label for="position" class="block text-[11px] text-gray-500 mb-1">Position</label>
                <input id="position" class="auth-input" type="text" name="position" value="{{ old('position') }}">
                @if ($errors->has('position'))
                    <div class="mt-2 text-[11px] text-red-600">{{ $errors->first('position') }}</div>
                @endif
            </div>

            <div class="col-span-2">
                <label for="email" class="block text-[11px] text-gray-500 mb-1">Email Address</label>
                <div class="glow-field">
                    <span class="glow-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path fill="currentColor" d="M2 6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v.2l-10 6.25L2 6.2V6Zm0 2.6V18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8.6l-9.3 5.8a2 2 0 0 1-2.1 0L2 8.6Z"/></svg>
                    </span>
                    <input id="email" class="auth-input" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
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
                    <input id="password" class="auth-input" type="password" name="password" required autocomplete="new-password">
                </div>
                @if ($errors->has('password'))
                    <div class="mt-2 text-[11px] text-red-600">{{ $errors->first('password') }}</div>
                @endif
            </div>

            <div>
                <label for="password_confirmation" class="block text-[11px] text-gray-500 mb-1">Confirm Password</label>
                <div class="glow-field">
                    <span class="glow-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path fill="currentColor" d="M17 8h-1V6a4 4 0 0 0-8 0v2H7a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1ZM9 6a3 3 0 0 1 6 0v2H9Zm6 12H9v-5h6Z"/></svg>
                    </span>
                    <input id="password_confirmation" class="auth-input" type="password" name="password_confirmation" required autocomplete="new-password">
                </div>
                @if ($errors->has('password_confirmation'))
                    <div class="mt-2 text-[11px] text-red-600">{{ $errors->first('password_confirmation') }}</div>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-2 text-[11px] text-gray-700">
            <input id="agree_terms" type="checkbox" name="agree_terms" class="h-3.5 w-3.5 rounded" style="accent-color:#002C76">
            <label for="agree_terms">I’ve read and agree to <a href="#" class="font-semibold" style="color:#002C76">Terms &amp; Conditions</a></label>
        </div>

        <button type="submit" class="auth-primary-btn">Create account</button>

        <div class="mt-4 text-center text-[11px] text-gray-700">
            Already have an account?
            <a href="{{ route('login') }}" class="font-semibold" style="color:#002C76" data-auth-switch="login">Log in</a>
        </div>
    </form>
</x-guest-layout>
