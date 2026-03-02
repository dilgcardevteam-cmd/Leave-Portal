<x-app-layout>
    @php
        $role = auth()->user()?->role;
        $isHr = $role === 'hr';
        $isUser = $role === 'user';
        $isDc = in_array($role, ['dc', 'lgmed'], true);
        $isRd = $role === 'rd';
        $isArd = $role === 'ard';
        $useCollapsibleSidebar = $isHr || $isUser || $isDc || $isRd || $isArd;
    @endphp
    <div class="hr-shell min-h-[calc(100vh-4rem)] bg-[#f4f5f9]"
         x-data="{
            sidebarCollapsed: false,
            showMarkAllModal: false,
            markAllBusy: false,
            async confirmMarkAllRead() {
                this.markAllBusy = true;
                try {
                    const res = await fetch('{{ route('notifications.mark-all-read') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    if (!res.ok) throw new Error('Request failed');
                    window.location.reload();
                } catch (e) {
                    alert('Failed to mark all as read. Please try again.');
                    this.markAllBusy = false;
                }
            }
         }">
        @if ($useCollapsibleSidebar)
        <div class="grid min-h-[calc(100vh-4rem)] grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] lg:transition-[grid-template-columns] lg:duration-200 lg:ease-out"
             :class="sidebarCollapsed ? 'lg:grid-cols-[88px_minmax(0,1fr)]' : 'lg:grid-cols-[280px_minmax(0,1fr)]'">
        @else
        <div class="grid grid-cols-1 lg:grid-cols-[256px_minmax(0,1fr)] gap-0">
        @endif
            @if ($isHr)
                @include('hr.partials.sidebar')
            @elseif ($isUser)
                @include('user.partials.sidebar')
            @elseif ($isDc)
                @include('dc.partials.sidebar')
            @elseif ($isArd)
                @include('ard.partials.sidebar')
            @elseif ($isRd)
                @include('rd.partials.sidebar')
            @elseif ($role === 'admin')
                @include('admin.partials.sidebar')
            @endif

            <section class="{{ $useCollapsibleSidebar ? 'p-4 sm:p-6 lg:p-8' : '' }}">
                <div class="max-w-5xl {{ $useCollapsibleSidebar ? '' : 'mx-auto' }} px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <a href="{{ url()->previous() ?: ($isHr ? route('hr.index') : ($isUser ? route('dashboard') : ($isDc ? route('dc.index') : ($isArd ? route('ard.index') : ($isRd ? route('rd.index') : ($role === 'admin' ? route('admin.index') : url('/'))))))) }}"
                               onclick="if (history.length > 1) { history.back(); return false; }"
                               class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-3 py-2 text-gray-700 hover:bg-gray-50">
                                <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.293 16.707a1 1 0 0 1-1.414 0l-6-6a1 1 0 0 1 0-1.414l6-6a1 1 0 1 1 1.414 1.414L7.414 9H18a1 1 0 1 1 0 2H7.414l4.879 4.879a1 1 0 0 1 0 1.414z" clip-rule="evenodd"/></svg>
                                Back
                            </a>
                            <h1 class="text-2xl font-semibold text-gray-900 tracking-tight">Notifications</h1>
                        </div>
                        <button type="button"
                                @click="showMarkAllModal = true"
                                class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-3 py-2 text-white shadow hover:bg-indigo-700">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 22zm6-6v-5a6 6 0 1 0-12 0v5l-2 2v1h18v-1l-2-2z"/></svg>
                            Mark all read
                        </button>
                    </div>

                    <div class="mt-4 glass">
                        <div class="p-2 sm:p-3">
                            <ul>
                                @forelse ($notifications as $n)
                                    @php
                                        $data = $n->data ?? [];
                                        $isUnread = is_null($n->read_at);
                                        $title = $data['title'] ?? 'Notification';
                                        $message = $data['message'] ?? '';
                                        $leaveId = $data['leave_id'] ?? null;
                                        $link = null;
                                        if ($leaveId) {
                                            if ($isHr) {
                                                $link = route('hr.leaves.show', ['leave' => $leaveId]);
                                            } elseif ($isDc) {
                                                $link = route('dc.leaves.show', ['leave' => $leaveId]);
                                            } elseif ($isRd) {
                                                $link = route('rd.leaves.show', ['leave' => $leaveId]);
                                            } elseif ($isArd) {
                                                $link = route('ard.leaves.show', ['leave' => $leaveId]);
                                            } elseif ($role === 'admin') {
                                                $link = route('admin.leaves.show', ['leave' => $leaveId]);
                                            } elseif ($isUser) {
                                                $targetStatus = \App\Models\Leave::find($leaveId)?->status;
                                                if ($targetStatus === 'approved') {
                                                    $link = route('leaves.pdf.view', ['leave' => $leaveId]);
                                                } elseif ($targetStatus === 'pending') {
                                                    $link = route('leaves.edit', ['leave' => $leaveId]);
                                                } else {
                                                    $link = route('leaves.index');
                                                }
                                            }
                                        }
                                    @endphp
                                    <li class="rounded-xl {{ $isUnread ? 'bg-indigo-50 ring-1 ring-indigo-100' : 'bg-white/70 ring-1 ring-gray-200' }} mb-2">
                                        @if ($link)
                                        <a href="{{ $link }}" class="flex items-start justify-between p-4 rounded-xl hover:bg-gray-50 transition" role="link">
                                        @else
                                        <div class="flex items-start justify-between p-4">
                                        @endif
                                            <div class="flex items-start gap-3">
                                                <div class="h-10 w-10 rounded-full bg-indigo-600 text-white flex items-center justify-center shrink-0">
                                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 22zm6-6v-5a6 6 0 1 0-12 0v5l-2 2v1h18v-1l-2-2z"/></svg>
                                                </div>
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <div class="text-sm font-semibold text-gray-900">{{ $title }}</div>
                                                        @if ($isUnread)
                                                            <span class="inline-flex items-center rounded-full bg-indigo-600 text-white text-[10px] px-2 py-0.5">NEW</span>
                                                        @endif
                                                    </div>
                                                    <div class="mt-1 text-sm text-gray-700">{{ $message }}</div>
                                                    <div class="mt-1 text-xs text-gray-500">{{ $n->created_at?->format('M d, Y g:i A') }}</div>
                                                </div>
                                            </div>
                                            @if (!$link)
                                            <div class="ml-4"></div>
                                            @endif
                                        @if ($link)
                                        </a>
                                        @else
                                        </div>
                                        @endif
                                    </li>
                                @empty
                                    <li class="rounded-xl bg-white/70 ring-1 ring-gray-200 p-6 text-sm text-gray-500">No notifications.</li>
                                @endforelse
                            </ul>
                            <div class="mt-2">
                                {{ $notifications->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div x-show="showMarkAllModal"
             x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4"
             style="display: none;">
            <div @click.outside="if(!markAllBusy) showMarkAllModal = false"
                 x-transition
                 class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                <h2 class="text-lg font-semibold text-gray-900">Mark all as read?</h2>
                <p class="mt-2 text-sm text-gray-600">This will mark all notifications as read.</p>
                <div class="mt-6 flex items-center justify-end gap-2">
                    <button type="button"
                            @click="showMarkAllModal = false"
                            :disabled="markAllBusy"
                            class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-60">
                        Cancel
                    </button>
                    <button type="button"
                            @click="confirmMarkAllRead()"
                            :disabled="markAllBusy"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60">
                        <span x-show="!markAllBusy">Confirm</span>
                        <span x-show="markAllBusy" style="display: none;">Processing...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
