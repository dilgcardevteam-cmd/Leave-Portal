<x-app-layout>
    <div class="hr-shell min-h-[calc(100vh-4rem)] bg-[#f4f5f9]" x-data="{ sidebarCollapsed: false }">
            <div class="grid min-h-[calc(100vh-4rem)] grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] lg:transition-[grid-template-columns] lg:duration-200 lg:ease-out" :class="sidebarCollapsed ? 'lg:grid-cols-[88px_minmax(0,1fr)]' : 'lg:grid-cols-[280px_minmax(0,1fr)]'">
                @include('dc.partials.sidebar')
                <section class="p-4 sm:p-6 lg:p-8">
                    <div class="bg-white border-t border-gray-100 rounded-none p-6 sm:p-8 shadow-none">
                        <div class="flex items-start sm:items-end sm:justify-start gap-3">
                            <div>
                                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-800 tracking-tight">DC Dashboard</h1>
                                <p class="text-sm text-gray-500 mt-1">Monitor leave activity forwarded by HR.</p>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('dc.leaves') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white shadow hover:bg-indigo-700 transition">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M3 5h18v4H3V5zm0 6h10v8H3v-8zm12 0h6v8h-6v-8z"/></svg>
                                    Go to Leaves
                                </a>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mt-6">
                            <div class="p-4 border rounded-lg">
                                <div class="text-sm text-gray-500">Total Requests</div>
                                <div class="text-2xl font-semibold">{{ $stats['requests'] ?? 0 }}</div>
                            </div>
                            <div class="p-4 border rounded-lg">
                                <div class="text-sm text-gray-500">Pending</div>
                                <div class="text-2xl font-semibold text-yellow-600">{{ $stats['pending'] ?? 0 }}</div>
                            </div>
                            <div class="p-4 border rounded-lg">
                                <div class="text-sm text-gray-500">Approved</div>
                                <div class="text-2xl font-semibold text-green-600">{{ $stats['approved'] ?? 0 }}</div>
                            </div>
                            <div class="p-4 border rounded-lg">
                                <div class="text-sm text-gray-500">Rejected</div>
                                <div class="text-2xl font-semibold text-red-600">{{ $stats['rejected'] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
    </div>
</x-app-layout>

