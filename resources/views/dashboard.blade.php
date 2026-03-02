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
                    <h1 class="mb-6 text-4xl font-semibold tracking-tight text-gray-900">Dashboard</h1>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="glass text-gray-900">
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
                        <div class="glass text-gray-900">
                            <div class="p-4">
                                <div class="text-[11px] uppercase tracking-wide text-gray-800">Sick Leave Credits</div>
                                <div class="mt-4 text-[34px] leading-none font-medium">{{ number_format($sl, 3) }}</div>
                            </div>
                        </div>
                        <div class="glass text-gray-900">
                            <div class="p-4">
                                <div class="text-[11px] uppercase tracking-wide text-gray-800">Vacation Leave Credits</div>
                                <div class="mt-4 text-[34px] leading-none font-medium">{{ number_format($vl, 3) }}</div>
                            </div>
                        </div>
                        <div class="glass text-gray-900">
                            <div class="p-4">
                                <div class="text-[11px] uppercase tracking-wide text-gray-800">Total Credits</div>
                                <div class="mt-4 text-[34px] leading-none font-medium">{{ number_format($totalCredits, 3) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <div class="glass w-full">
                            <div class="p-4 sm:p-6">
                                <h2 class="text-3xl font-semibold text-gray-900 tracking-tight">History</h2>
                                <div class="mt-1 text-sm text-gray-600">Recent Holds</div>
                                <div class="mt-6 overflow-x-auto">
                                    <table class="min-w-full">
                                        <thead>
                                            <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-700">
                                                <th class="px-3 py-2">Leave</th>
                                                <th class="px-3 py-2">Amount</th>
                                                <th class="px-3 py-2">Status</th>
                                                <th class="px-3 py-2">Date</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-sm text-gray-900">
                                            @foreach ($holds as $hold)
                                                @php
                                                    $labelStatus = $hold->status === 'held' ? 'Applied' : ucfirst($hold->status ?? '');
                                                    $leave = $hold->leave;
                                                    $leaveLink = null;
                                                    if ($leave && $leave->status === 'approved') {
                                                        $leaveLink = route('leaves.pdf.view', $leave);
                                                    } elseif ($leave && $leave->status === 'pending') {
                                                        $leaveLink = route('leaves.edit', $leave);
                                                    }
                                                @endphp
                                                <tr class="border-t border-gray-300/60">
                                                    <td class="px-3 py-3">
                                                        @if ($leaveLink)
                                                            <a href="{{ $leaveLink }}" class="text-indigo-700 hover:underline">#{{ $hold->leave_id }}</a>
                                                        @else
                                                            <span class="text-gray-800">#{{ $hold->leave_id }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-3 py-3">{{ number_format((float)$hold->amount, 3) }}</td>
                                                    <td class="px-3 py-3">
                                                        <span class="px-2 py-1 rounded text-xs {{ $hold->status === 'released' ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-800' }}">
                                                            {{ $labelStatus }}
                                                        </span>
                                                    </td>
                                                    <td class="px-3 py-3 text-gray-700">
                                                        @if ($hold->created_at)
                                                            {{ $hold->created_at->timezone(config('app.timezone'))->format('M d, Y g:i A') }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-4 flex items-center justify-between text-sm text-gray-700">
                                    <div>
                                        @if($holds->count())
                                            Showing {{ $holds->firstItem() }} to {{ $holds->lastItem() }} of {{ $holds->total() }} results
                                        @else
                                            No results
                                        @endif
                                    </div>
                                    <div>
                                        {{ $holds->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
