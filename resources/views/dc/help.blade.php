<x-app-layout>
    <div class="hr-shell min-h-[calc(100vh-4rem)] bg-[#f4f5f9]" x-data="{ sidebarCollapsed: false }">
            <div class="grid min-h-[calc(100vh-4rem)] grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] lg:transition-[grid-template-columns] lg:duration-200 lg:ease-out" :class="sidebarCollapsed ? 'lg:grid-cols-[88px_minmax(0,1fr)]' : 'lg:grid-cols-[280px_minmax(0,1fr)]'">
                @include('dc.partials.sidebar')
                <section class="p-4 sm:p-6 lg:p-8">
                    <h1 class="text-2xl sm:text-3xl font-semibold text-gray-700">Help & Support</h1>
                    <div class="mt-6 bg-white p-6 shadow sm:rounded-lg border border-gray-100">
                        <div class="text-gray-700">Guidance for submitting recommendations and finalizing DC decisions is available here. For system issues, contact your support lead.</div>
                    </div>
                </section>
            </div>
    </div>
</x-app-layout>

