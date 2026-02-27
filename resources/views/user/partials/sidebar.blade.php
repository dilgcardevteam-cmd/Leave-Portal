<aside class="hr-sidebar hidden min-h-[calc(100vh-4rem)] bg-gradient-to-b from-[#1c2a5c] to-[#17254f] p-4 text-white lg:block">
    <div class="text-center font-semibold text-white transition-all duration-200" :class="sidebarCollapsed ? 'mb-2 text-xs tracking-normal' : 'mb-3 text-lg tracking-wide'">Welcome</div>
    <div class="mb-4 flex items-center" :class="sidebarCollapsed ? 'justify-center' : 'justify-between'">
        <div x-show="!sidebarCollapsed" class="px-2 text-xs uppercase tracking-wider text-white/70">Menu</div>
        <button type="button" @click="sidebarCollapsed = !sidebarCollapsed" class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-white/10 text-white transition hover:bg-white/20 focus:outline-none focus:ring-0" aria-label="Toggle sidebar">
            <svg class="h-5 w-5 transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M4 6h16v2H4V6zm0 6h16v2H4v-2zm0 6h16v2H4v-2z"/>
            </svg>
        </button>
    </div>
    <nav class="space-y-2" aria-label="User sidebar">
        <a href="{{ route('dashboard') }}" class="flex items-center rounded-xl py-3 text-lg transition @if(request()->routeIs('dashboard')) bg-white/10 shadow-inner @else hover:bg-white/10 @endif focus:outline-none focus:ring-0" :class="sidebarCollapsed ? 'justify-center px-3' : 'gap-3 px-4'" @if(request()->routeIs('dashboard')) aria-current="page" @endif title="Dashboard">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M3 12l9-9 9 9v8a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-8z"/></svg>
            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Dashboard</span>
        </a>
        <a href="{{ route('leaves.index') }}" class="flex items-center rounded-xl py-3 text-lg transition @if(request()->routeIs('leaves.*')) bg-white/10 shadow-inner @else hover:bg-white/10 @endif focus:outline-none focus:ring-0" :class="sidebarCollapsed ? 'justify-center px-3' : 'gap-3 px-4'" @if(request()->routeIs('leaves.*')) aria-current="page" @endif title="Leave Application">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M4 6h16v2H4V6zm0 5h16v2H4v-2zm0 5h10v2H4v-2z"/></svg>
            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Leave Application</span>
        </a>
        <a href="{{ route('user.credits') }}" class="flex items-center rounded-xl py-3 text-lg transition @if(request()->routeIs('user.credits')) bg-white/10 shadow-inner @else hover:bg-white/10 @endif focus:outline-none focus:ring-0" :class="sidebarCollapsed ? 'justify-center px-3' : 'gap-3 px-4'" @if(request()->routeIs('user.credits')) aria-current="page" @endif title="Credits">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 1a11 11 0 100 22 11 11 0 000-22zm1 6h-2v2h2V7zm2 8H9v-2h4a2 2 0 000-4H9V7h4a4 4 0 010 8z"/></svg>
            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Credits</span>
        </a>
        <a href="{{ route('user.help') }}" class="flex items-center rounded-xl py-3 text-lg transition @if(request()->routeIs('user.help')) bg-white/10 shadow-inner @else hover:bg-white/10 @endif focus:outline-none focus:ring-0" :class="sidebarCollapsed ? 'justify-center px-3' : 'gap-3 px-4'" @if(request()->routeIs('user.help')) aria-current="page" @endif title="Help & Support">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 100 20 10 10 0 0 0 0-20zm1 15h-2v-2h2v2zm1.07-7.75l-.9.92A2 2 0 0 0 12 12h-1v-1c0-.55.22-1.05.59-1.41l1.2-1.2a1.5 1.5 0 1 0-2.56-1.06H8.5a3.5 3.5 0 1 1 6.57 1.72z"/></svg>
            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Help &amp; Support</span>
        </a>
    </nav>
</aside>
