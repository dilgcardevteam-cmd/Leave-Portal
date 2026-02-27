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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased {{ request()->routeIs('login') ? 'overflow-hidden' : 'bg-gray-50' }}">
        @if (request()->routeIs('login') || request()->routeIs('register'))
            <div class="fixed inset-0 z-0 pointer-events-none">
                <video class="w-screen h-screen object-cover block" autoplay muted loop playsinline preload="auto">
                    <source src="{{ asset('Free 4k  motion background - 3D  hexagon pistons - futuristic background video - Motion stock (1080p, h264).mp4') }}" type="video/mp4">
                </video>
                <div class="absolute inset-0 bg-black/45"></div>
            </div>
        @endif
        <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 px-3 py-2 bg-indigo-600 text-white rounded">Skip to content</a>
        @if (request()->routeIs('login') || request()->routeIs('register') || request()->routeIs('password.request'))
            <header class="relative z-20 bg-white/90 backdrop-blur-md border-b border-gray-200">
                <div class="max-w-7xl mx-auto h-16 px-6 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <img src="{{ asset('logo.png') }}" alt="System Logo" class="h-10 w-10 object-contain">
                        <div class="text-lg font-semibold text-gray-800">Leave Application System</div>
                    </div>
                </div>
            </header>
        @endif
        <div class="relative z-10 min-h-screen max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 {{ request()->routeIs('password.request') ? 'pt-3 pb-6' : 'py-6' }} grid grid-cols-1 lg:grid-cols-2 gap-6 items-center">
            <main id="main" class="order-2 lg:order-1 flex justify-center">
                <div class="{{ (request()->routeIs('login') || request()->routeIs('register')) ? 'login-glass' : '' }} max-w-md w-full bg-gray-100/90 backdrop-blur-md border border-gray-200/80 shadow-2xl rounded-3xl p-6 sm:p-8 text-gray-900">
                    {{ $slot }}
                </div>
            </main>
            <aside class="order-1 lg:order-2 flex items-center justify-center">
                <div class="{{ (request()->routeIs('login') || request()->routeIs('register')) ? 'bg-gray-100/90 backdrop-blur-md border border-gray-200/80 rounded-3xl shadow-2xl p-6' : '' }} text-center text-gray-900">
                    <img src="{{ asset('logo.png') }}" alt="Leave Application System" class="mx-auto drop-shadow" style="max-width:180px;height:auto;">
                    <div class="mt-4 font-semibold tracking-wide">Leave Application System</div>
                    <div class="mt-6">
                        @if (request()->routeIs('register'))
                            <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-2 border border-blue-900 text-blue-900 rounded hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-700 transition">Log In</a>
                        @elseif (request()->routeIs('login'))
                            @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-2 rounded-full border border-gray-300 bg-white text-gray-900 hover:bg-gray-50 shadow focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-700 transition">Create Account</a>
                            @endif
                        @endif
                    </div>
                </div>
            </aside>
        </div>
        @if (request()->routeIs('login') || request()->routeIs('register'))
        <style>
            .login-glass input[type="email"],
            .login-glass input[type="password"],
            .login-glass input[type="text"]{border-radius:9999px;padding-top:.6rem;padding-bottom:.6rem}
            .login-glass label{color:#111827}
            .login-glass .text-gray-600{color:#111827}
        </style>
        <script>
            (function(){
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
