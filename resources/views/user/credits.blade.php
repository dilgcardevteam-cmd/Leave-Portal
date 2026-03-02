<x-app-layout>
    <div class="hr-shell min-h-[calc(100vh-4rem)] bg-[#f4f5f9]" x-data="{ sidebarCollapsed: false }">
        <div class="grid min-h-[calc(100vh-4rem)] grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] lg:transition-[grid-template-columns] lg:duration-200 lg:ease-out" :class="sidebarCollapsed ? 'lg:grid-cols-[88px_minmax(0,1fr)]' : 'lg:grid-cols-[280px_minmax(0,1fr)]'">
            @include('user.partials.sidebar')
            <section class="p-4 sm:p-6 lg:p-8">
                <div class="glass p-6">
                    <div class="flex items-end justify-between">
                        <div>
                            <div class="text-2xl font-semibold text-gray-900">Credits</div>
                            <div class="text-sm text-gray-600">All Holds</div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-gray-600">Total Credits</div>
                            <div class="text-3xl font-semibold text-gray-900">{{ number_format((float)(($baseline->vl_total ?? 0) + ($baseline->sl_total ?? 0)), 3) }}</div>
                        </div>
                    </div>
                    <div class="mt-6 overflow-x-auto rounded-xl ring-1 ring-gray-200 bg-white/60 backdrop-blur">
                        <table class="min-w-full">
                            <thead>
                                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-700">
                                    <th class="px-4 py-2">Leave</th>
                                    <th class="px-4 py-2">Amount</th>
                                    <th class="px-4 py-2">Status</th>
                                    <th class="px-4 py-2">Date</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm text-gray-900">
                                @forelse ($holds as $h)
                                    @php
                                        $leaveStatus = ucfirst($h->leave?->status ?? '');
                                        $map = ['applied' => 'Approved', 'held' => 'Pending', 'released' => 'Rejected'];
                                        $displayStatus = $leaveStatus !== '' ? $leaveStatus : ($map[$h->status] ?? ucfirst($h->status ?? ''));
                                    @endphp
                                    <tr class="border-t border-gray-300/60 hover:bg-gray-50/70 transition">
                                        <td class="px-4 py-3">#{{ $h->leave_id }}</td>
                                        <td class="px-4 py-3">{{ number_format((float)$h->amount, 3) }}</td>
                                        <td class="px-4 py-3">
                                            <x-status-chip :status="$displayStatus" />
                                        </td>
                                        <td class="px-4 py-3">{{ $h->created_at?->timezone(config('app.timezone'))->format('M d, Y g:i A') }}</td>
                                    </tr>
                                @empty
                                    <tr><td class="px-4 py-6 text-gray-500" colspan="4">No holds found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
