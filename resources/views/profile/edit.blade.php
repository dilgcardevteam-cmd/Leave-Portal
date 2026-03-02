<x-app-layout>
    <div class="hr-shell min-h-[calc(100vh-4rem)] bg-[#f3f4f7]" x-data="{ sidebarCollapsed: false }">
        <div class="grid min-h-[calc(100vh-4rem)] grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] lg:transition-[grid-template-columns] lg:duration-200 lg:ease-out" :class="sidebarCollapsed ? 'lg:grid-cols-[88px_minmax(0,1fr)]' : 'lg:grid-cols-[280px_minmax(0,1fr)]'">
            @include('user.partials.sidebar')
            <section class="p-6 sm:p-8 lg:p-10">
                <div class="max-w-5xl">
                    <h1 class="text-4xl font-semibold tracking-tight text-gray-900">Profile</h1>
                    <div class="mt-1 text-sm text-gray-600">Manage your personal information and account security.</div>

                    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <div class="glass p-4 sm:p-6">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                        <div class="glass p-4 sm:p-6">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <div class="mt-6 glass p-4 sm:p-6">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
