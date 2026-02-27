<x-guest-layout>
    @if (session('message'))
        <div class="mb-4 text-green-600">{{ session('message') }}</div>
    @endif
    <form method="POST" action="{{ route('otp.verify') }}" class="space-y-4">
        @csrf
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" value="{{ old('email', $email) }}" required readonly />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="otp_code" :value="__('OTP Code')" />
            <x-text-input id="otp_code" class="block mt-1 w-full" type="text" inputmode="numeric" name="otp_code" placeholder="6-digit code" required autofocus />
            <x-input-error :messages="$errors->get('otp_code')" class="mt-2" />
        </div>
        <x-primary-button>Verify</x-primary-button>
    </form>
    <form method="POST" action="{{ route('otp.resend') }}" class="mt-4">
        @csrf
        <input type="hidden" name="email" value="{{ old('email', $email) }}" />
        <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900">Resend code</button>
    </form>
</x-guest-layout>
