<x-app-layout>
    <div class="py-0">
        <div class="px-0">
            <div class="grid grid-cols-1 lg:grid-cols-[256px_minmax(0,1fr)] {{ Auth::user()->role === 'hr' ? 'gap-0' : 'gap-6' }}">
                @include(Auth::user()->role === 'hr' ? 'hr.partials.sidebar' : 'admin.partials.sidebar')
                <section>
                    <div class="bg-white p-6 shadow sm:rounded-lg">
                        <form method="POST" action="{{ route('categories.update', $category) }}" class="space-y-4">
                            @csrf @method('PUT')
                            <div>
                                <x-input-label for="name" :value="__('Name')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" value="{{ old('name', $category->name) }}" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="description" :value="__('Description')" />
                                <x-text-input id="description" class="block mt-1 w-full" type="text" name="description" value="{{ old('description', $category->description) }}" />
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="vl_default_credits" :value="__('Vacation Leave')" />
                                    <x-text-input id="vl_default_credits" class="block mt-1 w-48" type="number" step="0.001" name="vl_default_credits" value="{{ old('vl_default_credits', $category->vl_default_credits) }}" />
                                    <x-input-error :messages="$errors->get('vl_default_credits')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="sl_default_credits" :value="__('Sick Leave')" />
                                    <x-text-input id="sl_default_credits" class="block mt-1 w-48" type="number" step="0.001" name="sl_default_credits" value="{{ old('sl_default_credits', $category->sl_default_credits) }}" />
                                    <x-input-error :messages="$errors->get('sl_default_credits')" class="mt-2" />
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <x-primary-button>Update</x-primary-button>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
