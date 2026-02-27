<x-app-layout>
    <div class="hr-shell min-h-[calc(100vh-4rem)] bg-[#f4f5f9]" x-data="{ sidebarCollapsed: false }">
        <div class="grid min-h-[calc(100vh-4rem)] grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] lg:transition-[grid-template-columns] lg:duration-200 lg:ease-out" :class="sidebarCollapsed ? 'lg:grid-cols-[88px_minmax(0,1fr)]' : 'lg:grid-cols-[280px_minmax(0,1fr)]'">
            @include('user.partials.sidebar')
            <section class="h-full overflow-auto p-4 sm:p-6 lg:p-8">
                    <h1 class="text-2xl sm:text-3xl font-semibold text-gray-700">Dashboard</h1>
                    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                            <div class="p-6">
                                <div class="text-lg font-medium text-gray-800">My Leave Requests</div>
                                <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-4">
                                    <div class="p-4 rounded-lg border border-gray-100 bg-gray-50">
                                        <div class="text-xs uppercase text-gray-500">Total</div>
                                        <div class="mt-1 text-2xl font-semibold">{{ $leaveCounts['total'] ?? 0 }}</div>
                                    </div>
                                    <div class="p-4 rounded-lg border border-gray-100 bg-gray-50">
                                        <div class="text-xs uppercase text-gray-500">Pending</div>
                                        <div class="mt-1 text-2xl font-semibold text-amber-600">{{ $leaveCounts['pending'] ?? 0 }}</div>
                                    </div>
                                    <div class="p-4 rounded-lg border border-gray-100 bg-gray-50">
                                        <div class="text-xs uppercase text-gray-500">Approved</div>
                                        <div class="mt-1 text-2xl font-semibold text-green-600">{{ $leaveCounts['approved'] ?? 0 }}</div>
                                    </div>
                                    <div class="p-4 rounded-lg border border-gray-100 bg-gray-50">
                                        <div class="text-xs uppercase text-gray-500">Rejected</div>
                                        <div class="mt-1 text-2xl font-semibold text-red-600">{{ $leaveCounts['rejected'] ?? 0 }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <div class="text-lg font-medium text-gray-800">Credits</div>
                                    <a href="{{ route('user.credits') }}" class="text-sm text-indigo-600 hover:text-indigo-700">View all</a>
                                </div>
                                <div class="mt-4">
                                    <div class="text-sm text-gray-600">Total Credits</div>
                                    @php
                                        $vl = (float)($baseline->vl_total ?? 0);
                                        $sl = (float)($baseline->sl_total ?? 0);
                                        $totalCredits = (float)($baseline->credits_total ?? ($vl + $sl));
                                    @endphp
                                    <div class="text-4xl font-semibold">{{ number_format($totalCredits, 3) }}</div>
                                </div>
                                <div class="mt-6">
                                    <div class="text-sm font-medium text-gray-700 mb-2">Recent Holds</div>
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @forelse ($holds ?? [] as $h)
                                                <tr>
                                                    <td class="px-4 py-2">#{{ $h->leave_id }}</td>
                                                    <td class="px-4 py-2">{{ number_format((float)($h->amount ?? 0), 3) }}</td>
                                                    <td class="px-4 py-2 capitalize">{{ $h->status }}</td>
                                                    <td class="px-4 py-2">{{ $h->created_at?->timezone(config('app.timezone'))->format('M d, Y g:i A') }}</td>
                                                </tr>
                                            @empty
                                                <tr><td class="px-4 py-6 text-gray-500" colspan="4">No recent holds.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                    @if(($holds ?? null) && method_exists($holds,'links'))
                                        <div class="mt-4">
                                            {{ $holds->links() }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
            </section>
        </div>
    </div>
</x-app-layout>
