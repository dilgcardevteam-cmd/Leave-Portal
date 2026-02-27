<x-app-layout>
    <div class="hr-shell min-h-[calc(100vh-4rem)] bg-[#f4f5f9]" x-data="{ sidebarCollapsed: false }">
        <div class="grid min-h-[calc(100vh-4rem)] grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] lg:transition-[grid-template-columns] lg:duration-200 lg:ease-out" :class="sidebarCollapsed ? 'lg:grid-cols-[88px_minmax(0,1fr)]' : 'lg:grid-cols-[280px_minmax(0,1fr)]'">
            @include('user.partials.sidebar')
            <section class="p-4 sm:p-6 lg:p-8">
                <div class="bg-white p-6 shadow sm:rounded-lg border border-gray-100">
                    @if (session('error'))
                        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('leaves.update', $leave) }}" class="space-y-4" id="leaveEditForm">
                        @csrf
                        @method('PUT')

                        <div class="border-b mb-4">
                            <nav class="flex gap-2" role="tablist" aria-label="Leave edit steps">
                                <button type="button" class="tab-trigger px-4 py-2 rounded-t-md bg-indigo-50 text-indigo-700 border border-b-0 border-indigo-200" data-tab="step1">Type of Leave</button>
                                <button type="button" class="tab-trigger px-4 py-2 rounded-t-md text-gray-600 hover:text-gray-800 border border-b-0 border-transparent" data-tab="step2">Details of Leave</button>
                                <button type="button" class="tab-trigger px-4 py-2 rounded-t-md text-gray-600 hover:text-gray-800 border border-b-0 border-transparent" data-tab="step3">Number of Working Days</button>
                                <button type="button" class="tab-trigger px-4 py-2 rounded-t-md text-gray-600 hover:text-gray-800 border border-b-0 border-transparent" data-tab="step4">Commutation</button>
                            </nav>
                        </div>

                        <div id="tab-step1" class="tab-panel">
                            <x-input-label :value="__('TYPE OF LEAVE TO BE AVAILED OF')" />
                            <input type="hidden" id="leave_category_id_hidden" name="leave_category_id" value="{{ old('leave_category_id', $leave->leave_category_id) }}" required>
                            <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2" id="leaveCategoryChoices">
                                @foreach ($categories as $category)
                                @php
                                    $n = mb_strtolower($category->name ?? '');
                                    if (str_contains($n, 'vacation')) {
                                        $credits = $category->vl_default_credits ?? $category->default_credits ?? 0;
                                    } elseif (str_contains($n, 'sick')) {
                                        $credits = $category->sl_default_credits ?? $category->default_credits ?? 0;
                                    } else {
                                        $credits = $category->default_credits ?? 0;
                                    }
                                @endphp
                                <label class="flex items-center gap-2 p-2 border rounded-md cursor-pointer hover:bg-gray-50">
                                    <input type="checkbox"
                                           class="lc-choice rounded text-indigo-600 focus:ring-indigo-500"
                                           value="{{ $category->id }}"
                                           data-credits="{{ $credits }}"
                                           @checked((string)old('leave_category_id', $leave->leave_category_id) === (string)$category->id)>
                                    <span class="text-sm text-gray-800">{{ $category->name }}</span>
                                </label>
                                @endforeach
                            </div>
                            <div id="categoryCreditsInfo" class="mt-2 text-sm text-gray-600 hidden">
                                Credits for selected leave type: <span id="categoryCreditsValue" class="font-medium">0.000</span>
                            </div>
                            <x-input-error :messages="$errors->get('leave_category_id')" class="mt-2" />

                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="start_date" :value="__('Start Date')" />
                                    <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" value="{{ old('start_date', $leave->start_date) }}" required />
                                    <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="end_date" :value="__('End Date')" />
                                    <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" value="{{ old('end_date', $leave->end_date) }}" required />
                                    <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                                </div>
                            </div>
                            <div class="mt-4 flex items-center justify-end gap-2">
                                <button type="button" id="toStep2" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Next</button>
                            </div>
                        </div>

                        <div id="tab-step2" class="tab-panel hidden">
                            <div>
                                <x-input-label for="reason" :value="__('Reason / Details')" />
                                <textarea id="reason" name="reason" rows="8" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('reason', $leave->reason) }}</textarea>
                                <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                            </div>
                            <div class="mt-4 flex items-center justify-between">
                                <button type="button" id="backToStep1" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">Back</button>
                                <button type="button" id="toStep3" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Next</button>
                            </div>
                        </div>

                        <div id="tab-step3" class="tab-panel hidden">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="applied_days_preview" :value="__('Number of Working Days Applied For')" />
                                    <x-text-input id="applied_days_preview" class="block mt-1 w-full" type="number" min="1" value="{{ old('applied_days_preview', $leave->days) }}" readonly />
                                    <p class="text-xs text-gray-500 mt-1">Auto-computed from Start Date and End Date.</p>
                                </div>
                                <div>
                                    <x-input-label for="inclusive_dates_preview" :value="__('Inclusive Dates')" />
                                    <x-text-input id="inclusive_dates_preview" class="block mt-1 w-full" type="text" readonly />
                                </div>
                            </div>
                            <div class="mt-4 flex items-center justify-between">
                                <button type="button" id="backToStep2" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">Back</button>
                                <button type="button" id="toStep4" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Next</button>
                            </div>
                        </div>

                        <div id="tab-step4" class="tab-panel hidden">
                            <div class="space-y-3">
                                <div class="text-sm font-medium text-gray-700">Commutation</div>
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" class="commutation-box rounded text-indigo-600 focus:ring-indigo-500" value="not_requested">
                                    <span class="text-sm text-gray-700">Not Requested</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" class="commutation-box rounded text-indigo-600 focus:ring-indigo-500" value="requested">
                                    <span class="text-sm text-gray-700">Requested</span>
                                </label>
                                <p class="text-xs text-gray-500">Commutation is shown for consistency with New Request layout.</p>
                            </div>

                            <div class="mt-6 flex items-center justify-between">
                                <button type="button" id="backToStep3" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">Back</button>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('leaves.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">Cancel</a>
                                    <x-primary-button>Save Changes</x-primary-button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>

    <script>
    (function(){
        const form = document.getElementById('leaveEditForm');
        const choices = document.querySelectorAll('#leaveCategoryChoices .lc-choice');
        const hidden = document.getElementById('leave_category_id_hidden');
        const creditInfo = document.getElementById('categoryCreditsInfo');
        const creditVal = document.getElementById('categoryCreditsValue');
        const tabTriggers = document.querySelectorAll('.tab-trigger');
        const panels = {
            step1: document.getElementById('tab-step1'),
            step2: document.getElementById('tab-step2'),
            step3: document.getElementById('tab-step3'),
            step4: document.getElementById('tab-step4'),
        };
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');
        const appliedPreview = document.getElementById('applied_days_preview');
        const inclusivePreview = document.getElementById('inclusive_dates_preview');
        const commBoxes = document.querySelectorAll('.commutation-box');

        function setActive(tab){
            tabTriggers.forEach((btn)=>{
                const on = btn.getAttribute('data-tab') === tab;
                btn.classList.toggle('bg-indigo-50', on);
                btn.classList.toggle('text-indigo-700', on);
                btn.classList.toggle('border', on);
                btn.classList.toggle('border-b-0', on);
                btn.classList.toggle('border-indigo-200', on);
                btn.classList.toggle('text-gray-600', !on);
            });
            Object.entries(panels).forEach(([key, panel])=>{
                if (!panel) return;
                panel.classList.toggle('hidden', key !== tab);
            });
        }

        function updateCategoryUI(){
            const checked = Array.from(choices).find(c => c.checked);
            hidden.value = checked ? checked.value : '';
            if (creditInfo && creditVal) {
                if (checked) {
                    const credits = parseFloat(checked.getAttribute('data-credits') || '0') || 0;
                    creditVal.textContent = credits.toFixed(3);
                    creditInfo.classList.remove('hidden');
                } else {
                    creditInfo.classList.add('hidden');
                }
            }
        }

        function computeDays(){
            if (!startDate || !endDate || !appliedPreview || !inclusivePreview) return;
            if (!startDate.value || !endDate.value) {
                appliedPreview.value = '';
                inclusivePreview.value = '';
                return;
            }
            const sd = new Date(startDate.value + 'T00:00:00');
            const ed = new Date(endDate.value + 'T00:00:00');
            if (isNaN(sd) || isNaN(ed) || ed < sd) {
                appliedPreview.value = '';
                inclusivePreview.value = '';
                return;
            }
            let days = 0;
            const cur = new Date(sd);
            while (cur <= ed) {
                const d = cur.getDay();
                if (d !== 0 && d !== 6) days++;
                cur.setDate(cur.getDate() + 1);
            }
            appliedPreview.value = String(days);
            const fmt = (d) => d.toLocaleDateString('en-US', { month:'long', day:'numeric', year:'numeric' });
            inclusivePreview.value = `${fmt(sd)} to ${fmt(ed)}`;
        }

        choices.forEach(box => {
            box.addEventListener('change', function(){
                if (this.checked) {
                    choices.forEach(b => { if (b !== this) b.checked = false; });
                }
                updateCategoryUI();
            });
        });

        if (startDate) startDate.addEventListener('change', computeDays);
        if (endDate) endDate.addEventListener('change', computeDays);

        const bindNav = (id, target) => {
            const el = document.getElementById(id);
            if (!el) return;
            el.addEventListener('click', () => setActive(target));
        };
        bindNav('toStep2', 'step2');
        bindNav('backToStep1', 'step1');
        bindNav('toStep3', 'step3');
        bindNav('backToStep2', 'step2');
        bindNav('toStep4', 'step4');
        bindNav('backToStep3', 'step3');

        tabTriggers.forEach(btn => {
            btn.addEventListener('click', () => setActive(btn.getAttribute('data-tab')));
        });

        commBoxes.forEach(cb => {
            cb.addEventListener('change', function(){
                if (this.checked) {
                    commBoxes.forEach(other => { if (other !== this) other.checked = false; });
                }
            });
        });

        updateCategoryUI();
        computeDays();
        setActive('step1');

        if (form) {
            form.addEventListener('submit', (e) => {
                if (!hidden.value) {
                    alert('Please select a leave type.');
                    setActive('step1');
                    e.preventDefault();
                }
            });
        }
    })();
    </script>
</x-app-layout>

