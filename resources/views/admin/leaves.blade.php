<x-app-layout>
    <div class="py-0">
        <div class="px-0">
            <div class="grid grid-cols-1 lg:grid-cols-[256px_minmax(0,1fr)] gap-6">
                @include('admin.partials.sidebar')
                <section>
                    @if (session('status'))
                        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
                    @endif
                    <div class="bg-white p-6 shadow sm:rounded-lg">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                            <div class="text-lg font-medium">All Leave Requests</div>
                            <form id="filterForm" method="GET" class="flex gap-2">
                                <select name="status" id="statusSelect" class="border-gray-300 rounded-md shadow-sm">
                                    <option value="">All</option>
                                    <option value="pending" @selected(request('status')==='pending')>Pending</option>
                                    <option value="approved" @selected(request('status')==='approved')>Approved</option>
                                    <option value="rejected" @selected(request('status')==='rejected')>Rejected</option>
                                </select>
                                <input type="text" name="q" id="searchInput" value="{{ request('q') }}" placeholder="Search" class="border-gray-300 rounded-md shadow-sm" />
                            </form>
                        </div>
                        <script>
                            (function () {
                                const form = document.getElementById('filterForm');
                                const input = document.getElementById('searchInput');
                                const select = document.getElementById('statusSelect');
                                if (!form || !input || !select) return;
                                let t;
                                const submit = () => form.requestSubmit ? form.requestSubmit() : form.submit();
                                input.addEventListener('input', () => {
                                    clearTimeout(t);
                                    t = setTimeout(submit, 300);
                                });
                                select.addEventListener('change', submit);
                            })();
                        </script>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TYPE OF LEAVE TO BE AVAILED OF</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-2"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($leaves as $leave)
                                    <tr>
                                        <td class="px-4 py-2">
                                            <div class="text-gray-900">{{ $leave->user->display_name ?? ($leave->user->name ?? '-') }}</div>
                                            <div class="text-xs text-gray-500">{{ $leave->user->email ?? '' }}</div>
                                        </td>
                                        <td class="px-4 py-2">{{ $leave->category->name ?? '-' }}</td>
                                        <td class="px-4 py-2 text-gray-600">{{ $leave->start_date }} → {{ $leave->end_date }}</td>
                                        <td class="px-4 py-2">{{ $leave->days }}</td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-1 rounded text-sm {{ $leave->status === 'approved' ? 'bg-green-100 text-green-800' : ($leave->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">{{ ucfirst($leave->status) }}</span>
                                        </td>
                                        <td class="px-4 py-2 text-right">
                                            <a href="{{ route('admin.leaves.show', $leave) }}" class="px-3 py-1 bg-indigo-600 text-white rounded">View</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $leaves->links() }}
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
