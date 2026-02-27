<x-app-layout>
    <div class="py-0">
        <div class="px-0">
            <div class="grid grid-cols-1 lg:grid-cols-[256px_minmax(0,1fr)] gap-6 h-[calc(100vh-4rem)] overflow-hidden">
                @include('admin.partials.sidebar')
                <section class="h-full overflow-auto">
                    <h1 class="text-2xl sm:text-3xl font-semibold text-gray-700">Help & Support</h1>
                    <div class="mt-6 bg-white p-6 shadow sm:rounded-lg border border-gray-100">
                        <div class="text-gray-700">For assistance with user management, leave requests, or categories, contact the system administrator or refer to your internal support guide.</div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
