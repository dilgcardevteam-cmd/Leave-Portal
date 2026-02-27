<x-app-layout>
    <div class="hr-shell min-h-[calc(100vh-4rem)] bg-[#f4f5f9]" x-data="{ sidebarCollapsed: false }">
        <div class="grid min-h-[calc(100vh-4rem)] grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] lg:transition-[grid-template-columns] lg:duration-200 lg:ease-out" :class="sidebarCollapsed ? 'lg:grid-cols-[88px_minmax(0,1fr)]' : 'lg:grid-cols-[280px_minmax(0,1fr)]'">
            @include($sidebarPartial)
            <section class="p-4 sm:p-6 lg:p-8">
                @if (session('status'))
                    <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('status') }}</div>
                @endif
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow">
                    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800">{{ $title }}</h2>
                            <p class="text-sm text-gray-500">All approved requests from users are available for PDF view/download.</p>
                        </div>
                        <form id="approvedDownloadsSearchForm" method="GET" class="w-full sm:w-80">
                            <input
                                id="approvedDownloadsSearchInput"
                                type="text"
                                name="q"
                                value="{{ request('q') }}"
                                placeholder="Search user, email, category, date..."
                                class="w-full rounded-lg border-gray-300 shadow-sm"
                            />
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 rounded-lg overflow-hidden">
                            <thead class="bg-gray-50 text-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">User</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Type of Leave</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Dates</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Days</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider">PDF</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse ($leaves as $leave)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3">
                                            <div class="text-gray-900 font-medium">{{ $leave->user->display_name ?? ($leave->user->name ?? '-') }}</div>
                                            <div class="text-xs text-gray-500">{{ $leave->user->email ?? '' }}</div>
                                        </td>
                                        <td class="px-4 py-3">{{ $leave->category->name ?? '-' }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $leave->start_date }} -> {{ $leave->end_date }}</td>
                                        <td class="px-4 py-3">{{ $leave->days }}</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">
                                                Approved
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right space-x-2">
                                            <a href="{{ route('leaves.pdf.view', $leave) }}" target="_blank" class="inline-flex items-center rounded px-3 py-1.5 text-sm text-white hover:opacity-90" style="background-color:#FEC20C;">View</a>
                                            <a href="{{ route('leaves.pdf', $leave) }}" target="_blank" class="inline-flex items-center rounded bg-indigo-600 px-3 py-1.5 text-sm text-white hover:bg-indigo-700">Download</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">No approved requests found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $leaves->links() }}
                    </div>
                </div>
                <script>
                    (function () {
                        const form = document.getElementById('approvedDownloadsSearchForm');
                        const input = document.getElementById('approvedDownloadsSearchInput');
                        if (!form || !input) return;
                        let t = null;
                        const submit = () => (form.requestSubmit ? form.requestSubmit() : form.submit());
                        input.addEventListener('input', () => {
                            clearTimeout(t);
                            t = setTimeout(submit, 300);
                        });
                    })();
                </script>
            </section>
        </div>
    </div>
</x-app-layout>
