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
    <nav class="space-y-2" aria-label="ARD sidebar">
        <a href="{{ route('ard.index') }}" class="flex items-center rounded-xl py-3 text-lg transition @if(request()->routeIs('ard.index')) bg-white/10 shadow-inner @else hover:bg-white/10 @endif focus:outline-none focus:ring-0" :class="sidebarCollapsed ? 'justify-center px-3' : 'gap-3 px-4'" @if(request()->routeIs('ard.index')) aria-current="page" @endif title="Dashboard">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M3 12l9-9 9 9v8a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-8z"/></svg>
            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Dashboard</span>
        </a>
        <a href="{{ route('ard.leaves') }}" class="flex items-center rounded-xl py-3 text-lg transition @if(request()->routeIs('ard.leaves*')) bg-white/10 shadow-inner @else hover:bg-white/10 @endif focus:outline-none focus:ring-0" :class="sidebarCollapsed ? 'justify-center px-3' : 'gap-3 px-4'" @if(request()->routeIs('ard.leaves*')) aria-current="page" @endif title="Leave Requests">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M3 5h18v4H3V5zm0 6h10v8H3v-8zm12 0h6v8h-6v-8z"/></svg>
            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Leave Requests</span>
        </a>
        <a href="{{ route('ard.downloads') }}" class="flex items-center rounded-xl py-3 text-lg transition @if(request()->routeIs('ard.downloads')) bg-white/10 shadow-inner @else hover:bg-white/10 @endif focus:outline-none focus:ring-0" :class="sidebarCollapsed ? 'justify-center px-3' : 'gap-3 px-4'" @if(request()->routeIs('ard.downloads')) aria-current="page" @endif title="Download">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                <path d="M4 19h16v2H4z"/>
                <path d="M11 3h2v10h3l-4 4-4-4h3z"/>
            </svg>
            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Download</span>
        </a>
        <a href="{{ route('ard.help') }}" class="flex items-center rounded-xl py-3 text-lg transition @if(request()->routeIs('ard.help')) bg-white/10 shadow-inner @else hover:bg-white/10 @endif focus:outline-none focus:ring-0" :class="sidebarCollapsed ? 'justify-center px-3' : 'gap-3 px-4'" @if(request()->routeIs('ard.help')) aria-current="page" @endif title="Help & Support">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 100 20 10 10 0 0 0 0-20zm1 15h-2v-2h2v2zm1.07-7.75l-.9.92A2 2 0 0 0 12 12h-1v-1c0-.55.22-1.05.59-1.41l1.2-1.2a1.5 1.5 0 1 0-2.56-1.06H8.5a3.5 3.5 0 1 1 6.57 1.72z"/></svg>
            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Help &amp; Support</span>
        </a>
    </nav>
</aside>
