<x-app-layout>
    <div class="hr-shell min-h-[calc(100vh-4rem)] bg-[#f4f5f9]" x-data="{ sidebarCollapsed: false }">
        <div class="grid min-h-[calc(100vh-4rem)] grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] lg:transition-[grid-template-columns] lg:duration-200 lg:ease-out" :class="sidebarCollapsed ? 'lg:grid-cols-[88px_minmax(0,1fr)]' : 'lg:grid-cols-[280px_minmax(0,1fr)]'">
            @include('user.partials.sidebar')
            <section class="p-4 sm:p-6 lg:p-8">
                    <h1 class="text-2xl sm:text-3xl font-semibold text-gray-700">Leave Application</h1>
                    @if (session('status'))
                        <div class="mt-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
                    @endif
                    <div class="mt-6 bg-white p-6 shadow sm:rounded-lg border border-gray-100">
                        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="text-lg font-medium">My Requests</div>
                            <div class="flex flex-wrap items-center gap-2">
                                <form method="GET" id="userStatusFilterForm">
                                    <input type="hidden" name="q" id="userSearchHidden" value="{{ request('q') }}">
                                    <select name="status" id="userStatusFilter" class="h-10 rounded-lg border-gray-300 bg-white px-3 text-sm text-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">All Status</option>
                                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </form>
                                <form method="GET" id="userSearchForm">
                                    <input type="hidden" name="status" id="userStatusHidden" value="{{ request('status') }}">
                                    <div class="relative">
                                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                                        </span>
                                        <input type="text" name="q" id="userSearchInput" value="{{ request('q') }}" placeholder="Search category, status, date..." class="h-10 w-64 rounded-lg border-gray-300 bg-white pl-9 pr-3 text-sm leading-5 text-gray-700 shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                </form>
                                <a href="{{ route('leaves.create') }}" class="inline-flex h-10 items-center rounded-lg bg-indigo-600 px-4 text-white shadow-sm transition hover:bg-indigo-700">New Request</a>
                            </div>
                        </div>
                        <script>
                            (function () {
                                const form = document.getElementById('userStatusFilterForm');
                                const select = document.getElementById('userStatusFilter');
                                const searchForm = document.getElementById('userSearchForm');
                                const searchInput = document.getElementById('userSearchInput');
                                const searchHidden = document.getElementById('userSearchHidden');
                                const statusHidden = document.getElementById('userStatusHidden');
                                if (!form || !select || !searchForm || !searchInput || !searchHidden || !statusHidden) return;

                                select.addEventListener('change', () => {
                                    searchHidden.value = searchInput.value || '';
                                    form.submit();
                                });

                                let t = null;
                                const submitSearch = () => {
                                    statusHidden.value = select.value || '';
                                    searchForm.submit();
                                };
                                searchInput.addEventListener('input', () => {
                                    clearTimeout(t);
                                    t = setTimeout(submitSearch, 300);
                                });
                            })();
                        </script>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level of Approval</th>
                                        <th class="px-4 py-2"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($leaves as $leave)
                                    <tr>
                                        <td class="px-4 py-2">{{ $leave->category->name ?? '-' }}</td>
                                        <td class="px-4 py-2 text-gray-600">{{ $leave->start_date }} → {{ $leave->end_date }}</td>
                                        <td class="px-4 py-2">{{ $leave->days }}</td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-1 rounded text-sm {{ $leave->status === 'approved' ? 'bg-green-100 text-green-800' : ($leave->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">{{ ucfirst($leave->status) }}</span>
                                        </td>
                                        <td class="px-4 py-2">
                                            @php
                                                $level = '';
                                                $levelClass = 'bg-blue-50 text-blue-700';
                                                if (($leave->status ?? '') === 'approved') {
                                                    $level = 'Approved';
                                                    $levelClass = 'bg-green-100 text-green-800';
                                                } elseif (($leave->status ?? '') === 'rejected') {
                                                    $level = 'Rejected';
                                                    $levelClass = 'bg-red-100 text-red-800';
                                                } elseif (($leave->workflow_state ?? '') === 'hr_pending') {
                                                    $level = 'HR processing';
                                                } elseif (($leave->workflow_state ?? '') === 'dc_pending') {
                                                    $level = 'DC processing';
                                                } elseif (($leave->workflow_state ?? '') === 'final_pending') {
                                                    $level = ($leave->final_approver_role ?? '') === 'ard' ? 'ARD processing' : 'RD processing';
                                                } else {
                                                    if (!empty($leave->dc_approved_at)) {
                                                        $level = 'DC processing';
                                                    } elseif (!empty($leave->hr_approved_at)) {
                                                        $level = 'HR processing';
                                                    } else {
                                                        $level = 'HR processing';
                                                    }
                                                }
                                            @endphp
                                            <span class="px-2 py-1 rounded text-xs {{ $levelClass }}">{{ $level }}</span>
                                        </td>
                                        <td class="px-4 py-2 text-right space-x-3">
                                            @if ($leave->status === 'pending')
                                                <a href="{{ route('leaves.edit', $leave) }}" class="text-indigo-600 hover:underline">Edit</a>
                                                <form action="{{ route('leaves.destroy', $leave) }}" method="POST" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Cancel this request?')">Delete</button>
                                                </form>
                                            @elseif ($leave->status === 'approved')
                                                <a href="{{ route('leaves.pdf', $leave) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 rounded bg-indigo-600 text-white text-sm hover:bg-indigo-700">Download</a>
                                                <a href="{{ route('leaves.pdf.view', $leave) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 rounded text-white text-sm hover:opacity-90" style="background-color:#FEC20C;">View</a>
                                            @endif
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
