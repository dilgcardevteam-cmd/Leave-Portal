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
                    <div class="pt-4 pb-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <a href="{{ url()->previous() ?: ($isHr ? route('hr.index') : ($isUser ? route('dashboard') : ($isDc ? route('dc.index') : ($isArd ? route('ard.index') : ($isRd ? route('rd.index') : ($role === 'admin' ? route('admin.index') : url('/'))))))) }}"
                               onclick="if (history.length > 1) { history.back(); return false; }"
                               class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50">
                                <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.293 16.707a1 1 0 0 1-1.414 0l-6-6a1 1 0 0 1 0-1.414l6-6a1 1 0 1 1 1.414 1.414L7.414 9H18a1 1 0 1 1 0 2H7.414l4.879 4.879a1 1 0 0 1 0 1.414z" clip-rule="evenodd"/></svg>
                                Back
                            </a>
                            <h1 class="text-xl font-semibold text-gray-800">Notifications</h1>
                        </div>
                        <button type="button"
                                @click="showMarkAllModal = true"
                                class="inline-flex items-center px-3 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                            Mark all read
                        </button>
                    </div>

                    <div class="bg-white border border-gray-200 shadow sm:rounded-xl overflow-hidden">
                        <ul class="divide-y divide-gray-100">
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
                                        }
                                    }
                                @endphp
                                <li class="p-4 {{ $isUnread ? 'bg-indigo-50' : '' }}">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <div class="text-sm font-medium text-gray-900">{{ $title }}</div>
                                                @if ($isUnread)
                                                    <span class="inline-flex items-center rounded-full bg-indigo-600 text-white text-[10px] px-2 py-0.5">NEW</span>
                                                @endif
                                            </div>
                                            <div class="text-sm text-gray-600 mt-1">{{ $message }}</div>
                                            <div class="text-xs text-gray-400 mt-1">{{ $n->created_at?->format('Y-m-d H:i:s') }}</div>
                                        </div>
                                        @if ($link)
                                            <a href="{{ $link }}" class="ml-4 inline-flex items-center px-3 py-1.5 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50">Open</a>
                                        @endif
                                    </div>
                                </li>
                            @empty
                                <li class="p-6 text-sm text-gray-500">No notifications.</li>
                            @endforelse
                        </ul>
                        <div class="px-4 py-3 border-t border-gray-100">
                            {{ $notifications->links() }}
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

