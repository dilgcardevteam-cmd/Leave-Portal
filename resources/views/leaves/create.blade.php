<x-app-layout>
    <div class="hr-shell min-h-[calc(100vh-4rem)] bg-[#f4f5f9]" x-data="{ sidebarCollapsed: false }">
        <div class="grid min-h-[calc(100vh-4rem)] grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] lg:transition-[grid-template-columns] lg:duration-200 lg:ease-out" :class="sidebarCollapsed ? 'lg:grid-cols-[88px_minmax(0,1fr)]' : 'lg:grid-cols-[280px_minmax(0,1fr)]'">
            @include('user.partials.sidebar')
            <section class="p-4 sm:p-6 lg:p-8">
            <div class="bg-white p-6 shadow sm:rounded-lg">
                @if (session('error'))
                    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
                @endif
                <form method="POST" action="{{ route('leaves.store') }}" class="space-y-4" id="leaveCreateForm">
                    @csrf
                    <!-- Tabs Header -->
                    <div class="border-b mb-4">
                        <nav class="flex gap-2" role="tablist" aria-label="Leave creation steps">
                            <button type="button" class="tab-trigger px-4 py-2 rounded-t-md bg-indigo-50 text-indigo-700 border border-b-0 border-indigo-200" data-tab="step1">Type of Leave</button>
                            <button type="button" class="tab-trigger px-4 py-2 rounded-t-md bg-white text-gray-600 hover:text-gray-800 border border-b-0 border-transparent" data-tab="step2">Details of Leave</button>
                            <button type="button" class="tab-trigger px-4 py-2 rounded-t-md bg-white text-gray-600 hover:text-gray-800 border border-b-0 border-transparent" data-tab="step3">Number of Working Days</button>
                            <button type="button" class="tab-trigger px-4 py-2 rounded-t-md bg-white text-gray-600 hover:text-gray-800 border border-b-0 border-transparent" data-tab="step4">Commutation</button>
                        </nav>
                    </div>

                    <!-- Step 1: Type of Leave -->
                    <div id="tab-step1" class="tab-panel">
                        <x-input-label :value="__('TYPE OF LEAVE TO BE AVAILED OF')" />
                        <input type="hidden" id="leave_category_id_hidden" name="leave_category_id" value="{{ old('leave_category_id') }}" required>
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
                                <input type="checkbox" class="lc-choice rounded text-indigo-600 focus:ring-indigo-500" value="{{ $category->id }}" data-credits="{{ $credits }}" data-name="{{ $category->name }}">
                                <span class="text-sm text-gray-800">{{ $category->name }}</span>
                            </label>
                            @endforeach
                            <label class="flex items-center gap-2 p-2 border rounded-md cursor-pointer hover:bg-gray-50">
                                <input type="checkbox" class="lc-choice lc-other rounded text-indigo-600 focus:ring-indigo-500" value="other">
                                <span class="text-sm text-gray-800">Others</span>
                            </label>
                        </div>
                        <div id="categoryCreditsInfo" class="mt-2 text-sm text-gray-600 hidden">
                            Credits for selected leave type: <span id="categoryCreditsValue" class="font-medium">0.000</span>
                        </div>
                        <div id="otherLeaveWrap" class="mt-2 hidden">
                            <input type="text" name="other_leave_name" id="other_leave_name" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Please specify other leave type" value="{{ old('other_leave_name') }}">
                            <x-input-error :messages="$errors->get('other_leave_name')" class="mt-2" />
                        </div>
                        <x-input-error :messages="$errors->get('leave_category_id')" class="mt-2" />
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="start_date" :value="__('Start Date')" />
                                <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" value="{{ old('start_date') }}" required />
                                <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="end_date" :value="__('End Date')" />
                                <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" value="{{ old('end_date') }}" required />
                                <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                            </div>
                        </div>
                        <div id="extraDateRanges" class="space-y-3 mt-3"></div>
                        <button type="button" id="addDateRange" class="mt-2 inline-flex items-center px-3 py-1.5 rounded border border-indigo-200 text-indigo-700 hover:bg-indigo-50">+ Add Dates</button>
                        <div class="mt-4 flex items-center justify-between">
                            <div></div>
                            <div class="flex items-center gap-2">
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Submit</button>
                                <button type="button" id="toStep2" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Next</button>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Details of Leave -->
                    <div id="tab-step2" class="tab-panel hidden">
                        <!-- Details Sections -->
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-3" data-detail-block="vacation">
                                <div class="text-sm font-medium text-gray-700">In case of Vacation/Special Privilege Leave</div>
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="detail_vac_within_check" value="1" @checked(old('detail_vac_within')) class="rounded text-indigo-600 focus:ring-indigo-500" data-group="vacation_place" data-toggle="#vac_within_input">
                                    <span class="text-sm text-gray-700">Within the Philippines</span>
                                </label>
                                <div id="vac_within_input" class="hidden">
                                    <input type="text" name="detail_vac_within" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old('detail_vac_within') }}" placeholder="Specify place within the Philippines">
                                </div>
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="detail_vac_abroad_check" value="1" @checked(old('detail_vac_abroad')) class="rounded text-indigo-600 focus:ring-indigo-500" data-group="vacation_place" data-toggle="#vac_abroad_input">
                                    <span class="text-sm text-gray-700">Abroad</span>
                                </label>
                                <div id="vac_abroad_input" class="hidden">
                                    <input type="text" name="detail_vac_abroad" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old('detail_vac_abroad') }}" placeholder="Specify country/city">
                                </div>
                            </div>
                            <div class="space-y-3" data-detail-block="sick">
                                <div class="text-sm font-medium text-gray-700">In case of Sick Leave</div>
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="detail_sick_hospital_check" value="1" @checked(old('detail_sick_hospital')) class="rounded text-indigo-600 focus:ring-indigo-500" data-group="sick_kind" data-toggle="#sick_hospital_input">
                                    <span class="text-sm text-gray-700">In Hospital (Specify Illness)</span>
                                </label>
                                <div id="sick_hospital_input" class="hidden">
                                    <input type="text" name="detail_sick_hospital" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old('detail_sick_hospital') }}" placeholder="Specify illness">
                                </div>
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="detail_sick_outpatient_check" value="1" @checked(old('detail_sick_outpatient')) class="rounded text-indigo-600 focus:ring-indigo-500" data-group="sick_kind" data-toggle="#sick_outpatient_input">
                                    <span class="text-sm text-gray-700">Out Patient (Specify Illness)</span>
                                </label>
                                <div id="sick_outpatient_input" class="hidden">
                                    <input type="text" name="detail_sick_outpatient" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old('detail_sick_outpatient') }}" placeholder="Specify illness">
                                </div>
                            </div>
                            <div class="space-y-3" data-detail-block="women">
                                <div class="text-sm font-medium text-gray-700">In case of Special Leave Benefits for Women</div>
                                <input type="text" name="detail_women" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old('detail_women') }}">
                            </div>
                            <div class="space-y-3" data-detail-block="study">
                                <div class="text-sm font-medium text-gray-700">In case of Study Leave</div>
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="detail_study_master" value="1" @checked(old('detail_study_master')) class="rounded text-indigo-600 focus:ring-indigo-500" data-group="study">
                                    <span class="text-sm text-gray-700">Completion of Master’s Degree</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="detail_study_bar" value="1" @checked(old('detail_study_bar')) class="rounded text-indigo-600 focus:ring-indigo-500" data-group="study">
                                    <span class="text-sm text-gray-700">BAR/Board Examination Review</span>
                                </label>
                            </div>
                            <div class="space-y-3" data-detail-block="other">
                                <div class="text-sm font-medium text-gray-700">Other purpose</div>
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="detail_other_monetization" value="1" @checked(old('detail_other_monetization')) class="rounded text-indigo-600 focus:ring-indigo-500" data-group="other_purpose">
                                    <span class="text-sm text-gray-700">Monetization of Leave Credits</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="detail_other_terminal" value="1" @checked(old('detail_other_terminal')) class="rounded text-indigo-600 focus:ring-indigo-500" data-group="other_purpose">
                                    <span class="text-sm text-gray-700">Terminal Leave</span>
                                </label>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center justify-between">
                            <button type="button" id="backToStep1" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">Back</button>
                            <div class="flex items-center gap-2">
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Submit</button>
                                <button type="button" id="toStep3" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Next</button>
                            </div>
                        </div>
                    </div>
                    <!-- Step 3: Number of Working Days Applied For -->
                    <div id="tab-step3" class="tab-panel hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="applied_days" :value="__('Number of Working Days Applied For')" />
                                <x-text-input id="applied_days" class="block mt-1 w-full" type="number" min="1" name="applied_days" value="{{ old('applied_days') }}" placeholder="e.g., 1" required />
                                <p class="text-xs text-gray-500 mt-1">Auto-filled from dates; you may adjust if needed.</p>
                            </div>
                            <div>
                                <x-input-label for="inclusive_dates_text" :value="__('Inclusive Dates')" />
                                <input id="inclusive_dates_text" name="inclusive_dates_text" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" value="{{ old('inclusive_dates_text') }}" placeholder="e.g., January 22, 2026" required>
                                <div id="inclusive_dates_preview" class="mt-2 text-xs text-gray-600"></div>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-between">
                            <button type="button" id="backToStep2" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">Back</button>
                            <div class="flex items-center gap-2">
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Submit</button>
                                <button type="button" id="toStep4" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Next</button>
                            </div>
                        </div>
                    </div>
                    <!-- Step 4: Commutation -->
                    <div id="tab-step4" class="tab-panel hidden">
                        <div class="space-y-3">
                            <div class="text-sm font-medium text-gray-700">Commutation</div>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="commutation_not_requested" value="not_requested" @checked(old('commutation')==='not_requested') class="rounded text-indigo-600 focus:ring-indigo-500" data-group="commutation">
                                <span class="text-sm text-gray-700">Not Requested</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="commutation_requested" value="requested" @checked(old('commutation')==='requested') class="rounded text-indigo-600 focus:ring-indigo-500" data-group="commutation">
                                <span class="text-sm text-gray-700">Requested</span>
                            </label>
                            <input type="hidden" name="commutation" id="commutation_value" value="{{ old('commutation') }}">
                        </div>
                        <div class="mt-4">
                            <x-input-label for="reason" :value="__('Reason')" />
                            <textarea id="reason" name="reason" rows="4" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>{{ old('reason') }}</textarea>
                            <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                        </div>
                        <div class="mt-4 flex items-center justify-between">
                            <button type="button" id="backToStep3" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">Back</button>
                            <x-primary-button>Submit</x-primary-button>
                        </div>
                    </div>
                 </form>
            </div>
                </section>
            </div>
        </div>
