<x-app-layout>
    <div class="hr-shell min-h-[calc(100vh-4rem)] bg-[#f4f5f9]" x-data="{ sidebarCollapsed: false }">
            <div class="grid min-h-[calc(100vh-4rem)] grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] lg:transition-[grid-template-columns] lg:duration-200 lg:ease-out" :class="sidebarCollapsed ? 'lg:grid-cols-[88px_minmax(0,1fr)]' : 'lg:grid-cols-[280px_minmax(0,1fr)]'">
                @include('ard.partials.sidebar')
                <section class="p-4 sm:p-6 lg:p-8">
                    <div class="max-w-3xl mx-auto p-6 bg-white shadow sm:rounded-xl border border-gray-200">
                        <h1 class="text-2xl font-semibold text-gray-800">Help & Support</h1>
                        <p class="mt-2 text-gray-600">This section provides guidance for Assistant Regional Director users.</p>
                        <ul class="mt-4 list-disc list-inside text-gray-700 space-y-2">
                            <li>Use the Leave Requests page to review submissions.</li>
                            <li>Navigate tabs to view request details and leave credits.</li>
                            <li>Finalize the request in the “Approved For / Disapproved Due To” tab.</li>
                        </ul>
                    </div>
                </section>
            </div>
    </div>
</x-app-layout>

