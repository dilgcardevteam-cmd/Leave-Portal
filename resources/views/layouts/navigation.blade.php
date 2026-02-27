<nav x-data="{ open: false }" class="las-header bg-white border-b border-gray-100 z-10">
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 px-3 py-2 bg-indigo-600 text-white rounded">Skip to content</a>
    @if (request()->routeIs('admin.*') || request()->routeIs('categories.*') || request()->routeIs('hr.*') || request()->routeIs('rd.*') || request()->routeIs('ard.*'))
    <div class="w-full">
        <div class="h-16 px-6 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <img src="{{ asset('logo.png') }}" alt="System Logo" class="h-11 w-11 object-contain">
                <div class="text-lg font-semibold text-gray-800">Leave Application System</div>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('notifications.index') }}" class="relative inline-flex items-center justify-center h-10 w-10 rounded-full text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition" aria-label="Notifications">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 22zm6-6v-5a6 6 0 1 0-12 0v5l-2 2v1h18v-1l-2-2z"/></svg>
                    <span id="notifDot" class="absolute top-0 right-0 translate-x-1/3 -translate-y-1/3 flex items-center justify-center h-5 min-w-[1.25rem] px-1 rounded-full bg-red-600 text-white text-[10px] font-semibold ring-2 ring-white shadow hidden"></span>
                </a>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 p-1 rounded-full hover:bg-gray-100 transition" aria-label="User menu">
                            <div class="h-10 w-10 rounded-full bg-[#0d3b66] text-white flex items-center justify-center shadow-sm">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="M12 12c2.761 0 5-2.686 5-6s-2.239-6-5-6-5 2.686-5 6 2.239 6 5 6zm0 2c-4.418 0-12 2.239-12 6v2h24v-2c0-3.761-7.582-6-12-6z"/>
                                </svg>
                            </div>
                            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-gray-100 text-gray-500">
                                <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 011.08 1.04l-4.24 4.64a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                            </span>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <div class="text-sm font-medium text-gray-900">{{ Auth::user()->display_name }}</div>
                            <div class="text-xs text-gray-500">{{ ucfirst(Auth::user()->role) }}</div>
                        </div>
                        <x-dropdown-link href="{{ route('profile.edit') }}">
                            Settings &bull; Edit Profile
                        </x-dropdown-link>
                        <x-dropdown-link href="#">
                            Help &amp; Support
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                                Logout
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>
    @else
    <div class="w-full">
        <div class="h-16 px-6 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <img src="{{ asset('logo.png') }}" alt="System Logo" class="h-11 w-11 object-contain">
                <div class="text-lg font-semibold text-gray-800">Leave Application System</div>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('notifications.index') }}" class="relative inline-flex items-center justify-center h-10 w-10 rounded-full text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition" aria-label="Notifications">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 22zm6-6v-5a6 6 0 1 0-12 0v5l-2 2v1h18v-1l-2-2z"/></svg>
                    <span id="notifDot" class="absolute top-0 right-0 translate-x-1/3 -translate-y-1/3 flex items-center justify-center h-5 min-w-[1.25rem] px-1 rounded-full bg-red-600 text-white text-[10px] font-semibold ring-2 ring-white shadow hidden"></span>
                </a>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 p-1 rounded-full hover:bg-gray-100 transition" aria-label="User menu">
                            <div class="h-10 w-10 rounded-full bg-[#0d3b66] text-white flex items-center justify-center shadow-sm">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="M12 12c2.761 0 5-2.686 5-6s-2.239-6-5-6-5 2.686-5 6 2.239 6 5 6zm0 2c-4.418 0-12 2.239-12 6v2h24v-2c0-3.761-7.582-6-12-6z"/>
                                </svg>
                            </div>
                            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-gray-100 text-gray-500">
                                <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 011.08 1.04l-4.24 4.64a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                            </span>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <div class="text-sm font-medium text-gray-900">{{ Auth::user()->display_name }}</div>
                            <div class="text-xs text-gray-500">{{ ucfirst(Auth::user()->role) }}</div>
                        </div>
                        <x-dropdown-link href="{{ route('profile.edit') }}">
                            Settings &bull; Edit Profile
                        </x-dropdown-link>
                        <x-dropdown-link href="#">
                            Help &amp; Support
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                                Logout
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
                </div>
            </div>
        </div>
    </div>
    @endif
    @auth
    <script>
        (function () {
            if (window.LAS_NOTIFS_DOT_INIT) return;
            window.LAS_NOTIFS_DOT_INIT = true;
            const dot = document.getElementById('notifDot');
            if (!dot) return;
            const tick = () => {
                fetch('{{ route('notifications.unread-count') }}', {headers: {'X-Requested-With':'XMLHttpRequest'}})
                    .then(r => r.json()).then(data => {
                        const c = data.count || 0;
                        if (c === 0) { dot.classList.add('hidden'); dot.textContent=''; dot.classList.remove('animate-pulse'); }
                        else { dot.classList.remove('hidden'); dot.textContent = c > 9 ? '9+' : String(c); dot.classList.add('animate-pulse'); }
                    }).catch(()=>{});
            };
            tick(); setInterval(tick, 15000);
        })();
    </script>
    @endauth
