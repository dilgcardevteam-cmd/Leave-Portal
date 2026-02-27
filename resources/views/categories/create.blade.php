<x-app-layout>
    @if (Auth::user()->role === 'hr')
    <div class="hr-shell min-h-[calc(100vh-4rem)] bg-[#f4f5f9]" x-data="{ sidebarCollapsed: false }">
        <div class="grid min-h-[calc(100vh-4rem)] grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] lg:transition-[grid-template-columns] lg:duration-200 lg:ease-out" :class="sidebarCollapsed ? 'lg:grid-cols-[88px_minmax(0,1fr)]' : 'lg:grid-cols-[280px_minmax(0,1fr)]'">
            @include('hr.partials.sidebar')
            <section class="p-4 sm:p-6 lg:p-8">
                <div class="bg-white p-6 shadow rounded-xl border border-gray-200">
                    <form method="POST" action="{{ route('categories.store') }}" class="space-y-4" x-data="{ rows: [0] }">
                        @csrf
                        <template x-for="(r, idx) in rows" :key="r">
                            <div class="rounded-lg border border-gray-200 p-4 bg-white">
                                <div class="flex items-start gap-2">
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700" :for="'name_'+idx">Type of Leave</label>
                                        <input :id="'name_'+idx" type="text" name="names[]" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required placeholder="Enter type e.g., Vacation Leave">
                                    </div>
                                    <div class="ms-2 flex flex-col gap-2">
                                        <button type="button" class="inline-flex items-center justify-center h-10 w-10 rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200" @click="rows.push(Date.now())" title="Add another">
                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M11 11V5h2v6h6v2h-6v6h-2v-6H5v-2z"/></svg>
                                        </button>
                                        <button type="button" class="inline-flex items-center justify-center h-10 w-10 rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200" @click="rows.splice(idx,1)" x-show="rows.length>1" title="Remove">
                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13H5v-2h14v2z"/></svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="block text-sm font-medium text-gray-700" :for="'desc_'+idx">Description</label>
                                    <input :id="'desc_'+idx" type="text" name="descriptions[]" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Optional policy reference or notes">
                                </div>
                                <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700" for="vl_default_credits">Vacation Leave</label>
                                        <input id="vl_default_credits" type="number" step="0.001" name="vl_default_credits" class="block mt-1 w-48 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., 0.000">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700" for="sl_default_credits">Sick Leave</label>
                                        <input id="sl_default_credits" type="number" step="0.001" name="sl_default_credits" class="block mt-1 w-48 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., 0.000">
                                    </div>
                                    <p class="text-xs text-gray-500 sm:col-span-2">Shown to users when requesting this leave.</p>
                                </div>
                            </div>
                        </template>
                        <div class="flex justify-end">
                            <x-primary-button>Save</x-primary-button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
    @else
    <div class="py-2">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-[256px_minmax(0,1fr)] gap-6">
                @include('admin.partials.sidebar')
                <section>
                    <div class="bg-white p-6 shadow sm:rounded-lg">
                        <form method="POST" action="{{ route('categories.store') }}" class="space-y-4" x-data="{ rows: [0] }">
                            @csrf
                            <template x-for="(r, idx) in rows" :key="r">
                                <div class="rounded-lg border border-gray-200 p-4 bg-white">
                                    <div class="flex items-start gap-2">
                                        <div class="flex-1">
                                            <label class="block text-sm font-medium text-gray-700" :for="'name_'+idx">Type of Leave</label>
                                            <input :id="'name_'+idx" type="text" name="names[]" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required placeholder="Enter type e.g., Vacation Leave">
                                        </div>
                                        <div class="ms-2 flex flex-col gap-2">
                                            <button type="button" class="inline-flex items-center justify-center h-10 w-10 rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200" @click="rows.push(Date.now())" title="Add another">
                                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M11 11V5h2v6h6v2h-6v6h-2v-6H5v-2z"/></svg>
                                            </button>
                                            <button type="button" class="inline-flex items-center justify-center h-10 w-10 rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200" @click="rows.splice(idx,1)" x-show="rows.length>1" title="Remove">
                                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13H5v-2h14v2z"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <label class="block text-sm font-medium text-gray-700" :for="'desc_'+idx">Description</label>
                                        <input :id="'desc_'+idx" type="text" name="descriptions[]" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Optional policy reference or notes">
                                    </div>
                                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700" for="vl_default_credits">Vacation Leave</label>
                                            <input id="vl_default_credits" type="number" step="0.001" name="vl_default_credits" class="block mt-1 w-48 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., 0.000">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700" for="sl_default_credits">Sick Leave</label>
                                            <input id="sl_default_credits" type="number" step="0.001" name="sl_default_credits" class="block mt-1 w-48 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., 0.000">
                                        </div>
                                        <p class="text-xs text-gray-500 sm:col-span-2">Shown to users when requesting this leave.</p>
                                    </div>
                                </div>
                            </template>
                            <div class="flex justify-end">
                                <x-primary-button>Save</x-primary-button>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
    @endif
</x-app-layout>

