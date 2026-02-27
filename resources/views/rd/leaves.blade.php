<x-app-layout>
    <div class="hr-shell min-h-[calc(100vh-4rem)] bg-[#f4f5f9]" x-data="{ sidebarCollapsed: false }">
            <div class="grid min-h-[calc(100vh-4rem)] grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] lg:transition-[grid-template-columns] lg:duration-200 lg:ease-out" :class="sidebarCollapsed ? 'lg:grid-cols-[88px_minmax(0,1fr)]' : 'lg:grid-cols-[280px_minmax(0,1fr)]'">
                @include('rd.partials.sidebar')
                <section class="p-4 sm:p-6 lg:p-8">
                    @if (session('status'))
                        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
                    @endif
                    <div class="bg-white p-6 shadow sm:rounded-xl border border-gray-200">
                        <div class="flex flex-col gap-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="text-xl font-semibold text-gray-800">Leave Requests</h2>
                                    <p class="text-sm text-gray-500">Search and filter across all requests.</p>
                                </div>
                                <div class="hidden sm:flex items-center gap-1 bg-gray-100 p-1 rounded-lg">
                                    @php $q = request('q'); @endphp
                                    <a href="{{ route('rd.leaves', array_filter(['status'=>null,'q'=>$q])) }}" class="px-3 py-1.5 rounded-md text-sm {{ request('status') ? 'text-gray-600' : 'bg-white shadow text-gray-900' }}">All</a>
                                    <a href="{{ route('rd.leaves', array_filter(['status'=>'pending','q'=>$q])) }}" class="px-3 py-1.5 rounded-md text-sm {{ request('status')==='pending' ? 'bg-yellow-500 text-white shadow' : 'text-gray-600' }}">Pending</a>
                                    <a href="{{ route('rd.leaves', array_filter(['status'=>'approved','q'=>$q])) }}" class="px-3 py-1.5 rounded-md text-sm {{ request('status')==='approved' ? 'bg-green-600 text-white shadow' : 'text-gray-600' }}">Approved</a>
                                    <a href="{{ route('rd.leaves', array_filter(['status'=>'rejected','q'=>$q])) }}" class="px-3 py-1.5 rounded-md text-sm {{ request('status')==='rejected' ? 'bg-red-600 text-white shadow' : 'text-gray-600' }}">Rejected</a>
                                </div>
                            </div>
                            <form id="rdFilterForm" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                                <div class="relative flex-1">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                                    </span>
                                    <input type="text" name="q" id="rdSearchInput" value="{{ request('q') }}" placeholder="Search by name, email, category, date…" class="pl-10 border-gray-300 rounded-lg shadow-sm w-full" />
                                </div>
                            </form>
                        </div>
                        <script>
                            (function () {
                                const form = document.getElementById('rdFilterForm');
                                const input = document.getElementById('rdSearchInput');
                                if (!form || !input) return;
                                let t;
                                const submit = () => form.requestSubmit ? form.requestSubmit() : form.submit();
                                input.addEventListener('input', () => {
                                    clearTimeout(t);
                                    t = setTimeout(submit, 300);
                                });
                            })();
                        </script>
                        <div class="overflow-x-auto mt-4">
                            <table class="min-w-full divide-y divide-gray-200 rounded-lg overflow-hidden">
                                <thead class="bg-gray-50 text-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">User</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">TYPE OF LEAVE TO BE AVAILED OF</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Dates</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Days</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @foreach ($leaves as $leave)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <div class="h-10 w-10 rounded-full bg-[#0d3b66] text-white flex items-center justify-center">
                                                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                                        <path d="M12 12c2.761 0 5-2.686 5-6s-2.239-6-5-6-5 2.686-5 6 2.239 6 5 6zm0 2c-4.418 0-12 2.239-12 6v2h24v-2c0-3.761-7.582-6-12-6z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="text-gray-900 font-medium">{{ $leave->user->display_name ?? ($leave->user->name ?? '-') }}</div>
                                                    <div class="text-xs text-gray-500">{{ $leave->user->email ?? '' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">{{ $leave->category->name ?? '-' }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $leave->start_date }} → {{ $leave->end_date }}</td>
                                        <td class="px-4 py-3">{{ $leave->days }}</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                                {{ $leave->status === 'approved' ? 'bg-green-100 text-green-700' : ($leave->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ ucfirst($leave->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('rd.leaves.show', $leave) }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 shadow-sm">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 12c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"/></svg>
                                                View
                                            </a>
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
</x-app-layout>

