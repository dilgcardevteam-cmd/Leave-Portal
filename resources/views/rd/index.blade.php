<x-app-layout>
    <div class="hr-shell min-h-[calc(100vh-4rem)] bg-[#f4f5f9]" x-data="{ sidebarCollapsed: false }">
            <div class="grid min-h-[calc(100vh-4rem)] grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] lg:transition-[grid-template-columns] lg:duration-200 lg:ease-out" :class="sidebarCollapsed ? 'lg:grid-cols-[88px_minmax(0,1fr)]' : 'lg:grid-cols-[280px_minmax(0,1fr)]'">
                @include('rd.partials.sidebar')
                <section class="p-4 sm:p-6 lg:p-8">
                    <div class="bg-white border-t border-gray-100 rounded-none p-6 sm:p-8 shadow-none">
                        <div class="flex flex-col sm:flex-row items-start sm:items-end sm:justify-start gap-3">
                            <div>
                                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-800 tracking-tight">RD Dashboard</h1>
                                <p class="text-sm text-gray-500 mt-1">Welcome, <span class="font-medium text-[#0d3b66]">{{ Auth::user()->display_name }}</span>. Review and process leave requests.</p>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('rd.leaves') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white shadow hover:bg-indigo-700 transition">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M3 5h18v4H3V5zm0 6h10v8H3v-8zm12 0h6v8h-6v-8z"/></svg>
                                    Go to Leaves
                                </a>
                            </div>
                        </div>
                        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="group rounded-xl border border-gray-200 bg-white p-5 shadow-sm hover:shadow transition">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3a9 9 0 100 18 9 9 0 000-18zm0 2a7 7 0 110 14 7 7 0 010-14zm-1 3h2v5h3v2h-5V8z"/></svg>
                                    </div>
                                    <div>
                                        <div class="text-xs uppercase text-gray-500">Total Requests</div>
                                        <div class="text-2xl font-semibold text-gray-900">{{ $stats['requests'] }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="group rounded-xl border border-gray-200 bg-white p-5 shadow-sm hover:shadow transition">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-lg bg-yellow-100 text-yellow-800 flex items-center justify-center">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 5h-2v6h6v-2h-4V7z"/></svg>
                                    </div>
                                    <div>
                                        <div class="text-xs uppercase text-gray-500">Pending</div>
                                        <div class="text-2xl font-semibold text-gray-900">{{ $stats['pending'] }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="group rounded-xl border border-gray-200 bg-white p-5 shadow-sm hover:shadow transition">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-lg bg-green-100 text-green-700 flex items-center justify-center">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 100 20 10 10 0 000-20zm-1 14l-4-4 1.41-1.41L11 12.17l4.59-4.58L17 9l-6 7z"/></svg>
                                    </div>
                                    <div>
                                        <div class="text-xs uppercase text-gray-500">Approved</div>
                                        <div class="text-2xl font-semibold text-gray-900">{{ $stats['approved'] }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="group rounded-xl border border-gray-200 bg-white p-5 shadow-sm hover:shadow transition">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-lg bg-red-100 text-red-700 flex items-center justify-center">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 100 20 10 10 0 000-20zm-1.41 6.59L12 10l1.41-1.41L15 10.17l1.41-1.41L16.83 8l-1.42-1.41L14 9.17l-1.41-1.58L12 8.59l-1.41-1.41L9.17 8 10.59 9.41 9.17 10.83 10.59 12.25 12 10.83l1.41 1.42L14.83 11l-1.42-1.41L14.83 8l-1.42-1.41L12 7.17l-1.41-1.99z"/></svg>
                                    </div>
                                    <div>
                                        <div class="text-xs uppercase text-gray-500">Rejected</div>
                                        <div class="text-2xl font-semibold text-gray-900">{{ $stats['rejected'] }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
    </div>
</x-app-layout>

