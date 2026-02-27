<x-app-layout>
    <div class="hr-shell min-h-[calc(100vh-4rem)] bg-[#f4f5f9]" x-data="{ sidebarCollapsed: false }">
        <div class="grid min-h-[calc(100vh-4rem)] grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] lg:transition-[grid-template-columns] lg:duration-200 lg:ease-out" :class="sidebarCollapsed ? 'lg:grid-cols-[88px_minmax(0,1fr)]' : 'lg:grid-cols-[280px_minmax(0,1fr)]'">
            @include('user.partials.sidebar')
            <section class="p-4 sm:p-6 lg:p-8">
                    <div class="bg-white p-6 shadow sm:rounded-lg">
                        <div class="text-lg font-medium mb-4">Credits</div>
                        <div class="mb-4">
                            <div class="text-sm text-gray-600">Total Credits</div>
                            <div class="text-3xl font-semibold">{{ number_format((float)(Auth::user()?->credits_total ?? 0), 3) }}</div>
                        </div>
                        <div>
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
                                    @forelse ($holds as $h)
                                        <tr>
                                            <td class="px-4 py-2">#{{ $h->leave_id }}</td>
                                            <td class="px-4 py-2">{{ number_format((float)$h->amount, 3) }}</td>
                                            <td class="px-4 py-2 capitalize">{{ $h->status }}</td>
                                            <td class="px-4 py-2">{{ $h->created_at?->timezone(config('app.timezone'))->format('M d, Y g:i A') }}</td>
                                        </tr>
                                    @empty
                                        <tr><td class="px-4 py-6 text-gray-500" colspan="4">No recent holds.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>