<script>
(function(){
    const choices = document.querySelectorAll('#leaveCategoryChoices .lc-choice');
    const hidden = document.getElementById('leave_category_id_hidden');
    const otherChoice = document.querySelector('#leaveCategoryChoices .lc-other');
    const otherWrap = document.getElementById('otherLeaveWrap');
    const otherInput = document.getElementById('other_leave_name');
    const toStep2 = document.getElementById('toStep2');
    const backToStep1 = document.getElementById('backToStep1');
    const toStep3 = document.getElementById('toStep3');
    const backToStep2 = document.getElementById('backToStep2');
    const toStep4 = document.getElementById('toStep4');
    const backToStep3 = document.getElementById('backToStep3');
    const tabTriggers = document.querySelectorAll('.tab-trigger');
    const tab1 = document.getElementById('tab-step1');
    const tab2 = document.getElementById('tab-step2');
    const tab3 = document.getElementById('tab-step3');
    const tab4 = document.getElementById('tab-step4');
    const form = document.getElementById('leaveCreateForm');
    const storageKey = 'leaveCreateDraft:' + ({{ Auth::id() ?? 0 }});
    if(!choices.length || !hidden) return;
    const enforceGroup = (groupName) => {
        const boxes = document.querySelectorAll(`input[type="checkbox"][data-group="${groupName}"]`);
        let found = false;
        boxes.forEach(b => {
            if (b.checked) {
                if (!found) {
                    found = true;
                } else {
                    b.checked = false;
                }
            }
        });
    };
    const creditInfo = document.getElementById('categoryCreditsInfo');
    const creditVal = document.getElementById('categoryCreditsValue');
    const setValue = (val) => {
        hidden.value = val || '';
    };
    const updateDetailBlocks = () => {
        const blocks = document.querySelectorAll('[data-detail-block]');
        if (!blocks.length) return;
        const checked = Array.from(choices).find(c => c.checked);
        const name = (checked?.getAttribute('data-name') || '').toLowerCase();
        const onlyVacation = name.includes('vacation');
        const onlyWomen = name.includes('maternity');
        const onlyMandatory = name.includes('mandatory') || name.includes('forced');
        const onlyStudy = name.includes('study');
        const onlyWomenLeave = name.includes('special leave benefits for women') || name.includes('special leave for women');
        const onlyWellness = name.includes('wellness');
        const onlySick = name.includes('sick');
        const onlySpecialPrivilege = name.includes('special privilege');
        blocks.forEach(block => {
            const key = block.getAttribute('data-detail-block');
            const show =
                ((onlyVacation || onlyMandatory || onlySpecialPrivilege) && key === 'vacation') ||
                ((onlyWellness || onlySick) && key === 'sick') ||
                ((onlyWomen || onlyWomenLeave) && key === 'women') ||
                (onlyStudy && key === 'study') ||
                (!onlyVacation && !onlyWomen && !onlyMandatory && !onlyStudy && !onlyWomenLeave && !onlyWellness && !onlySick && !onlySpecialPrivilege);
            block.classList.toggle('hidden', !show);
            if (!show) {
                block.querySelectorAll('input, textarea, select').forEach(el => {
                    if (el.type === 'checkbox' || el.type === 'radio') {
                        el.checked = false;
                    } else {
                        el.value = '';
                    }
                    el.disabled = true;
                    el.required = false;
                });
            } else {
                block.querySelectorAll('input, textarea, select').forEach(el => {
                    el.disabled = false;
                });
            }
        });
        // Re-sync toggle-driven inputs within visible blocks
        document.querySelectorAll('input[type="checkbox"][data-toggle]').forEach(el => {
            const parentBlock = el.closest('[data-detail-block]');
            if (parentBlock && parentBlock.classList.contains('hidden')) return;
            const targetSel = el.getAttribute('data-toggle');
            const target = targetSel ? document.querySelector(targetSel) : null;
            if (target) {
                const show = el.checked;
                target.classList.toggle('hidden', !show);
                const input = target.querySelector('input, textarea, select');
                if (input) {
                    input.disabled = !show;
                    if (!show) input.required = false;
                }
            }
        });
    };
    const updateUI = () => {
        const checked = Array.from(choices).find(c => c.checked);
        setValue(checked ? checked.value : '');
        // Update credits display
        if (creditInfo && creditVal) {
            if (checked && !checked.classList.contains('lc-other')) {
                const credits = parseFloat(checked.getAttribute('data-credits') || '0') || 0;
                creditVal.textContent = credits.toFixed(3);
                creditInfo.classList.remove('hidden');
            } else {
                creditInfo.classList.add('hidden');
            }
        }
        if (otherChoice && otherWrap) {
            const isOther = checked && checked.classList.contains('lc-other');
            otherWrap.classList.toggle('hidden', !isOther);
            if (!isOther) {
                if (otherInput) otherInput.value = '';
            }
            if (otherInput) otherInput.required = !!isOther;
        }
        updateDetailBlocks();
    };
    const saveDraft = () => {
        if (!form) return;
        const data = {};
        const elements = form.querySelectorAll('input, textarea, select');
        elements.forEach(el => {
            if (!el.name) return;
            if (el.name === '_token' || el.name === '_method') return; // never persist CSRF/method
            const isArray = /\[\]$/.test(el.name);
            let val;
            if (el.type === 'checkbox') {
                val = el.checked ? '1' : '';
            } else {
                val = el.value || '';
            }
            if (isArray) {
                if (!Array.isArray(data[el.name])) data[el.name] = [];
                data[el.name].push(val);
            } else {
                data[el.name] = val;
            }
        });
        localStorage.setItem(storageKey, JSON.stringify(data));
    };
    const loadDraft = () => {
        const raw = localStorage.getItem(storageKey);
        if (!raw) return;
        let data;
        try { data = JSON.parse(raw); } catch { data = null; }
        if (!data) return;
        Object.keys(data).forEach(name => {
            if (name === '_token' || name === '_method') return; // never restore CSRF/method
            if (/\[\]$/.test(name)) return; // handled separately for dynamic arrays
            const el = form.querySelector(`[name="${name}"]`);
            if (!el) return;
            if (el.type === 'checkbox') {
                el.checked = data[name] === '1';
            } else {
                el.value = data[name];
            }
        });
        // After restoring state, enforce single-select groups
        const groups = new Set();
        form.querySelectorAll('input[type="checkbox"][data-group]').forEach(el => groups.add(el.getAttribute('data-group')));
        groups.forEach(g => enforceGroup(g));
        // Show/hide toggle inputs according to checkbox state
        document.querySelectorAll('input[type="checkbox"][data-toggle]').forEach(el => {
            const targetSel = el.getAttribute('data-toggle');
            const target = targetSel ? document.querySelector(targetSel) : null;
            if (target) {
                const show = el.checked;
                target.classList.toggle('hidden', !show);
                const input = target.querySelector('input, textarea, select');
                if (input) {
                    input.disabled = !show;
                }
            }
        });
        if (hidden && hidden.value) {
            const pre = Array.from(choices).find(c => c.value === hidden.value);
            if (pre) pre.checked = true;
        }
        updateUI();
    };
    if (form) {
        let t;
        form.addEventListener('input', () => {
            clearTimeout(t);
            t = setTimeout(saveDraft, 200);
        });
        form.addEventListener('change', saveDraft);
        form.addEventListener('submit', () => {
            localStorage.removeItem(storageKey);
        });
        window.addEventListener('beforeunload', saveDraft);
    }
    loadDraft();
    choices.forEach(box => {
        box.addEventListener('change', function(){
            if (this.checked) {
                choices.forEach(b => { if (b !== this) b.checked = false; });
            }
            updateUI();
            saveDraft();
        });
    });
    // Pre-select based on old hidden value
    if (hidden.value) {
        const pre = Array.from(choices).find(c => c.value === hidden.value);
        if (pre) { pre.checked = true; }
    }
    updateUI();
    // Attach single-select behavior for grouped checkboxes
    document.querySelectorAll('input[type="checkbox"][data-group]').forEach(el => {
        el.addEventListener('change', function(){
            const grp = this.getAttribute('data-group');
            if (this.checked && grp) {
                document.querySelectorAll(`input[type="checkbox"][data-group="${grp}"]`).forEach(b => {
                    if (b !== this) b.checked = false;
                });
                saveDraft();
            }
            // Handle toggle visibility for associated input
            const targetSel = this.getAttribute('data-toggle');
            const target = targetSel ? document.querySelector(targetSel) : null;
            if (target) {
                const show = this.checked;
                target.classList.toggle('hidden', !show);
                const input = target.querySelector('input, textarea, select');
                if (input) {
                    if (!show) {
                        input.value = '';
                        input.disabled = true;
                        input.required = false;
                    } else {
                        input.disabled = false;
                        input.required = true;
                    }
                }
            }
            // When switching within a group, hide/clear other toggled inputs
            if (grp) {
                document.querySelectorAll(`input[type="checkbox"][data-group="${grp}"]`).forEach(b => {
                    if (b !== this) {
                        const sel = b.getAttribute('data-toggle');
                        const tgt = sel ? document.querySelector(sel) : null;
                        if (tgt) {
                            tgt.classList.add('hidden');
                            const i = tgt.querySelector('input, textarea, select');
                            if (i) { i.value = ''; i.disabled = true; i.required = false; }
                        }
                    }
                });
            }
        });
    });

    const validateStep1 = () => {
        const s = document.getElementById('start_date');
        const e = document.getElementById('end_date');
        if (!hidden.value) { alert('Please select a leave type.'); return false; }
        if (!s || !s.value) { alert('Please enter a Start Date.'); return false; }
        if (!e || !e.value) { alert('Please enter an End Date.'); return false; }
        return true;
    };
    const validateStep2 = () => {
        // For each checkbox with data-toggle, if checked, ensure inner input has value
        const togglers = document.querySelectorAll('input[type="checkbox"][data-toggle]');
        for (const t of togglers) {
            if (t.checked) {
                const sel = t.getAttribute('data-toggle');
                const tgt = sel ? document.querySelector(sel) : null;
                const input = tgt ? tgt.querySelector('input, textarea') : null;
                if (input && !input.value.trim()) {
                    alert('Please fill out all required detail fields.');
                    input.focus();
                    return false;
                }
            }
        }
        return true;
    };
    const validateStep3 = () => {
        const days = document.getElementById('applied_days');
        const incl = document.getElementById('inclusive_dates_text');
        if (!days || !days.value || Number(days.value) < 1) { alert('Please provide the number of working days.'); return false; }
        if (!incl || !incl.value.trim()) { alert('Please provide the inclusive dates.'); return false; }
        return true;
    };
    const validateStep4 = () => {
        const cv = document.getElementById('commutation_value');
        const reason = document.getElementById('reason');
        if (!cv || !cv.value) { alert('Please select a Commutation option.'); return false; }
        if (!reason || !reason.value.trim()) { alert('Please provide a Reason.'); return false; }
        return true;
    };

    const setActiveTrigger = (idx) => {
        tabTriggers.forEach((btn, i) => {
            btn.classList.toggle('bg-indigo-50', i===idx);
            btn.classList.toggle('bg-white', i!==idx);
            btn.classList.toggle('text-indigo-700', i===idx);
            btn.classList.toggle('border', i===idx);
            btn.classList.toggle('border-b-0', i===idx);
            btn.classList.toggle('border-indigo-200', i===idx);
            btn.classList.toggle('border-transparent', i!==idx);
            btn.classList.toggle('text-gray-600', i!==idx);
        });
    };
    const goTo = (which) => {
        [tab1, tab2, tab3, tab4].forEach(t => t.classList.add('hidden'));
        if (which === 'step1') { tab1.classList.remove('hidden'); setActiveTrigger(0); return; }
        if (which === 'step2') { tab2.classList.remove('hidden'); setActiveTrigger(1); return; }
        if (which === 'step3') { tab3.classList.remove('hidden'); setActiveTrigger(2); return; }
        if (which === 'step4') { tab4.classList.remove('hidden'); setActiveTrigger(3); return; }
    };
    if (toStep2) {
        toStep2.addEventListener('click', () => {
            if (!validateStep1()) return;
            saveDraft();
            goTo('step2');
        });
    }
    if (backToStep1) {
        backToStep1.addEventListener('click', () => goTo('step1'));
    }
    if (toStep3) {
        toStep3.addEventListener('click', () => {
            if (!validateStep2()) return;
            saveDraft();
            goTo('step3');
        });
    }
    if (backToStep2) {
        backToStep2.addEventListener('click', () => goTo('step2'));
    }
    if (toStep4) {
        toStep4.addEventListener('click', () => {
            if (!validateStep3()) return;
            saveDraft();
            goTo('step4');
        });
    }
    if (backToStep3) {
        backToStep3.addEventListener('click', () => goTo('step3'));
    }
    // Allow clicking tab headers
    tabTriggers.forEach(btn => {
        btn.addEventListener('click', () => {
            const target = btn.getAttribute('data-tab');
            if ((target === 'step2' || target === 'step3' || target === 'step4') && !hidden.value) {
                alert('Please select a leave type first.');
                return;
            }
            goTo(target);
        });
    });
    // Auto-fill applied days based on dates
    const s = document.getElementById('start_date');
    const e = document.getElementById('end_date');
    const applied = document.getElementById('applied_days');
    const inclText = document.getElementById('inclusive_dates_text');
    const formatLong = (d) => d.toLocaleDateString('en-US', { month:'long', day:'numeric', year:'numeric' });
    const extraWrap = document.getElementById('extraDateRanges');
    const addBtn = document.getElementById('addDateRange');
    let rangeIndex = 0;
    const attachListeners = (st, en) => {
        if (st) st.addEventListener('change', computeDays);
        if (en) en.addEventListener('change', computeDays);
    };
    const addRangeRow = () => {
        rangeIndex++;
        const row = document.createElement('div');
        row.className = 'extra-range-row grid grid-cols-1 md:grid-cols-2 gap-4 items-end';
        row.innerHTML = `
            <div>
                <label class="text-sm text-gray-700">Start Date</label>
                <input type="date" name="extra_start_date[]" class="block mt-1 w-full extra-start border-gray-300 rounded-md shadow-sm">
            </div>
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label class="text-sm text-gray-700">End Date</label>
                    <input type="date" name="extra_end_date[]" class="block mt-1 w-full extra-end border-gray-300 rounded-md shadow-sm">
                </div>
                <button type="button" class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50 remove-range">Remove</button>
            </div>
        `;
        extraWrap.appendChild(row);
        const st = row.querySelector('.extra-start');
        const en = row.querySelector('.extra-end');
        const rm = row.querySelector('.remove-range');
        attachListeners(st, en);
        if (rm) rm.addEventListener('click', () => { row.remove(); computeDays(); saveDraft(); });
    };
    if (addBtn) addBtn.addEventListener('click', addRangeRow);
    const restoreExtraDateRanges = () => {
        const raw = localStorage.getItem(storageKey);
        if (!raw) return;
        let data; try { data = JSON.parse(raw); } catch { data = null; }
        if (!data) return;
        const starts = Array.isArray(data['extra_start_date[]']) ? data['extra_start_date[]'] : [];
        const ends = Array.isArray(data['extra_end_date[]']) ? data['extra_end_date[]'] : [];
        const n = Math.max(starts.length, ends.length);
        for (let i=0;i<n;i++){
            addRangeRow();
            const rows = extraWrap.querySelectorAll('.extra-range-row');
            const row = rows[rows.length-1];
            const st = row.querySelector('.extra-start');
            const en = row.querySelector('.extra-end');
            if (st) st.value = starts[i] || '';
            if (en) en.value = ends[i] || '';
        }
        computeDays();
    };
    const computeDays = () => {
        let pairs = [];
        if (s && e && s.value && e.value) pairs.push([s.value, e.value]);
        document.querySelectorAll('.extra-range-row').forEach(r => {
            const st = r.querySelector('.extra-start');
            const en = r.querySelector('.extra-end');
            if (st && en && st.value && en.value) pairs.push([st.value, en.value]);
        });
        let total = 0;
        let parts = [];
        let items = [];
        pairs.forEach(([sv, ev]) => {
            const sd = new Date(sv + 'T00:00:00');
            const ed = new Date(ev + 'T00:00:00');
            if (isNaN(sd) || isNaN(ed) || ed < sd) return;
            let c = 0;
            const cur = new Date(sd);
            while (cur <= ed) {
                const day = cur.getDay();
                if (day !== 0 && day !== 6) c++;
                cur.setDate(cur.getDate() + 1);
            }
            total += c;
            const a = formatLong(sd);
            const b = formatLong(ed);
            parts.push(a === b ? a : `${a} to ${b}`);
            items.push({text: a === b ? a : `${a} to ${b}`, days: c});
        });
        applied.value = total ? String(total) : '';
        if (inclText) inclText.value = parts.join(' ; ');
        const prev = document.getElementById('inclusive_dates_preview');
        if (prev) {
            if (items.length) {
                let html = '<div class="text-gray-700 font-medium">Ranges</div><ul class="list-disc pl-5">';
                items.forEach(it => {
                    html += `<li>${it.text} — ${it.days} working day${it.days===1?'':'s'}</li>`;
                });
                html += `</ul><div class="mt-1">Total: <span class="font-semibold">${total}</span> working day${total===1?'':'s'}</div>`;
                prev.innerHTML = html;
            } else {
                prev.innerHTML = '';
            }
        }
    };
    if (s) s.addEventListener('change', computeDays);
    if (e) e.addEventListener('change', computeDays);
    computeDays();
    restoreExtraDateRanges();
    // Commutation single-select and hidden field sync
    const commHidden = document.getElementById('commutation_value');
    const commBoxes = document.querySelectorAll('input[type="checkbox"][data-group="commutation"]');
    commBoxes.forEach(cb => {
        cb.addEventListener('change', function(){
            if (this.checked) {
                commBoxes.forEach(b => { if (b !== this) b.checked = false; });
                commHidden.value = this.value === 'requested' ? 'requested' : 'not_requested';
            } else if (!Array.from(commBoxes).some(b=>b.checked)) {
                commHidden.value = '';
            }
        });
        if (cb.checked) {
            commHidden.value = cb.value === 'requested' ? 'requested' : 'not_requested';
        }
    });
    // Validate on form submit
    if (form) {
        form.addEventListener('submit', (e) => {
            if (!validateStep1() || !validateStep2() || !validateStep3() || !validateStep4()) {
                e.preventDefault();
                return false;
            }
            // Remove 'required' from inputs inside hidden steps to avoid
            // native HTML validation complaining about non-focusable controls.
            [tab1, tab2, tab3, tab4].forEach(panel => {
                if (panel.classList.contains('hidden')) {
                    const reqs = panel.querySelectorAll('[required]');
                    reqs.forEach(el => {
                        el.removeAttribute('required');
                    });
                }
            });
        });
    }
    // Start in step1
    goTo('step1');
})();
</script>
</x-app-layout>
