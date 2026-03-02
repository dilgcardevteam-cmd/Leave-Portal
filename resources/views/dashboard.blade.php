<x-app-layout>
    <div class="hr-shell min-h-[calc(100vh-4rem)] bg-white" x-data="{ sidebarCollapsed: false }">
        <div class="grid min-h-[calc(100vh-4rem)] grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] lg:transition-[grid-template-columns] lg:duration-200 lg:ease-out" :class="sidebarCollapsed ? 'lg:grid-cols-[88px_minmax(0,1fr)]' : 'lg:grid-cols-[280px_minmax(0,1fr)]'">
            @include('user.partials.sidebar')
            <section class="h-full overflow-auto p-6 sm:p-8 lg:p-10">
                @php
                    $vl = (float)($baseline->vl_total ?? 0);
                    $sl = (float)($baseline->sl_total ?? 0);
                    $totalCredits = (float)($baseline->credits_total ?? ($vl + $sl));
                @endphp
                <div class="max-w-6xl mx-auto">
                    <h1 class="text-[64px] leading-tight text-gray-900">Dashboard</h1>
                    <div class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded-lg bg-[#bec3bb] text-gray-900">
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
                        <div class="rounded-lg bg-[#bec3bb] text-gray-900">
                            <div class="p-4">
                                <div class="text-[11px] uppercase tracking-wide text-gray-800">Sick Leave Credits</div>
                                <div class="mt-4 text-[34px] leading-none font-medium">{{ number_format($sl, 3) }}</div>
                            </div>
                        </div>
                        <div class="rounded-lg bg-[#bec3bb] text-gray-900">
                            <div class="p-4">
                                <div class="text-[11px] uppercase tracking-wide text-gray-800">Vacation Leave Credits</div>
                                <div class="mt-4 text-[34px] leading-none font-medium">{{ number_format($vl, 3) }}</div>
                            </div>
                        </div>
                        <div class="rounded-lg bg-[#bec3bb] text-gray-900">
                            <div class="p-4">
                                <div class="text-[11px] uppercase tracking-wide text-gray-800">Total Credits</div>
                                <div class="mt-4 text-[34px] leading-none font-medium">{{ number_format($totalCredits, 3) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-10">
                        <h2 class="text-[48px] leading-tight text-gray-900">History</h2>
                        <div class="mt-4 rounded-lg bg-[#bec3bb] p-4 sm:p-5">
                            <div class="text-[13px] text-gray-700">Recent Holds</div>
                            <div class="mt-3 overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 rounded-lg overflow-hidden">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse ($holds as $h)
                                            <tr>
                                                <td class="px-4 py-3">#{{ $h->leave_id }}</td>
                                                <td class="px-4 py-3">{{ number_format((float)$h->amount, 3) }}</td>
                                                <td class="px-4 py-3 capitalize">{{ $h->status }}</td>
                                                <td class="px-4 py-3">{{ $h->created_at?->timezone(config('app.timezone'))->format('M d, Y g:i A') }}</td>
                                            </tr>
                                        @empty
                                            <tr><td class="px-4 py-6 text-gray-500" colspan="4">No recent holds.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                {{ $holds->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
