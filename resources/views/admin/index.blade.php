<x-app-layout>
    <div class="py-0">
        <div class="px-0">
            <div class="grid grid-cols-1 lg:grid-cols-[256px_minmax(0,1fr)] gap-6 h-[calc(100vh-4rem)] overflow-hidden">
                @include('admin.partials.sidebar')
                <section class="h-full overflow-hidden">
                    <h1 class="admin-welcome text-2xl sm:text-3xl font-semibold text-gray-700">Welcome, <span class="text-[#0d3b66]">{{ Auth::user()->display_name }}</span></h1>
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="admin-card p-6 flex items-center">
                            <div class="icon">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.657 0 3-1.79 3-4s-1.343-4-3-4-3 1.79-3 4 1.343 4 3 4zm-8 0c1.657 0 3-1.79 3-4S9.657 3 8 3 5 4.79 5 7s1.343 4 3 4zm0 2c-2.67 0-8 1.34-8 4v2h10v-2c0-1.31.84-2.42 2.06-3.17A9.42 9.42 0 0 0 8 13zm8 0c-.29 0-.62.02-.97.05A5.98 5.98 0 0 1 19 18v2h5v-2c0-2.66-5.33-5-8-5z"/></svg>
                            </div>
                            <div>
                                <div class="text-3xl font-bold text-gray-900">{{ $stats['users'] }}</div>
                                <div class="text-sm text-gray-500">Total Users</div>
                            </div>
                        </div>
                        <div class="admin-card p-6 flex items-center">
                            <div class="icon">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M3 5h18v4H3V5zm0 6h10v8H3v-8zm12 0h6v8h-6v-8z"/></svg>
                            </div>
                            <div>  
                                <div class="text-3xl font-bold text-gray-900">{{ $stats['categories'] }}</div>
                                <div class="text-sm text-gray-500">Leave Categories</div>
                            </div>
                        </div>
                        <div class="admin-card p-6 flex items-center">
                            <div class="icon">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3a9 9 0 100 18 9 9 0 000-18zm0 2a7 7 0 110 14 7 7 0 010-14zm-1 3h2v5h3v2h-5V8z"/></svg>
                            </div>
                            <div>
                                <div class="text-3xl font-bold text-gray-900">{{ $stats['requests'] }}</div>
                                <div class="text-sm text-gray-500">Total Requests</div>
                            </div>
                        </div>
                        <div class="admin-card p-6 flex items-center">
                            <div class="icon">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 5h-2v6h6v-2h-4V7z"/></svg>
                            </div>
                            <div>
                                <div class="text-3xl font-bold text-gray-900">{{ $stats['pending'] }}</div>
                                <div class="text-sm text-gray-500">Pending</div>
                            </div>
                        </div>
                        <div class="admin-card p-6 flex items-center">
                            <div class="icon">
                                <svg class="w-6 h-6 text-green-600" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 100 20 10 10 0 000-20zm-1 14l-4-4 1.41-1.41L11 12.17l4.59-4.58L17 9l-6 7z"/></svg>
                            </div>
                            <div>
                                <div class="text-3xl font-bold text-gray-900">{{ $stats['approved'] }}</div>
                                <div class="text-sm text-gray-500">Approved</div>
                            </div>
                        </div>
                        <div class="admin-card p-6 flex items-center">
                            <div class="icon">
                                <svg class="w-6 h-6 text-red-600" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 100 20 10 10 0 000-20zm-1.41 6.59L12 10l1.41-1.41L15 10.17l1.41-1.41L16.83 8l-1.42-1.41L14 9.17l-1.41-1.58L12 8.59l-1.41-1.41L9.17 8 10.59 9.41 9.17 10.83 10.59 12.25 12 10.83l1.41 1.42L14.83 11l-1.42-1.41L14.83 8l-1.42-1.41L12 7.17l-1.41-1.99z"/></svg>
                            </div>
                            <div>
                                <div class="text-3xl font-bold text-gray-900">{{ $stats['rejected'] }}</div>
                                <div class="text-sm text-gray-500">Rejected</div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
