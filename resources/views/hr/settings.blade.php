<x-app-layout>
    <div class="hr-shell min-h-[calc(100vh-4rem)] bg-[#f4f5f9]" x-data="{ sidebarCollapsed: false }">
        <div class="grid min-h-[calc(100vh-4rem)] grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] lg:transition-[grid-template-columns] lg:duration-200 lg:ease-out" :class="sidebarCollapsed ? 'lg:grid-cols-[88px_minmax(0,1fr)]' : 'lg:grid-cols-[280px_minmax(0,1fr)]'">
            @include('hr.partials.sidebar')

            <section class="space-y-6 p-4 sm:p-6 lg:p-8">
                @if (session('status'))
                    <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
                @endif

                <div>
                    <h1 class="text-4xl font-semibold tracking-tight text-slate-900 sm:text-5xl">HR Settings</h1>
                    <p class="mt-2 text-lg text-slate-700">Manage user credits and signatories for leave processing.</p>
                </div>

                

                @if(($active ?? request('view')) === 'credit')
                <article id="credit-management" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-semibold text-slate-900">Credit Management</h2>
                            <p class="text-sm text-slate-600">Name, position, salary, station, and credits.</p>
                        </div>
                        <form method="GET" action="{{ route('hr.settings') }}" class="w-full sm:w-80">
                            <input
                                type="text"
                                name="q"
                                value="{{ request('q') }}"
                                placeholder="Search name, email, position, station..."
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-400 focus:ring-indigo-400"
                            />
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50 text-slate-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Position</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Salary</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Station</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Vacation Leave</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Sick Leave</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Credits</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse($users as $user)
                                    @php
                                        $vl = (float)($user->credits->vl_total ?? 0);
                                        $sl = (float)($user->credits->sl_total ?? 0);
                                        $credits = (float)($user->credits->credits_total ?? ($vl + $sl));
                                    @endphp
                                    <tr class="hover:bg-slate-50" x-data="{ openEdit:false, openMonthly:false }">
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-slate-900">{{ $user->display_name }}</div>
                                            <div class="text-xs text-slate-500">{{ $user->email }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-slate-700">{{ $user->position ?: '-' }}</td>
                                        <td class="px-4 py-3 text-slate-700">{{ isset($user->salary) && $user->salary !== null ? 'P'.$user->salary : '-' }}</td>
                                        <td class="px-4 py-3 text-slate-700">{{ $user->province_office ?: '-' }}</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                                {{ number_format($vl, 3) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                                {{ number_format($sl, 3) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                                {{ number_format($credits, 3) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right align-top">
                                            <div class="flex flex-col items-end gap-2">
                                                <button type="button" @click="openMonthly = true" class="inline-flex w-32 items-center justify-center rounded-lg bg-emerald-600 px-3 py-1.5 text-sm font-medium text-white transition hover:bg-emerald-700">
                                                    Monthly Credit
                                                </button>
                                                <button type="button" @click="openEdit = true" class="inline-flex w-32 items-center justify-center rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white transition hover:bg-indigo-700">
                                                    Edit
                                                </button>
                                            </div>
                                            <div
                                                x-show="openEdit"
                                                x-cloak
                                                x-transition.opacity
                                                @keydown.escape.window="openEdit = false"
                                                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                                            >
                                                <div class="absolute inset-0 bg-slate-900/50" @click="openEdit = false"></div>
                                                <form
                                                    method="POST"
                                                    action="{{ route('hr.settings.credits.update', $user) }}"
                                                    class="relative z-10 w-full max-w-3xl md:max-w-4xl xl:max-w-5xl rounded-2xl bg-white p-6 shadow-2xl"
                                                >
                                                    @csrf
                                                    <input type="hidden" name="q" value="{{ request('q') }}">

                                                    <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-4">
                                                        <div>
                                                            <h3 class="text-left text-lg font-semibold text-slate-900">Edit Credit Management</h3>
                                                            <p class="mt-1 text-sm text-slate-500">
                                                                Update this employee&apos;s information and available leave credits.
                                                            </p>
                                                        </div>
                                                        <button
                                                            type="button"
                                                            @click="openEdit = false"
                                                            class="text-slate-400 transition hover:text-slate-600"
                                                        >
                                                            <span class="sr-only">Close</span>
                                                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                    </div>

                                                    <div class="mt-5 grid grid-cols-1 gap-4 text-left sm:grid-cols-2">
                                                        <div class="space-y-1">
                                                            <label for="name-{{ $user->id }}" class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                                                Full name
                                                            </label>
                                                            <input
                                                                id="name-{{ $user->id }}"
                                                                type="text"
                                                                name="name"
                                                                value="{{ old('name', $user->name) }}"
                                                                placeholder="Enter full name"
                                                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-400 focus:ring-indigo-400"
                                                            >
                                                        </div>

                                                        <div class="space-y-1">
                                                            <label for="position-{{ $user->id }}" class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                                                Position
                                                            </label>
                                                            <input
                                                                id="position-{{ $user->id }}"
                                                                type="text"
                                                                name="position"
                                                                value="{{ old('position', $user->position) }}"
                                                                placeholder="e.g. Administrative Officer"
                                                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-400 focus:ring-indigo-400"
                                                            >
                                                        </div>

                                                        <div class="space-y-1">
                                                            <label for="salary-{{ $user->id }}" class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                                                Monthly salary (₱)
                                                            </label>
                                                            <input
                                                                id="salary-{{ $user->id }}"
                                                                type="number"
                                                                step="0.01"
                                                                min="0"
                                                                name="salary"
                                                                value="{{ old('salary', $user->salary ?? '') }}"
                                                                placeholder="0.00"
                                                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-400 focus:ring-indigo-400"
                                                            >
                                                        </div>

                                                        <div class="space-y-1">
                                                            <label for="division-{{ $user->id }}" class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                                                Station / Office
                                                            </label>
                                                            <input
                                                                id="division-{{ $user->id }}"
                                                                type="text"
                                                                name="division"
                                                                value="{{ old('division', $user->province_office) }}"
                                                                placeholder="e.g. HR Station"
                                                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-400 focus:ring-indigo-400"
                                                            >
                                                        </div>

                                                        <div class="space-y-1 sm:col-span-2">
                                                            <label for="credits-{{ $user->id }}" class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                                                Total leave credits
                                                            </label>
                                                            <input
                                                                id="credits-{{ $user->id }}"
                                                                type="number"
                                                                step="0.001"
                                                                min="0"
                                                                name="credits"
                                                                value="{{ old('credits', $credits) }}"
                                                                placeholder="0.000"
                                                                class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-400 focus:ring-indigo-400"
                                                            >
                                                            <p class="mt-1 text-xs text-slate-500">
                                                                Enter the combined balance of all leave credits for this employee.
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="mt-6 flex items-center justify-end gap-2 border-t border-slate-100 pt-4">
                                                        <button
                                                            type="button"
                                                            @click="openEdit = false"
                                                            class="inline-flex items-center rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                                                        >
                                                            Cancel
                                                        </button>
                                                        <button
                                                            type="submit"
                                                            class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-1.5 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700"
                                                        >
                                                            Save changes
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                            <div
                                                x-show="openMonthly"
                                                x-cloak
                                                x-transition.opacity
                                                @keydown.escape.window="openMonthly = false"
                                                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                                            >
                                                <div class="absolute inset-0 bg-slate-900/50" @click="openMonthly = false"></div>
                                                <form
                                                    method="POST"
                                                    action="{{ route('hr.settings.credits.monthly', $user) }}"
                                                    class="relative z-10 w-full max-w-xl rounded-2xl bg-white p-6 shadow-2xl"
                                                >
                                                    @csrf
                                                    <input type="hidden" name="q" value="{{ request('q') }}">

                                                    <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-4">
                                                        <div>
                                                            <h3 class="text-left text-lg font-semibold text-slate-900">Monthly Credit</h3>
                                                            <p class="mt-1 text-sm text-slate-500">
                                                                Add monthly leave credits for {{ $user->display_name }}.
                                                            </p>
                                                        </div>
                                                        <button
                                                            type="button"
                                                            @click="openMonthly = false"
                                                            class="text-slate-400 transition hover:text-slate-600"
                                                        >
                                                            <span class="sr-only">Close</span>
                                                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                    </div>

                                                    <div class="mt-5 grid grid-cols-1 gap-4 text-left sm:grid-cols-2">
                                                        <div class="space-y-1">
                                                            <label for="vl-add-{{ $user->id }}" class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                                                Vacation Leave
                                                            </label>
                                                            <input
                                                                id="vl-add-{{ $user->id }}"
                                                                type="number"
                                                                step="0.001"
                                                                name="vl_add"
                                                                value="1.25"
                                                                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                                            />
                                                        </div>
                                                        <div class="space-y-1">
                                                            <label for="sl-add-{{ $user->id }}" class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                                                Sick Leave
                                                            </label>
                                                            <input
                                                                id="sl-add-{{ $user->id }}"
                                                                type="number"
                                                                step="0.001"
                                                                name="sl_add"
                                                                value="1.25"
                                                                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                                            />
                                                        </div>
                                                    </div>

                                                    <div class="mt-6 flex items-center justify-end gap-2">
                                                        <button type="button" @click="openMonthly = false" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">
                                                            Cancel
                                                        </button>
                                                        <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">
                                                            Apply Credits
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">No users found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $users->fragment('credit-management')->links() }}
                    </div>
                </article>
                @endif

                @if(($active ?? request('view')) === 'signatories')
                <article id="signatories" class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="mb-4">
                        <h2 class="text-2xl font-semibold text-slate-900">Signatories</h2>
                        <p class="text-sm text-slate-600">Current personnel used as signatories in the leave workflow.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        @forelse($signatories as $person)
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4" x-data="{ openEdit:false }">
                                <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ strtoupper($person->role) }}</div>
                                <div class="mt-1 text-lg font-semibold text-slate-900">{{ $person->display_name }}</div>
                                <div class="text-sm text-slate-700">{{ $person->position ?: '-' }}</div>
                                <div class="text-xs text-slate-500">{{ $person->email }}</div>
                                <div class="mt-3">
                                    <button type="button" @click="openEdit = true" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm ring-1 ring-indigo-600/20 transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M4 13.5V16h2.5l7.35-7.35-2.5-2.5L4 13.5zM15.85 6.65a1 1 0 000-1.41l-1.09-1.09a1 1 0 00-1.41 0l-1.25 1.25 2.5 2.5 1.25-1.25z"/></svg>
                                        Edit
                                    </button>
                                </div>
                                <div
                                    x-show="openEdit"
                                    x-cloak
                                    x-transition.opacity
                                    @keydown.escape.window="openEdit = false"
                                    class="fixed inset-0 z-50 flex items-center justify-center p-4"
                                >
                                    <div class="absolute inset-0 bg-slate-900/50" @click="openEdit = false"></div>
                                    <form
                                        method="POST"
                                        enctype="multipart/form-data"
                                        action="{{ route('hr.settings.signatory.update', $person) }}"
                                        class="relative z-10 w-full max-w-xl rounded-2xl bg-white p-6 shadow-2xl"
                                    >
                                        @csrf

                                        <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-4">
                                            <div>
                                                <h3 class="text-lg font-semibold text-slate-900">Edit Signatory</h3>
                                                <p class="mt-1 text-sm text-slate-500">
                                                    Update the details used when this role appears as a signatory.
                                                </p>
                                            </div>
                                            <button
                                                type="button"
                                                @click="openEdit = false"
                                                class="text-slate-400 transition hover:text-slate-600"
                                            >
                                                <span class="sr-only">Close</span>
                                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>

                                        <div class="mt-5 grid grid-cols-1 gap-4 text-left sm:grid-cols-2">
                                            <div class="space-y-1 sm:col-span-2">
                                                <label for="signatory-name-{{ $person->id }}" class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                                    Full name
                                                </label>
                                                <input
                                                    id="signatory-name-{{ $person->id }}"
                                                    type="text"
                                                    name="name"
                                                    value="{{ old('name', $person->name) }}"
                                                    placeholder="Enter full name"
                                                    class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-400 focus:ring-indigo-400"
                                                >
                                            </div>

                                            <div class="space-y-1">
                                                <label for="signatory-position-{{ $person->id }}" class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                                    Position / Title
                                                </label>
                                                <input
                                                    id="signatory-position-{{ $person->id }}"
                                                    type="text"
                                                    name="position"
                                                    value="{{ old('position', $person->position) }}"
                                                    placeholder="e.g. HR Manager"
                                                    class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-400 focus:ring-indigo-400"
                                                >
                                            </div>

                                            <div class="space-y-1">
                                                <label for="signatory-division-{{ $person->id }}" class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                                    Station / Office
                                                </label>
                                                <input
                                                    id="signatory-division-{{ $person->id }}"
                                                    type="text"
                                                    name="division"
                                                    value="{{ old('division', $person->province_office) }}"
                                                    placeholder="e.g. Regional Office"
                                                    class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-400 focus:ring-indigo-400"
                                                >
                                            </div>

                                            <div class="space-y-1">
                                                <label class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                                    Signature Image
                                                </label>
                                                <div class="flex items-center gap-4" x-data="{ preview: null }">
                                                    <input
                                                        id="signatory-signature-{{ $person->id }}"
                                                        type="file"
                                                        name="signature"
                                                        accept="image/*"
                                                        class="sr-only"
                                                        @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                                                    >
                                                    <button
                                                        type="button"
                                                        @click="document.getElementById('signatory-signature-{{ $person->id }}').click()"
                                                        class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-400"
                                                    >
                                                        Upload Signature
                                                    </button>
                                                    <img x-cloak x-show="preview" :src="preview" alt="Preview" class="h-14 w-auto rounded border border-slate-200 bg-white p-1">
                                                    @if (!empty($person->signature_path))
                                                        <img x-cloak x-show="!preview" src="{{ asset('storage/'.$person->signature_path) }}" alt="Current signature" class="h-14 w-auto rounded border border-slate-200 bg-white p-1">
                                                    @endif
                                                </div>
                                                <p class="mt-1 text-xs text-slate-500">
                                                    Accepted: PNG, JPG, JPEG, WEBP. Max size: 2MB.
                                                </p>
                                            </div>
                                        </div>

                                        <div class="mt-6 flex items-center justify-end gap-2 border-t border-slate-100 pt-4">
                                            <button
                                                type="button"
                                                @click="openEdit = false"
                                                class="inline-flex items-center rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                                            >
                                                Cancel
                                            </button>
                                            <button
                                                type="submit"
                                                class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-1.5 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700"
                                            >
                                                Save changes
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-xl border border-slate-200 p-6 text-sm text-slate-500">No signatories configured.</div>
                        @endforelse
                    </div>
                    <div class="mt-4">
                        {{ $signatories->fragment('signatories')->links() }}
                    </div>
                </article>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
