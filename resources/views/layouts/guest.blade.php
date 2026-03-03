<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="text-gray-900 antialiased bg-white" style="font-family: 'Poppins', sans-serif;">
        @if (request()->routeIs('login') || request()->routeIs('register') || request()->routeIs('password.request'))
            <div class="min-h-screen w-full bg-white">
                <div
                    id="auth-shell"
                    class="auth-shell"
                    data-view="{{ request()->routeIs('register') ? 'register' : 'login' }}"
                >
                    <section class="auth-panel auth-logo">
                        <div class="auth-logo-inner">
                            <img src="{{ asset('dilgLogo.png') }}" alt="DILG Logo" class="brand-logo">
                            <div class="greet-wrap">
                                <!-- <div class="greet-eyebrow">Welcome</div> -->
                                <h2 class="greet-title">Leave Application System</h2>
                                <p class="greet-sub">Manage your leaves with a modern, friendly experience.</p>
                            </div>
                        </div>
                    </section>
                    <section class="auth-panel auth-form">
                        <div class="auth-form-inner">
                            {{ $slot }}
                        </div>
                    </section>
                </div>
            </div>
        @else
            <div class="min-h-screen bg-gray-50 px-4 py-6 sm:px-6 lg:px-8">
                <main id="main" class="mx-auto max-w-md">
                    <div class="rounded-2xl bg-white p-6 shadow-lg">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        @endif

        @if (request()->routeIs('login') || request()->routeIs('register') || request()->routeIs('password.request'))
            <style>
                .auth-shell {
                    min-height: 80vh;
                    display: flex;
                    overflow: hidden;
                    width: min(100%, 880px);
                    margin: 24px auto;
                    border-radius: 18px;
                    box-shadow: 0 24px 64px rgba(0, 44, 118, 0.35), 0 8px 24px rgba(0, 44, 118, 0.25);
                    border: none;
                    background: #fff;
                }
                .auth-panel {
                    width: 50%;
                    min-height: 560px;
                    transition: transform 600ms ease;
                }
                .auth-logo {
                    background: #002C76;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    
                }
               .auth-logo-inner {
    display: flex;
    flex-direction: column;
    justify-content: center;   /* vertical center */
    align-items: center;       /* horizontal center */
    text-align: center;        /* center text */
    color: #ffffff;
    padding: 36px;
    height: 100%;
}
                .brand-logo{
                    width: 300px;
                    height: 300px;
                    object-fit: contain;
                    display: block;
                    background: #ffffff;
                    border-radius: 999px;
                    /* padding: 8px; */
                    box-shadow: 0 6px 18px rgba(0,0,0,.18);
                    margin-bottom: 18px;
                }
                .greet-wrap {
                    max-width: 360px;
                }
                .greet-eyebrow {
                    font-size: 12px;
                    letter-spacing: 0.12em;
                    text-transform: uppercase;
                    opacity: .9;
                }
                .greet-title {
                    font-size: 25px;
                    font-weight: 700;
                    margin-top: 6px;
                    margin-bottom: 10px;
                    text-shadow: 0 4px 18px rgba(0,0,0,.15);
                }
                .greet-sub {
                    font-size: 13px;
                    color: #ffffff;
                    opacity: 1;
                }
                .auth-form {
                    background: #ffffff;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .auth-form-inner {
                    width: 360px;
                }
                .auth-shell[data-view="register"] .auth-form-inner {
                    width: 400px;
                    max-height: 520px;
                    overflow-y: auto;
                    padding: 0 12px;
                }
                .auth-shell[data-view="register"] .auth-logo {
                    transform: translateX(100%);
                }
                .auth-shell[data-view="register"] .auth-form {
                    transform: translateX(-100%);
                }
                .glow-field { position: relative; }
                .glow-icon {
                    position: absolute;
                    left: 14px;
                    top: 50%;
                    transform: translateY(-50%);
                    width: 18px; height: 18px;
                    color: #FFDE15;
                    opacity: 1;
                }
                .auth-input {
                    width: 100%;
                    height: 48px;
                    border-radius: 999px;
                    padding: 0 16px;
                    font-size: 13px;
                    color: #111827;
                    background: #ffffff;
                    border: 2px solid #002C76;
                    transition: border-color .2s ease, background .2s ease;
                }
                .auth-shell[data-view="register"] .auth-form-inner .auth-input {
                    height: 40px;
                    font-size: 12px;
                    padding: 0 14px;
                }
                .auth-shell[data-view="register"] .auth-form-inner .glow-icon {
                    width: 16px; height: 16px;
                    left: 12px;
                }
                .glow-field .auth-input { padding-left: 44px; }
                .auth-input:focus {
                    outline: none;
                    border-color: #002C76;
                    box-shadow: none;
                }
                .auth-primary-btn {
                    width: 100%;
                    height: 46px;
                    border-radius: 999px;
                    background: #002C76;
                    color: #ffffff;
                    font-weight: 600;
                    font-size: 13px;
                    transition: filter 200ms ease, transform 120ms ease;
                }
                .auth-primary-btn:hover {
                    filter: brightness(0.95);
                }
                .auth-primary-btn:active { transform: translateY(1px); }
                .auth-google-btn {
                    width: 100%;
                    height: 40px;
                    border-radius: 999px;
                    border: 1px solid #002C76;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    gap: 10px;
                    font-size: 12px;
                    font-weight: 600;
                    color: #002C76;
                    background: #ffffff;
                }
                .auth-divider {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    color: #002C76;
                    font-size: 11px;
                    margin: 16px 0;
                }
                .auth-divider::before,
                .auth-divider::after {
                    content: "";
                    height: 1px;
                    background: #002C76;
                    flex: 1;
                }
                .auth-scroll {
                    max-height: 540px;
                    overflow-y: auto;
                    padding-right: 6px;
                }
                .auth-scroll::-webkit-scrollbar {
                    width: 6px;
                }
                .auth-scroll::-webkit-scrollbar-thumb {
                    background: #002C76;
                    border-radius: 999px;
                }
                .auth-link{
                    text-decoration: none;
                    transition: color .2s ease-in-out, opacity .2s ease-in-out, transform .08s ease-in-out;
                }
                .auth-link:hover{ text-decoration: underline; }
                .auth-link:active{ transform: scale(0.98); opacity: .85; }
                .auth-form-inner.auth-switch-out{
                    opacity: 0;
                    transform: translateY(8px);
                    transition: opacity .35s ease-in-out, transform .35s ease-in-out;
                }
                .auth-form-inner.auth-switch-pre{
                    opacity: 0;
                    transform: translateY(8px);
                }
                .auth-form-inner.auth-switch-in{
                    opacity: 1;
                    transform: translateY(0);
                    transition: opacity .35s ease-in-out, transform .35s ease-in-out;
                }
                @media (max-width: 1023px) {
                    .auth-shell {
                        flex-direction: column;
                        width: 100%;
                        margin: 0;
                        border-radius: 0;
                        box-shadow: none;
                        border: none;
                    }
                    .auth-panel {
                        width: 100%;
                        min-height: auto;
                        transform: none !important;
                    }
                    .auth-logo {
                        padding: 36px 24px;
                    }
                    .auth-form {
                        padding: 36px 24px 48px;
                    }
                    .auth-form-inner {
                        width: 100%;
                        max-width: 420px;
                    }
                }
            </style>
            <script>
                (function () {
                    const shell = document.getElementById('auth-shell');
                    const formInner = shell ? shell.querySelector('.auth-form-inner') : null;
                    const prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                    let transitioning = false;

                    const viewCache = new Map();

                    async function prefetchView(href) {
                        if (viewCache.has(href)) return;
                        try {
                            const res = await fetch(href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                            const html = await res.text();
                            viewCache.set(href, html);
                        } catch (e) {}
                    }

                    async function swapAuthView(target, href) {
                        if (!shell || !formInner) return;
                        if (transitioning) return;
                        transitioning = true;
                        shell.dataset.view = target;

                        try {
                            if (!prefersReduced) {
                                formInner.classList.remove('auth-switch-in');
                                formInner.classList.add('auth-switch-out');
                                formInner.setAttribute('aria-busy','true');
                                await new Promise(r => setTimeout(r, 160));
                            }
                            let html = viewCache.get(href);
                            if (!html) {
                                const res = await fetch(href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                                html = await res.text();
                                viewCache.set(href, html);
                            }
                            const doc = new DOMParser().parseFromString(html, 'text/html');
                            const nextShell = doc.getElementById('auth-shell');
                            const nextInner = nextShell ? nextShell.querySelector('.auth-form-inner') : null;
                            if (nextInner) {
                                formInner.innerHTML = nextInner.innerHTML;
                                if (!prefersReduced) {
                                    formInner.classList.remove('auth-switch-out');
                                    formInner.classList.add('auth-switch-pre');
                                    requestAnimationFrame(() => {
                                        formInner.classList.remove('auth-switch-pre');
                                        formInner.classList.add('auth-switch-in');
                                    });
                                }
                                history.pushState({}, '', href);
                                bindSwitchLinks();
                            } else {
                                window.location.href = href;
                            }
                        } catch (e) {
                            window.location.href = href;
                        } finally {
                            setTimeout(() => {
                                formInner.removeAttribute('aria-busy');
                                transitioning = false;
                            }, 350);
                        }
                    }

                    function bindSwitchLinks() {
                        document.querySelectorAll('[data-auth-switch]').forEach((link) => {
                            link.addEventListener('click', (event) => {
                                if (!shell || prefersReduced) return;
                                const target = link.getAttribute('data-auth-switch');
                                const href = link.getAttribute('href');
                                if (!target || !href || shell.dataset.view === target) return;
                                event.preventDefault();
                                swapAuthView(target, href);
                            }, { once: true });
                        });
                    }

                    const loginHref = "{{ route('login') }}";
                    const registerHref = "{{ route('register') }}";
                    const forgotHref = "{{ route('password.request') }}";
                    const currentHref = window.location.pathname.includes('register')
                        ? registerHref
                        : (window.location.pathname.includes('forgot-password') ? forgotHref : loginHref);
                    [loginHref, registerHref, forgotHref].forEach(prefetchView);

                    bindSwitchLinks();

                    window.addEventListener('popstate', () => {
                        if (!shell) return;
                        const url = window.location.href;
                        const target = url.includes('/register') ? 'register' : (url.includes('/forgot-password') ? 'forgot' : 'login');
                        swapAuthView(target, url);
                    });

                    try {
                        for (let i = localStorage.length - 1; i >= 0; i--) {
                            const key = localStorage.key(i);
                            if (key && key.indexOf('leaveCreateDraft:') === 0) {
                                localStorage.removeItem(key);
                            }
                        }
                    } catch (e) {}
                })();
            </script>
        @endif
    </body>
    </html>
