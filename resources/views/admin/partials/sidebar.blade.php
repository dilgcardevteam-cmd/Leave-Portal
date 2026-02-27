<aside class="hidden lg:block w-64 bg-[#0d3b66] text-white sticky top-16 min-h-[calc(100vh-4rem)] p-4">
    <nav class="space-y-1" aria-label="Admin sidebar">
        <a href="{{ route('admin.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-md transition @if(request()->routeIs('admin.index')) bg-white/20 @else hover:bg-white/10 @endif" @if(request()->routeIs('admin.index')) aria-current="page" @endif>
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M3 12l9-9 9 9v8a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-8z"/></svg>
            <span class="font-medium">Dashboard</span>
        </a>
        <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-4 py-3 rounded-md transition @if(request()->routeIs('admin.users')) bg-white/20 @else hover:bg-white/10 @endif" @if(request()->routeIs('admin.users')) aria-current="page" @endif>
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.657 0 3-1.79 3-4s-1.343-4-3-4-3 1.79-3 4 1.343 4 3 4zm-8 0c1.657 0 3-1.79 3-4S9.657 3 8 3 5 4.79 5 7s1.343 4 3 4zm0 2c-2.67 0-8 1.34-8 4v2h10v-2c0-1.31.84-2.42 2.06-3.17A9.42 9.42 0 0 0 8 13zm8 0c-.29 0-.62.02-.97.05A5.98 5.98 0 0 1 19 18v2h5v-2c0-2.66-5.33-5-8-5z"/></svg>
            <span class="font-medium">User Management</span>
        </a>
        <a href="{{ route('admin.leaves') }}" class="flex items-center gap-3 px-4 py-3 rounded-md transition @if(request()->routeIs('admin.leaves')) bg-white/20 @else hover:bg-white/10 @endif" @if(request()->routeIs('admin.leaves')) aria-current="page" @endif>
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M8 7h13v2H8V7zm0 4h13v2H8v-2zm0 4h13v2H8v-2zM3 7h3v3H3V7zm0 5h3v3H3v-3zm0 5h3v3H3v-3z"/></svg>
            <span class="font-medium">Leave Requests</span>
        </a>
        <a href="{{ route('categories.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-md transition @if(request()->routeIs('categories.*')) bg-white/20 @else hover:bg-white/10 @endif" @if(request()->routeIs('categories.*')) aria-current="page" @endif>
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M3 5h18v4H3V5zm0 6h10v8H3v-8zm12 0h6v8h-6v-8z"/></svg>
            <span class="font-medium">Leave Category Management</span>
        </a>
        <a href="{{ route('admin.help') }}" class="flex items-center gap-3 px-4 py-3 rounded-md transition @if(request()->routeIs('admin.help')) bg-white/20 @else hover:bg-white/10 @endif" @if(request()->routeIs('admin.help')) aria-current="page" @endif>
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 15h-2v-2h2v2zm1.07-7.75l-.9.92A2 2 0 0012 12h-1v-1c0-.55.22-1.05.59-1.41l1.2-1.2a1.5 1.5 0 10-2.56-1.06H8.5a3.5 3.5 0 116.57 1.72z"/></svg>
            <span class="font-medium">Help & Support</span>
        </a>
    </nav>
    <div class="mt-6 hidden">
        <button class="w-full text-left px-4 py-2 rounded-md bg-white/10 hover:bg-white/20 transition">Collapse</button>
    </div>
</aside>
