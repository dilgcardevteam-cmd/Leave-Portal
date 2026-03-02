<x-app-layout>
    <div class="hr-shell min-h-[calc(100vh-4rem)] bg-[#f3f4f7]" x-data="{ sidebarCollapsed: false }">
        <div class="grid min-h-[calc(100vh-4rem)] grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] lg:transition-[grid-template-columns] lg:duration-200 lg:ease-out" :class="sidebarCollapsed ? 'lg:grid-cols-[88px_minmax(0,1fr)]' : 'lg:grid-cols-[280px_minmax(0,1fr)]'">
            @include('user.partials.sidebar')
            <section class="h-full overflow-auto p-6 sm:p-8 lg:p-10">
                @php
                    $vl = (float)($baseline->vl_total ?? 0);
                    $sl = (float)($baseline->sl_total ?? 0);
                    $totalCredits = (float)($baseline->credits_total ?? ($vl + $sl));
                @endphp
                <div class="max-w-6xl">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="bg-[#f3f4f7] text-gray-900">
                            <div class="flex gap-4 p-4">
                                <div class="flex-1">
                                    <div class="text-[52px] leading-none font-medium">{{ $leaveCounts['total'] ?? 0 }}</div>
                                    <div class="mt-2 text-[11px] leading-tight uppercase tracking-wide text-gray-800">Total Requests</div>
                                </div>
                                <div class="w-px bg-gray-400/70"></div>
                                <div class="flex-1 text-[10px] uppercase tracking-wide text-gray-800 space-y-2">
                                    <div>
                                        <div class="text-[16px] leading-none font-semibold text-gray-900">{{ $leaveCounts['approved'] ?? 0 }}</div>
                                        Approved
                                    </div>
                                    <div>
                                        <div class="text-[16px] leading-none font-semibold text-gray-900">{{ $leaveCounts['pending'] ?? 0 }}</div>
                                        Pending
                                    </div>
                                    <div>
                                        <div class="text-[16px] leading-none font-semibold text-gray-900">{{ $leaveCounts['rejected'] ?? 0 }}</div>
                                        Rejected
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-[#f3f4f7] text-gray-900">
                            <div class="p-4">
                                <div class="text-[11px] uppercase tracking-wide text-gray-800">Sick Leave Credits</div>
                                <div class="mt-4 text-[34px] leading-none font-medium">{{ number_format($sl, 3) }}</div>
                            </div>
                        </div>
                        <div class="bg-[#f3f4f7] text-gray-900">
                            <div class="p-4">
                                <div class="text-[11px] uppercase tracking-wide text-gray-800">Vacation Leave Credits</div>
                                <div class="mt-4 text-[34px] leading-none font-medium">{{ number_format($vl, 3) }}</div>
                            </div>
                        </div>
                        <div class="bg-[#f3f4f7] text-gray-900">
                            <div class="p-4">
                                <div class="text-[11px] uppercase tracking-wide text-gray-800">Total Credits</div>
                                <div class="mt-4 text-[34px] leading-none font-medium">{{ number_format($totalCredits, 3) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <div class="bg-[#f3f4f7] min-h-[360px] w-full"></div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
