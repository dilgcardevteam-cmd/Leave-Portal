<x-app-layout>
    <div class="hr-shell min-h-[calc(100vh-4rem)] bg-[#f4f5f9]" x-data="{ sidebarCollapsed: false }">
        <div class="grid min-h-[calc(100vh-4rem)] grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] lg:transition-[grid-template-columns] lg:duration-200 lg:ease-out" :class="sidebarCollapsed ? 'lg:grid-cols-[88px_minmax(0,1fr)]' : 'lg:grid-cols-[280px_minmax(0,1fr)]'">
            @include('hr.partials.sidebar')

            <section class="space-y-6 p-4 sm:p-6 lg:p-8">
                <div>
                    <h1 class="text-4xl font-semibold tracking-tight text-slate-900 sm:text-5xl">HR Dashboard</h1>
                    <p class="mt-2 text-lg text-slate-700">Welcome, {{ Auth::user()->display_name }}. Monitor leave activity and manage categories.</p>
                    <div class="mt-5 flex flex-wrap gap-3">
                        <a href="{{ route('hr.leaves') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-[#7a4bff] to-[#2f80ed] px-5 py-3 text-lg font-semibold text-white shadow-md transition hover:brightness-95">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 5h18v4H3V5zm0 6h10v8H3v-8zm12 0h6v8h-6v-8z"/></svg>
                            Go to Leaves
                        </a>
                        <a href="{{ route('categories.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-3 text-lg font-semibold text-slate-900 shadow-sm transition hover:bg-slate-50">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4 6h16v2H4V6zm0 5h16v2H4v-2zm0 5h10v2H4v-2z"/></svg>
                            Manage Categories
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                    <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 1a11 11 0 1011 11A11 11 0 0012 1zm1 11.41l3.29 3.3-1.42 1.41L11 13V7h2z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-wide text-slate-700">Total Requests</p>
                                <p class="text-5xl font-semibold leading-none text-slate-900">{{ $stats['requests'] ?? 0 }}</p>
                            </div>
                        </div>
                    </article>

                    <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-yellow-50 text-yellow-500">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="7"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-wide text-slate-700">Pending</p>
                                <p class="text-5xl font-semibold leading-none text-slate-900">{{ $stats['pending'] ?? 0 }}</p>
                            </div>
                        </div>
                    </article>

                    <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-green-50 text-green-600">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M9 16.17L4.83 12 3.41 13.41 9 19l12-12-1.41-1.41z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-wide text-slate-700">Approved</p>
                                <p class="text-5xl font-semibold leading-none text-slate-900">{{ $stats['approved'] ?? 0 }}</p>
                            </div>
                        </div>
                    </article>

                    <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-center gap-4">
                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-50 text-rose-500">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="7"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold uppercase tracking-wide text-slate-700">Rejected</p>
                                <p class="text-5xl font-semibold leading-none text-slate-900">{{ $stats['rejected'] ?? 0 }}</p>
                            </div>
                        </div>
                    </article>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>

