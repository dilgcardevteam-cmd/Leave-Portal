<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="space-y-6">
            <div>
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Personal Information</h3>
                <div class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="first_name" :value="__('First Name')" />
                        <x-text-input id="first_name" class="block mt-1 w-full" type="text" name="first_name" :value="old('first_name')" required autofocus />
                        <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="middle_name" :value="__('Middle Name (Opt)')" />
                        <x-text-input id="middle_name" class="block mt-1 w-full" type="text" name="middle_name" :value="old('middle_name')" />
                        <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="last_name" :value="__('Last Name')" />
                        <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name" :value="old('last_name')" required />
                        <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                    </div>
                    <div class="md:col-span-2">
                        <x-input-label for="mobile_number" :value="__('Mobile Number')" />
                        <x-text-input id="mobile_number" class="block mt-1 w-full" type="text" name="mobile_number" :value="old('mobile_number')" />
                        <x-input-error :messages="$errors->get('mobile_number')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="sex" :value="__('Sex')" />
                        <select id="sex" name="sex" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select</option>
                            <option value="Male" @selected(old('sex')==='Male')>Male</option>
                            <option value="Female" @selected(old('sex')==='Female')>Female</option>
                        </select>
                        <x-input-error :messages="$errors->get('sex')" class="mt-2" />
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Job Information</h3>
                <div class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="id_no" :value="__('ID No.')" />
                        <x-text-input id="id_no" class="block mt-1 w-full" type="text" name="id_no" :value="old('id_no')" />
                        <x-input-error :messages="$errors->get('id_no')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="region" :value="__('Region')" />
                        <x-text-input id="region" class="block mt-1 w-full" type="text" name="region" :value="old('region')" />
                        <x-input-error :messages="$errors->get('region')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="province_office" :value="__('Office/Department')" />
                        <x-text-input id="province_office" class="block mt-1 w-full" type="text" name="province_office" :value="old('province_office')" />
                        <x-input-error :messages="$errors->get('province_office')" class="mt-2" />
                    </div>
                    <div class="md:col-span-3">
                        <x-input-label for="position" :value="__('Position')" />
                        <x-text-input id="position" class="block mt-1 w-full" type="text" name="position" :value="old('position')" />
                        <x-input-error :messages="$errors->get('position')" class="mt-2" />
                    </div>
                </div>
            </div>

        <!-- Email Address -->
        <div class="mt-4">
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-2">Login Credentials</h3>
            <x-input-label for="email" :value="__('Email Address')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-900 hover:text-black rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4 rounded-full px-6 py-2 bg-gray-900 hover:bg-gray-800">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
