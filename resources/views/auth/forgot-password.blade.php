<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Enter your email to receive an 8-digit OTP, then use it to reset your password.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-3">
        @csrf
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $email ?? '')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div class="flex items-center justify-end">
            <x-primary-button class="rounded-full px-5 py-2">
                {{ __('Send OTP') }}
            </x-primary-button>
        </div>
    </form>

    @php
        $otpSent = (bool) session('status');
        $otpVerifiedEmail = session('otp_verified_email');
        $canReset = $otpVerifiedEmail && $otpVerifiedEmail === old('email', $email ?? '');
        $modalEmail = old('email', $email ?? '');
        $shouldShowModal = ($otpSent && ! $canReset) || $errors->has('otp_code');
        $otpSentAt = session('otp_sent_at');
    @endphp

    <div id="otpModal" data-verified="{{ $canReset ? '1' : '0' }}" data-sent-at="{{ $otpSentAt ?? '' }}" class="{{ $shouldShowModal ? '' : 'hidden' }} fixed inset-0 z-50">
        <div class="absolute inset-0 bg-slate-900/50" data-close-modal></div>
        <div class="absolute left-1/2 top-1/2 z-10 w-full max-w-md -translate-x-1/2 -translate-y-1/2 rounded-3xl bg-white p-6 shadow-2xl">
            <div class="mb-2 text-center text-lg font-semibold text-slate-900">Enter OTP</div>
            <p class="text-center text-sm text-slate-600">Enter the 8-digit OTP sent to your email.</p>

            <form method="POST" action="{{ route('password.otp.verify') }}" class="mt-4 space-y-4">
                @csrf
                <input type="hidden" name="email" value="{{ $modalEmail }}">

                <div>
                    <x-input-label :value="__('OTP Code')" />
                    <div class="mt-2 flex flex-nowrap justify-center gap-2" id="otp-boxes">
                        @for ($i = 0; $i < 8; $i++)
                            <input
                                type="text"
                                inputmode="numeric"
                                maxlength="1"
                                class="otp-input h-12 w-10 rounded-xl border border-gray-300 text-center text-lg font-semibold shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        @endfor
                    </div>
                    <input type="hidden" name="otp_code" id="otp_code" value="{{ old('otp_code') }}">
                    <x-input-error :messages="$errors->get('otp_code')" class="mt-2" />
                    <div id="otpResentMsg" class="mt-2 hidden rounded-lg border border-green-200 bg-green-50 px-3 py-2 text-xs text-green-700">
                        OTP successfully sent.
                    </div>
                    <div class="mt-2 text-center text-xs text-slate-500">
                        OTP expires in <span id="otpTimer" class="font-semibold text-slate-700">02:00</span>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <button type="button" class="rounded-full border border-gray-200 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50" data-close-modal>
                        Cancel
                    </button>
                    <x-primary-button id="otpActionBtn" class="rounded-full px-5 py-2">
                        {{ __('Verify OTP') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>

    @if ($canReset)
    <div class="my-5 border-t border-gray-200"></div>
    <form id="resetForm" method="POST" action="{{ route('password.otp.reset') }}" class="mt-4 space-y-4">
        @csrf
        <input type="hidden" name="email" value="{{ old('email', $email ?? '') }}">
        <input type="hidden" name="otp_code" value="{{ old('otp_code') }}">

        <div>
            <x-input-label for="password" :value="__('New Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="rounded-full bg-gray-900 px-5 py-2 text-sm font-medium text-white hover:bg-gray-800">
                Reset Password
            </button>
        </div>
    </form>
    @endif

    <style>
        .login-glass .otp-input { border-radius: 0.85rem; }
    </style>
    <script>
        (function () {
            const boxes = Array.from(document.querySelectorAll('#otp-boxes .otp-input'));
            const hidden = document.getElementById('otp_code');
            if (!boxes.length || !hidden) return;
            const sync = () => {
                hidden.value = boxes.map(b => (b.value || '')).join('');
            };
            boxes.forEach((box, idx) => {
                box.addEventListener('input', (e) => {
                    const v = e.target.value.replace(/\D/g, '');
                    e.target.value = v.slice(0, 1);
                    if (v && idx < boxes.length - 1) boxes[idx + 1].focus();
                    sync();
                });
                box.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && !e.target.value && idx > 0) {
                        boxes[idx - 1].focus();
                    }
                });
                box.addEventListener('paste', (e) => {
                    const text = (e.clipboardData || window.clipboardData).getData('text') || '';
                    const digits = text.replace(/\D/g, '').slice(0, boxes.length);
                    if (!digits) return;
                    e.preventDefault();
                    digits.split('').forEach((d, i) => {
                        if (boxes[i]) boxes[i].value = d;
                    });
                    boxes[Math.min(digits.length, boxes.length) - 1].focus();
                    sync();
                });
            });
            sync();
        })();
    </script>
    <script>
        (function () {
            const modal = document.getElementById('otpModal');
            const closeBtns = document.querySelectorAll('[data-close-modal]');
            closeBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    if (modal) modal.classList.add('hidden');
                });
            });
            if (modal && modal.getAttribute('data-verified') === '1') {
                modal.classList.add('hidden');
                const resetForm = document.getElementById('resetForm');
                const pwd = document.getElementById('password');
                if (resetForm) {
                    resetForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                if (pwd) {
                    setTimeout(() => pwd.focus(), 300);
                }
            }
        })();
    </script>
    <script>
        (function () {
            const modal = document.getElementById('otpModal');
            const timerEl = document.getElementById('otpTimer');
            const actionBtn = document.getElementById('otpActionBtn');
            const boxes = Array.from(document.querySelectorAll('#otp-boxes .otp-input'));
            const hidden = document.getElementById('otp_code');
            if (!modal || !timerEl || !actionBtn || !boxes.length || !hidden) return;

            const emailInput = document.querySelector('form[action="{{ route('password.email') }}"] input[name="email"]');
            const csrf = '{{ csrf_token() }}';
            let remaining = 120;
            let ticking = null;
            let mode = 'verify';

            const format = (s) => {
                const m = String(Math.floor(s / 60)).padStart(2, '0');
                const r = String(s % 60).padStart(2, '0');
                return `${m}:${r}`;
            };
            const resetTimer = (seconds) => {
                remaining = typeof seconds === 'number' ? seconds : 120;
                timerEl.textContent = format(remaining);
                mode = 'verify';
                actionBtn.textContent = 'Verify OTP';
            };
            const resendOtp = async () => {
                const email = emailInput ? emailInput.value : '';
                if (!email) return;
                await fetch("{{ route('password.email') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ email }),
                }).catch(() => {});
                modal.setAttribute('data-sent-at', String(Math.floor(Date.now() / 1000)));
                resetTimer(120);
                const msg = document.getElementById('otpResentMsg');
                if (msg) {
                    msg.classList.remove('hidden');
                    clearTimeout(msg._t);
                    msg._t = setTimeout(() => msg.classList.add('hidden'), 4000);
                }
            };

            const start = () => {
                if (ticking) return;
                const sentAt = parseInt(modal.getAttribute('data-sent-at') || '', 10);
                if (sentAt) {
                    const now = Math.floor(Date.now() / 1000);
                    const diff = now - sentAt;
                    const left = Math.max(0, 120 - diff);
                    resetTimer(left);
                } else {
                    resetTimer(120);
                }
                if (remaining <= 0) {
                    boxes.forEach(b => { b.disabled = true; b.value = ''; });
                    hidden.value = '';
                } else {
                    boxes.forEach(b => { b.disabled = false; });
                }
                ticking = setInterval(async () => {
                    remaining -= 1;
                    if (remaining <= 0) {
                        clearInterval(ticking);
                        ticking = null;
                        timerEl.textContent = '00:00';
                        mode = 'resend';
                        actionBtn.textContent = 'Resend OTP';
                        boxes.forEach(b => {
                            b.disabled = true;
                            b.value = '';
                        });
                        hidden.value = '';
                        return;
                    }
                    timerEl.textContent = format(remaining);
                }, 1000);
            };

            actionBtn.addEventListener('click', async (e) => {
                if (mode === 'resend') {
                    e.preventDefault();
                    await resendOtp();
                    boxes.forEach(b => { b.disabled = false; });
                    start();
                }
            });

            if (!modal.classList.contains('hidden')) {
                start();
            }
        })();
    </script>
</x-guest-layout>
