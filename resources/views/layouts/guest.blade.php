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
        @if (request()->routeIs('login') || request()->routeIs('register'))
            <div class="min-h-screen w-full bg-white">
                <div
                    id="auth-shell"
                    class="auth-shell"
                    data-view="{{ request()->routeIs('register') ? 'register' : 'login' }}"
                >
                    <section class="auth-panel auth-logo">
                        <div class="auth-logo-inner">
                            <img src="{{ asset('dilgLogo.png') }}" alt="DILG Logo" class="auth-logo-img">
                            <div class="auth-logo-text">Leave Application System</div>
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

        @if (request()->routeIs('login') || request()->routeIs('register'))
            <style>
                .auth-shell {
                    min-height: 100vh;
                    display: flex;
                    overflow: hidden;
                }
                .auth-panel {
                    width: 50%;
                    min-height: 100vh;
                    transition: transform 600ms ease;
                }
                .auth-logo {
                    background: #0b3ea8;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .auth-logo-inner {
                    text-align: center;
                    color: #ffffff;
                }
                .auth-logo-img {
                    width: 210px;
                    height: 210px;
                    object-fit: contain;
                    margin: 0 auto 24px;
                    border-radius: 9999px;
                    box-shadow: 0 14px 32px rgba(0, 0, 0, 0.18);
                    background: #ffffff;
                    padding: 10px;
                }
                .auth-logo-text {
                    font-size: 20px;
                    font-weight: 600;
                    letter-spacing: 0.02em;
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
                .auth-shell[data-view="register"] .auth-logo {
                    transform: translateX(100%);
                }
                .auth-shell[data-view="register"] .auth-form {
                    transform: translateX(-100%);
                }
                .auth-input {
                    width: 100%;
                    height: 42px;
                    border: 1px solid #e3e6ee;
                    border-radius: 10px;
                    padding: 0 14px;
                    font-size: 13px;
                    color: #111827;
                    background: #ffffff;
                }
                .auth-input:focus {
                    outline: none;
                    border-color: #7c66ff;
                    box-shadow: 0 0 0 3px rgba(124, 102, 255, 0.2);
                }
                .auth-primary-btn {
                    width: 100%;
                    height: 42px;
                    border-radius: 999px;
                    background: #6c5ce7;
                    color: #ffffff;
                    font-weight: 600;
                    font-size: 13px;
                    transition: background 200ms ease;
                }
                .auth-primary-btn:hover {
                    background: #5a4bdc;
                }
                .auth-google-btn {
                    width: 100%;
                    height: 40px;
                    border-radius: 999px;
                    border: 1px solid #e5e7eb;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    gap: 10px;
                    font-size: 12px;
                    font-weight: 600;
                    color: #4b5563;
                    background: #f5f6fa;
                }
                .auth-divider {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    color: #9ca3af;
                    font-size: 11px;
                    margin: 16px 0;
                }
                .auth-divider::before,
                .auth-divider::after {
                    content: "";
                    height: 1px;
                    background: #e5e7eb;
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
                    background: #d6d9e5;
                    border-radius: 999px;
                }
                @media (max-width: 1023px) {
                    .auth-shell {
                        flex-direction: column;
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
                                history.pushState({}, '', href);
                                bindSwitchLinks();
                            } else {
                                window.location.href = href;
                            }
                        } catch (e) {
                            window.location.href = href;
                        } finally {
                            setTimeout(() => { transitioning = false; }, 100);
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
                    const currentHref = window.location.pathname.includes('register') ? registerHref : loginHref;
                    const prefetchHref = currentHref === loginHref ? registerHref : loginHref;

                    prefetchView(prefetchHref);

                    bindSwitchLinks();

                    window.addEventListener('popstate', () => {
                        if (!shell) return;
                        const url = window.location.href;
                        const target = url.includes('/register') ? 'register' : 'login';
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
