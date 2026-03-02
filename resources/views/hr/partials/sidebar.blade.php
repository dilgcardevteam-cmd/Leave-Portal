<aside class="hr-sidebar hidden min-h-[calc(100vh-4rem)] bg-gradient-to-b from-[#1c2a5c] to-[#17254f] p-4 text-white lg:block">
    <div class="text-center font-semibold text-white transition-all duration-200" :class="sidebarCollapsed ? 'mb-2 text-xs tracking-normal' : 'mb-3 text-lg tracking-wide'">Welcome</div>
    <div class="mb-4 flex items-center" :class="sidebarCollapsed ? 'justify-center' : 'justify-between'">
        <div x-show="!sidebarCollapsed" class="px-2 text-xs uppercase tracking-wider text-white/70"></div>
        <button type="button" @click="sidebarCollapsed = !sidebarCollapsed" class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-white/10 text-white transition hover:bg-white/20 focus:outline-none focus:ring-0" aria-label="Toggle sidebar">
            <svg class="h-5 w-5 transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M4 6h16v2H4V6zm0 6h16v2H4v-2zm0 6h16v2H4v-2z"/>
            </svg>
        </button>
    </div>
    <nav class="space-y-2" aria-label="HR sidebar">
        <a href="{{ route('hr.index') }}" class="flex items-center rounded-xl py-3 text-lg transition @if(request()->routeIs('hr.index')) bg-white/10 shadow-inner @else hover:bg-white/10 @endif focus:outline-none focus:ring-0" :class="sidebarCollapsed ? 'justify-center px-3' : 'gap-3 px-4'" @if(request()->routeIs('hr.index')) aria-current="page" @endif title="Dashboard">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M3 12l9-9 9 9v8a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-8z"/></svg>
            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Dashboard</span>
        </a>
        <a href="{{ route('hr.leaves') }}" class="flex items-center rounded-xl py-3 text-lg transition @if(request()->routeIs('hr.leaves*')) bg-white/10 shadow-inner @else hover:bg-white/10 @endif focus:outline-none focus:ring-0" :class="sidebarCollapsed ? 'justify-center px-3' : 'gap-3 px-4'" @if(request()->routeIs('hr.leaves*')) aria-current="page" @endif title="Leave Requests">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M3 5h18v4H3V5zm0 6h10v8H3v-8zm12 0h6v8h-6v-8z"/></svg>
            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Leave Requests</span>
        </a>
        <a href="{{ route('categories.index') }}" class="flex items-center rounded-xl py-3 text-lg transition @if(request()->routeIs('categories.*')) bg-white/10 shadow-inner @else hover:bg-white/10 @endif focus:outline-none focus:ring-0" :class="sidebarCollapsed ? 'justify-center px-3' : 'gap-3 px-4'" @if(request()->routeIs('categories.*')) aria-current="page" @endif title="Leave Categories">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M4 6h16v2H4V6zm0 5h16v2H4v-2zm0 5h10v2H4v-2z"/></svg>
            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Leave Categories</span>
        </a>
        <a href="{{ route('hr.downloads') }}" class="flex items-center rounded-xl py-3 text-lg transition @if(request()->routeIs('hr.downloads')) bg-white/10 shadow-inner @else hover:bg-white/10 @endif focus:outline-none focus:ring-0" :class="sidebarCollapsed ? 'justify-center px-3' : 'gap-3 px-4'" @if(request()->routeIs('hr.downloads')) aria-current="page" @endif title="Download">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                <path d="M4 19h16v2H4z"/>
                <path d="M11 3h2v10h3l-4 4-4-4h3z"/>
            </svg>
            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Download</span>
        </a>
        <div x-data="{ openSettings: {{ request()->routeIs('hr.settings') ? 'true' : 'false' }} }">
            <button type="button" @click="openSettings = !openSettings" class="w-full flex items-center rounded-xl py-3 text-lg transition @if(request()->routeIs('hr.settings')) bg-white/10 shadow-inner @else hover:bg-white/10 @endif focus:outline-none focus:ring-0" :class="sidebarCollapsed ? 'justify-center px-3' : 'gap-3 px-4'" title="Settings">
                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M19.14 12.94a7.49 7.49 0 000-1.88l2.03-1.58a.5.5 0 00.12-.65l-1.92-3.32a.5.5 0 00-.6-.22l-2.39.96a7.25 7.25 0 00-1.63-.94l-.36-2.54a.5.5 0 00-.5-.42h-3.84a.5.5 0 00-.5.42L8.2 5.31a7.25 7.25 0 00-1.63.94l-2.39-.96a.5.5 0 00-.6.22L1.66 8.83a.5.5 0 00.12.65l2.03 1.58a7.49 7.49 0 000 1.88L1.78 14.52a.5.5 0 00-.12.65l1.92 3.32a.5.5 0 00.6.22l2.39-.96c.5.39 1.05.71 1.63.94l.36 2.54a.5.5 0 00.5.42h3.84a.5.5 0 00.5-.42l.36-2.54c.58-.23 1.13-.55 1.63-.94l2.39.96a.5.5 0 00.6-.22l1.92-3.32a.5.5 0 00-.12-.65l-2.03-1.58zM12 15.5A3.5 3.5 0 1112 8a3.5 3.5 0 010 7.5z"/></svg>
                <span x-show="!sidebarCollapsed" class="flex-1 text-left whitespace-nowrap">System Setting</span>
                <svg x-show="!sidebarCollapsed" class="h-4 w-4 transition-transform" :class="openSettings ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor"><path d="M5.23 7.21a.75.75 0 011.06-.02L10 10.67l3.71-3.48a.75.75 0 111.04 1.08l-4.23 3.97a.75.75 0 01-1.04 0L5.25 8.27a.75.75 0 01-.02-1.06z"/></svg>
            </button>
            <div x-cloak x-show="!sidebarCollapsed && openSettings" class="mt-1 space-y-1 px-4">
                <a href="{{ route('hr.settings', ['view'=>'credit']) }}" class="flex items-center rounded-lg py-2 text-base transition hover:bg-white/10 focus:outline-none focus:ring-0 gap-3 px-3">
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 6v5h4v2h-6V8h2z"/></svg>
                    <span class="whitespace-nowrap">Credit Management</span>
                </a>
                <a href="{{ route('hr.settings', ['view'=>'credit']) }}" class="flex items-center rounded-lg py-2 text-base transition hover:bg-white/10 focus:outline-none focus:ring-0 gap-3 px-3">
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3v18m9-9H3"/></svg>
                    <span class="whitespace-nowrap">Monthly Credit</span>
                </a>
                <div x-data="{ openSignatories: {{ (request()->routeIs('hr.settings') && request('view') === 'signatories') ? 'true' : 'false' }} }">
                    <button type="button" @click="openSignatories = !openSignatories" class="w-full flex items-center rounded-lg py-2 text-base transition hover:bg-white/10 focus:outline-none focus:ring-0 gap-3 px-3">
                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12a5 5 0 100-10 5 5 0 000 10zm-7 9a7 7 0 1114 0H5z"/></svg>
                        <span class="flex-1 text-left whitespace-nowrap">Signatories</span>
                        <svg class="h-3 w-3 transition-transform" :class="openSignatories ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor"><path d="M5.23 7.21a.75.75 0 011.06-.02L10 10.67l3.71-3.48a.75.75 0 111.04 1.08l-4.23 3.97a.75.75 0 01-1.04 0L5.25 8.27a.75.75 0 01-.02-1.06z"/></svg>
                    </button>
                    <div x-cloak x-show="openSignatories" class="mt-1 space-y-1 pl-6">
                        <a href="{{ route('hr.settings', ['view'=>'signatories','role'=>'hr']) }}" class="flex items-center rounded-md py-1.5 text-sm transition hover:bg-white/10 focus:outline-none focus:ring-0 gap-2 px-2">
                            <span class="whitespace-nowrap">HR</span>
                        </a>
                        <a href="{{ route('hr.settings', ['view'=>'signatories','role'=>'dc']) }}" class="flex items-center rounded-md py-1.5 text-sm transition hover:bg-white/10 focus:outline-none focus:ring-0 gap-2 px-2">
                            <span class="whitespace-nowrap">DC</span>
                        </a>
                        <a href="{{ route('hr.settings', ['view'=>'signatories','role'=>'rd']) }}" class="flex items-center rounded-md py-1.5 text-sm transition hover:bg-white/10 focus:outline-none focus:ring-0 gap-2 px-2">
                            <span class="whitespace-nowrap">RD</span>
                        </a>
                        <a href="{{ route('hr.settings', ['view'=>'signatories','role'=>'ard']) }}" class="flex items-center rounded-md py-1.5 text-sm transition hover:bg-white/10 focus:outline-none focus:ring-0 gap-2 px-2">
                            <span class="whitespace-nowrap">ARD</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <a href="{{ route('hr.help') }}" class="flex items-center rounded-xl py-3 text-lg transition @if(request()->routeIs('hr.help')) bg-white/10 shadow-inner @else hover:bg-white/10 @endif focus:outline-none focus:ring-0" :class="sidebarCollapsed ? 'justify-center px-3' : 'gap-3 px-4'" @if(request()->routeIs('hr.help')) aria-current="page" @endif title="Help & Support">
            <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 100 20 10 10 0 0 0 0-20zm1 15h-2v-2h2v2zm1.07-7.75l-.9.92A2 2 0 0 0 12 12h-1v-1c0-.55.22-1.05.59-1.41l1.2-1.2a1.5 1.5 0 1 0-2.56-1.06H8.5a3.5 3.5 0 1 1 6.57 1.72z"/></svg>
            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Help &amp; Support</span>
        </a>
    </nav>
</aside>
