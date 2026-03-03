<x-guest-layout>
    <div class="text-center">
        <h1 class="text-[22px] font-semibold text-gray-900">Hello, friend!</h1>
        <p class="mt-1 text-[11px] text-gray-500">Enter your email to receive an 8‑digit OTP, then use it to reset your password.</p>
    </div>

    <x-auth-session-status class="mt-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label for="email" class="block text-[11px] text-gray-500 mb-1">Email</label>
            <div class="glow-field">
                <span class="glow-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path fill="currentColor" d="M2 6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v.2l-10 6.25L2 6.2V6Zm0 2.6V18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8.6l-9.3 5.8a2 2 0 0 1-2.1 0L2 8.6Z"/></svg>
                </span>
                <input id="email" class="auth-input" type="email" name="email" value="{{ old('email', $email ?? '') }}" required autofocus autocomplete="username" placeholder="you@example.com">
            </div>
            @if ($errors->has('email'))
                <div class="mt-2 text-[11px] text-red-600">{{ $errors->first('email') }}</div>
            @endif
        </div>

        <button type="submit" class="auth-primary-btn">Send OTP</button>

        <div class="mt-2 text-center text-[11px] text-gray-700">
            <a href="{{ route('login') }}" class="font-semibold auth-link" style="color:#002C76" data-auth-switch="login">Back to login</a>
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
        <div class="absolute inset-0 bg-slate-900/50 modal-overlay" data-close-modal></div>
        <div class="absolute left-1/2 top-1/2 z-10 w-full max-w-md -translate-x-1/2 -translate-y-1/2 rounded-3xl bg-white p-6 shadow-2xl modal-panel" role="dialog" aria-modal="true" aria-labelledby="otpTitle">
            <div id="otpTitle" class="mb-2 text-center text-lg font-semibold text-slate-900">Enter OTP</div>
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
                    <div id="otpErrorMsg" class="mt-2 hidden rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-700" role="alert" aria-live="polite">
                        Failed to send OTP. Please try again.
                        <button id="otpRetryBtn" type="button" class="ml-2 underline font-semibold" style="color:#002C76">Retry</button>
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
        .modal-overlay{ opacity: 0; transition: opacity 320ms ease-in-out; }
        #otpModal:not(.hidden) .modal-overlay{ opacity: 1; }
        .modal-panel{ opacity: 0; transform: translate(-50%, calc(-50% + 14px)) scale(.995); transition: opacity 320ms cubic-bezier(.22,.61,.36,1), transform 320ms cubic-bezier(.22,.61,.36,1); will-change: opacity, transform; }
        #otpModal:not(.hidden) .modal-panel{ opacity: 1; transform: translate(-50%, -50%) scale(1); }
        .otp-input{ transition: border-color 200ms ease, box-shadow 200ms ease, transform 120ms ease; }
        .otp-input:focus{ outline: none; border-color: #002C76; box-shadow: 0 0 0 2px rgba(0,44,118,.25); transform: scale(1.02); }
        @media (prefers-reduced-motion: reduce){
            .modal-overlay, .modal-panel, .otp-input{ transition: none; animation: none; }
        }
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
            if (!modal) return;
            const prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            const show = () => {
                modal.classList.remove('hidden');
                if (prefersReduced) return;
                requestAnimationFrame(() => {});
            };
            const hide = () => {
                if (prefersReduced) { modal.classList.add('hidden'); return; }
                const panel = modal.querySelector('.modal-panel');
                const overlay = modal.querySelector('.modal-overlay');
                if (panel) panel.style.transitionDuration = '320ms';
                if (overlay) overlay.style.transitionDuration = '320ms';
                modal.classList.add('hiding');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('hiding');
                }, 320);
            };
            document.querySelectorAll('[data-close-modal]').forEach(btn => btn.addEventListener('click', hide));
            window.otpModal = { show, hide, el: modal };
            if (modal.getAttribute('data-verified') === '1') {
                hide();
                const resetForm = document.getElementById('resetForm');
                const pwd = document.getElementById('password');
                if (resetForm) resetForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
                if (pwd) setTimeout(() => pwd.focus(), 300);
            } else if (!modal.classList.contains('hidden')) {
                show();
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
            const resentMsg = document.getElementById('otpResentMsg');
            const errorMsg = document.getElementById('otpErrorMsg');
            const retryBtn = document.getElementById('otpRetryBtn');
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
            // Intercept email submit: open modal instantly, send in background, show error only on failure
            const sendForm = document.querySelector('form[action="{{ route('password.email') }}"]');
            if (sendForm) {
                const trySend = async () => {
                    const email = emailInput ? emailInput.value : '';
                    try {
                        const res = await fetch("{{ route('password.email') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ email }),
                        });
                        if (!res.ok) throw new Error('Failed');
                        if (resentMsg) {
                            resentMsg.classList.remove('hidden');
                            clearTimeout(resentMsg._t);
                            resentMsg._t = setTimeout(() => resentMsg.classList.add('hidden'), 4000);
                        }
                        if (errorMsg) errorMsg.classList.add('hidden');
                    } catch (_) {
                        if (errorMsg) errorMsg.classList.remove('hidden');
                    }
                };
                sendForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    if (errorMsg) errorMsg.classList.add('hidden');
                    if (window.otpModal) window.otpModal.show();
                    const hiddenEmailInModal = document.querySelector('#otpModal input[name="email"]');
                    if (hiddenEmailInModal && emailInput) hiddenEmailInModal.value = emailInput.value;
                    modal.setAttribute('data-sent-at', String(Math.floor(Date.now() / 1000)));
                    boxes.forEach(b => { b.disabled = false; b.value = ''; });
                    if (boxes[0]) setTimeout(() => boxes[0].focus(), 120);
                    start();
                    await trySend();
                    if (retryBtn) retryBtn.onclick = async () => {
                        if (errorMsg) errorMsg.classList.add('hidden');
                        await trySend();
                    };
                }, { once: true });
            }
        })();
    </script>
</x-guest-layout>
