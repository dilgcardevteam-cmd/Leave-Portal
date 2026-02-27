<x-app-layout>
    <div class="hr-shell min-h-[calc(100vh-4rem)] bg-[#f4f5f9]" x-data="{ sidebarCollapsed: false }">
            <div class="grid min-h-[calc(100vh-4rem)] grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] lg:transition-[grid-template-columns] lg:duration-200 lg:ease-out" :class="sidebarCollapsed ? 'lg:grid-cols-[88px_minmax(0,1fr)]' : 'lg:grid-cols-[280px_minmax(0,1fr)]'">
                @include('rd.partials.sidebar')
                <section class="p-4 sm:p-6 lg:p-8">
                    @if (session('status'))
                        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
                    @endif
                    <div class="bg-white p-6 shadow sm:rounded-xl border border-gray-200">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800">RD Panel</h2>
                                <p class="text-sm text-gray-500">Review and process leave request.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                    {{ $leave->status === 'approved' ? 'bg-green-100 text-green-700' : ($leave->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($leave->status) }}
                                </span>
                                <a href="{{ route('rd.leaves') }}" class="px-3 py-2 bg-gray-100 border border-gray-200 text-gray-800 rounded-md hover:bg-gray-200">Back</a>
                            </div>
                        </div>
                        <div class="border-b mb-4">
                            <nav class="flex gap-2" role="tablist" aria-label="RD tabs">
                                <button type="button" class="rd-tab px-4 py-2 rounded-t-md bg-indigo-50 text-indigo-700 border border-b-0 border-indigo-200" data-tab="req">LEAVE REQUEST</button>
                                <button type="button" class="rd-tab px-4 py-2 rounded-t-md text-gray-600 hover:text-gray-800 border border-b-0 border-transparent" data-tab="credits">CERTIFICATION OF LEAVE CREDITS</button>
                                <button type="button" class="rd-tab px-4 py-2 rounded-t-md text-gray-600 hover:text-gray-800 border border-b-0 border-transparent" data-tab="reco">RECOMMENDATION</button>
                                <button type="button" class="rd-tab px-4 py-2 rounded-t-md text-gray-600 hover:text-gray-800 border border-b-0 border-transparent" data-tab="decision">APPROVED FOR / DISAPPROVED DUE TO</button>
                            </nav>
                        </div>
                        <div class="mb-2 text-xs text-gray-600">
                            @if($leave->hr_approved_at)
                                HR approved on <time datetime="{{ $leave->hr_approved_at?->toIso8601String() }}" data-realtime="absolute">{{ $leave->hr_approved_at?->timezone(config('app.timezone'))->format('M d, Y g:i A') }}</time>
                                @if($leave->hr_comment) • Comment: {{ $leave->hr_comment }} @endif
                            @endif
                            @if($leave->dc_approved_at)
                                <br>DC approved on <time datetime="{{ $leave->dc_approved_at?->toIso8601String() }}" data-realtime="absolute">{{ $leave->dc_approved_at?->timezone(config('app.timezone'))->format('M d, Y g:i A') }}</time>
                                @if($leave->dc_comment) • Comment: {{ $leave->dc_comment }} @endif
                            @endif
                        </div>
                        <div id="rd-tab-req">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <div class="text-sm text-gray-500">User</div>
                                    <div class="mt-1 flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-full bg-[#0d3b66] text-white flex items-center justify-center">
                                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                                <path d="M12 12c2.761 0 5-2.686 5-6s-2.239-6-5-6-5 2.686-5 6 2.239 6 5 6zm0 2c-4.418 0-12 2.239-12 6v2h24v-2c0-3.761-7.582-6-12-6z"/>
                                            </svg>
                                        </div>
                                        <div class="text-gray-900 font-medium">{{ $leave->user->display_name ?? ($leave->user->name ?? '-') }}</div>
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $leave->user->email ?? '' }}</div>
                                    @if($leave->user?->username)
                                    <div class="text-xs text-gray-500">{{ $leave->user->username }}</div>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500">TYPE OF LEAVE TO BE AVAILED OF</div>
                                    <div class="text-gray-900 font-medium">{{ $leave->category->name ?? '-' }}</div>
                                    @if($leave->category?->description)
                                    <div class="text-xs text-gray-500">{{ $leave->category->description }}</div>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500">Dates</div>
                                    <div class="text-gray-900 mt-1">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded bg-gray-100 border border-gray-200 text-sm">
                                            {{ $leave->start_date }} → {{ $leave->end_date }}
                                        </span>
                                        <span class="ml-2 text-xs text-gray-500">{{ $leave->days }} days</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500">Current Status</div>
                                    <div>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $leave->status === 'approved' ? 'bg-green-100 text-green-700' : ($leave->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-800') }}">{{ ucfirst($leave->status) }}</span>
                                    </div>
                                </div>
                                
                                @php
                                    $appliedDays = null;
                                    $inclusiveDates = null;
                                    $commutation = null;
                                    $detailItems = [];
                                    if (!empty($leave->details_json) && is_array($leave->details_json)) {
                                        $dj = $leave->details_json;
                                        $appliedDays = $dj['working_days']['applied_days'] ?? null;
                                        $inclusiveDates = $dj['working_days']['inclusive_dates'] ?? null;
                                        $commutation = $dj['commutation'] ?? null;
                                        $items = [];
                                        $vac = $dj['details_of_leave']['vacation'] ?? [];
                                        if (!empty($vac['within_ph'])) $items[] = 'Vacation/Special Privilege Leave: Within the Philippines: '.$vac['within_ph'];
                                        if (!empty($vac['abroad'])) $items[] = 'Vacation/Special Privilege Leave: Abroad: '.$vac['abroad'];
                                        $sick = $dj['details_of_leave']['sick'] ?? [];
                                        if (!empty($sick['hospital'])) $items[] = 'Sick Leave: In Hospital: '.$sick['hospital'];
                                        if (!empty($sick['outpatient'])) $items[] = 'Sick Leave: Out Patient: '.$sick['outpatient'];
                                        if (!empty($dj['details_of_leave']['women'])) $items[] = 'Special Leave Benefits for Women: '.$dj['details_of_leave']['women'];
                                        $study = $dj['details_of_leave']['study'] ?? [];
                                        if (!empty($study['master'])) $items[] = 'Study Leave: Completion of Master\'s Degree';
                                        if (!empty($study['bar'])) $items[] = 'Study Leave: BAR/Board Examination Review';
                                        $other = $dj['details_of_leave']['other'] ?? [];
                                        if (!empty($other['monetization'])) $items[] = 'Other Purpose: Monetization of Leave Credits';
                                        if (!empty($other['terminal'])) $items[] = 'Other Purpose: Terminal Leave';
                                        $detailItems = $items;
                                    } else {
                                        $detailText = (string)($leave->reason ?? '');
                                        if ($detailText !== '') {
                                            $needle = 'Details of Leave — ';
                                            $pos = mb_strpos($detailText, $needle);
                                            if ($pos !== false) {
                                                $section = trim(mb_substr($detailText, $pos + mb_strlen($needle)));
                                                $parts = preg_split('/\s*\|\s*/u', $section);
                                                foreach ($parts as $p) {
                                                    $p = trim($p);
                                                    if ($p === '') continue;
                                                    if (mb_stripos($p, 'Number of Working Days Applied For:') === 0) {
                                                        $appliedDays = trim(mb_substr($p, mb_strlen('Number of Working Days Applied For:')));
                                                        continue;
                                                    }
                                                    if (mb_stripos($p, 'Inclusive Dates:') === 0) {
                                                        $inclusiveDates = trim(mb_substr($p, mb_strlen('Inclusive Dates:')));
                                                        continue;
                                                    }
                                                    if (mb_stripos($p, 'Commutation:') === 0) {
                                                        $commutation = trim(mb_substr($p, mb_strlen('Commutation:')));
                                                        continue;
                                                    }
                                                    $detailItems[] = $p;
                                                }
                                            }
                                        }
                                    }
                                @endphp
                                <div class="md:col-span-2">
                                    <div class="border border-gray-200 rounded-lg overflow-hidden bg-white">
                                        <table class="w-full border-collapse">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="border border-gray-200 p-2 text-left w-1/3">Item</th>
                                                    <th class="border border-gray-200 p-2 text-left">Value</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-gray-900">
                                                <tr>
                                                    <th class="border border-gray-200 p-2 align-top">Type of Leave</th>
                                                    <td class="border border-gray-200 p-2">{{ $leave->details_json['type_of_leave']['name'] ?? ($leave->category->name ?? '—') }}</td>
                                                </tr>
                                                <tr>
                                                    <th class="border border-gray-200 p-2 align-top">Details of Leave</th>
                                                    <td class="border border-gray-200 p-2">
                                                        @if(!empty($detailItems))
                                                        <ul class="list-disc list-inside">
                                                            @foreach($detailItems as $item)
                                                            <li>{{ $item }}</li>
                                                            @endforeach
                                                        </ul>
                                                        @else
                                                        —
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="border border-gray-200 p-2 align-top">Number of Working Days</th>
                                                    <td class="border border-gray-200 p-2">
                                                        {{ $appliedDays ?? ($leave->days ?? '—') }}
                                                        @if(!empty($inclusiveDates))
                                                            <div class="text-xs text-gray-500">Inclusive Dates: {{ $inclusiveDates }}</div>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="border border-gray-200 p-2 align-top">Commutation</th>
                                                    <td class="border border-gray-200 p-2">{{ $commutation ?? '—' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="border-t mt-6 pt-6 flex justify-end">
                                <button type="button" id="rdNext1" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Next</button>
                            </div>
                        </div>
                        <div id="rd-tab-credits" class="hidden">
                            <div class="border border-gray-300 rounded-lg overflow-hidden">
                                <table class="w-full border-collapse">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="border border-gray-300 p-2"></th>
                                            <th class="border border-gray-300 p-2 text-center">Vacation Leave</th>
                                            <th class="border border-gray-300 p-2 text-center">Sick Leave</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th class="border border-gray-300 p-2 italic font-medium text-left">Total Earned</th>
                                            <td class="border border-gray-300 p-2">
                                                <input id="vl_total" type="number" step="0.001" class="w-full border-gray-300 rounded-md shadow-sm bg-gray-50" value="{{ $leave->details_json['credits']['vacation']['total'] ?? '' }}" readonly disabled>
                                            </td>
                                            <td class="border border-gray-300 p-2">
                                                <input id="sl_total" type="number" step="0.001" class="w-full border-gray-300 rounded-md shadow-sm bg-gray-50" value="{{ $leave->details_json['credits']['sick']['total'] ?? '' }}" readonly disabled>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="border border-gray-300 p-2 italic font-medium text-left">Less this application</th>
                                            <td class="border border-gray-300 p-2">
                                                <input id="vl_less" type="number" step="0.001" value="{{ $leave->details_json['credits']['vacation']['less'] ?? ($leave->category?->name && str_contains(mb_strtolower($leave->category->name),'vacation') ? $leave->days : 0) }}" class="w-full border-gray-300 rounded-md shadow-sm bg-gray-50" readonly disabled>
                                            </td>
                                            <td class="border border-gray-300 p-2">
                                                <input id="sl_less" type="number" step="0.001" value="{{ $leave->details_json['credits']['sick']['less'] ?? ($leave->category?->name && str_contains(mb_strtolower($leave->category->name),'sick') ? $leave->days : 0) }}" class="w-full border-gray-300 rounded-md shadow-sm bg-gray-50" readonly disabled>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="border border-gray-300 p-2 italic font-medium text-left">Balance</th>
                                            <td class="border border-gray-300 p-2">
                                                <input id="vl_balance" type="number" step="0.001" class="w-full border-gray-300 rounded-md shadow-sm bg-gray-50" value="{{ $leave->details_json['credits']['vacation']['balance'] ?? '' }}" readonly disabled>
                                            </td>
                                            <td class="border border-gray-300 p-2">
                                                <input id="sl_balance" type="number" step="0.001" class="w-full border-gray-300 rounded-md shadow-sm bg-gray-50" value="{{ $leave->details_json['credits']['sick']['balance'] ?? '' }}" readonly disabled>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-6 flex items-center justify-between">
                                <div class="text-sm text-gray-600">
                                    <span class="font-medium">Certified by:</span>
                                    {{ $leave->details_json['credits']['meta']['updated_by_name'] ?? '—' }}
                                    @if(!empty($leave->details_json['credits']['meta']['updated_at']))
                                        <span class="text-gray-500">on</span>
                                        <time datetime="{{ $leave->details_json['credits']['meta']['updated_at'] }}" data-realtime="absolute">{{ \Carbon\Carbon::parse($leave->details_json['credits']['meta']['updated_at'])->timezone(config('app.timezone'))->format('M d, Y g:i A') }}</time>
                                        <span class="ml-1 text-gray-500" data-realtime="relative" datetime="{{ $leave->details_json['credits']['meta']['updated_at'] }}"></span>
                                    @endif
                                </div>
                                <button type="button" id="rdNext2" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Next</button>
                            </div>
                        </div>
                        <div id="rd-tab-reco" class="hidden">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-3">
                                    @php 
                                        $rr = $leave->details_json['rd_recommendation'] ?? []; 
                                        $seed = !empty($rr) ? $rr : ($leave->details_json['dc_recommendation'] ?? []);
                                    @endphp
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" class="rounded text-indigo-600 focus:ring-indigo-500" data-group="reco" data-toggle="#rd_reco_for_approval" {{ (!empty($seed) && ($seed['decision'] ?? '')==='approval') ? 'checked' : '' }} disabled>
                                        <span class="text-sm text-gray-700">For approval</span>
                                    </label>
                                    <div id="rd_reco_for_approval" class="{{ (!empty($seed) && ($seed['decision'] ?? '')==='approval') ? '' : 'hidden' }}">
                                        <input type="text" class="mt-1 w-full border-gray-300 rounded-md shadow-sm bg-gray-50" placeholder="Remarks (optional)" value="{{ $seed['remarks'] ?? '' }}" readonly disabled>
                                    </div>
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" class="rounded text-indigo-600 focus:ring-indigo-500" data-group="reco" data-toggle="#rd_reco_disapproval" {{ (!empty($seed) && ($seed['decision'] ?? '')==='disapproval') ? 'checked' : '' }} disabled>
                                        <span class="text-sm text-gray-700">For disapproval due to</span>
                                    </label>
                                    <div id="rd_reco_disapproval" class="{{ (!empty($seed) && ($seed['decision'] ?? '')==='disapproval') ? '' : 'hidden' }}">
                                        <textarea rows="3" class="mt-1 w-full border-gray-300 rounded-md shadow-sm bg-gray-50" placeholder="Specify reasons" readonly disabled>{{ $seed['reason'] ?? '' }}</textarea>
                                    </div>
                                </div>
                                </div>
                                <div class="mt-6 flex items-center justify-between">
                                    <div class="text-sm text-gray-600">
                                        <span class="font-medium">Certified by:</span>
                                        {{ $seed['meta']['updated_by_name'] ?? '—' }}
                                        @if(!empty($seed['meta']['updated_at']))
                                            <span class="text-gray-500">on</span>
                                            <time datetime="{{ $seed['meta']['updated_at'] }}" data-realtime="absolute">{{ \Carbon\Carbon::parse($seed['meta']['updated_at'])->timezone(config('app.timezone'))->format('M d, Y g:i A') }}</time>
                                            <span class="ml-1 text-gray-500" data-realtime="relative" datetime="{{ $seed['meta']['updated_at'] }}"></span>
                                        @endif
                                    </div>
                                    <button type="button" id="rdRecoNext" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Next</button>
                                </div>
                        </div>
                        <div id="rd-tab-decision" class="hidden">
                            <div class="mt-6 flex items-center justify-between">
                                <form action="{{ route('rd.leaves.status', $leave) }}" method="POST" class="flex flex-wrap items-center gap-3">
                                    @csrf
                                    <select name="status" class="border-gray-300 rounded-md shadow-sm">
                                        <option value="approved" @selected($leave->status==='approved')>Approve</option>
                                        <option value="rejected" @selected($leave->status==='rejected')>Disapprove</option>
                                    </select>
                                    <input type="text" name="comment" class="border-gray-300 rounded-md shadow-sm w-full md:w-72" placeholder="Comment (optional)">
                                    <button class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Finalize Decision</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
    </div>
<script>
(function(){
    const rt=document.querySelectorAll('[data-realtime]');
    if(rt.length){
        const fmtAbs=new Intl.DateTimeFormat(undefined,{dateStyle:'medium',timeStyle:'short'});
        const rtf=new Intl.RelativeTimeFormat(undefined,{numeric:'auto'});
        function rel(d){
            const now=new Date();
            const diff=(d-now)/1000;
            const abs=Math.abs(diff);
            if(abs<45)return rtf.format(Math.round(diff),'second');
            const m=diff/60;
            if(Math.abs(m)<45)return rtf.format(Math.round(m),'minute');
            const h=m/60;
            if(Math.abs(h)<22)return rtf.format(Math.round(h),'hour');
            const day=h/24;
            return rtf.format(Math.round(day),'day');
        }
        function tick(){
            rt.forEach(n=>{
                const iso=n.getAttribute('datetime'); if(!iso)return;
                const d=new Date(iso);
                const mode=(n.getAttribute('data-realtime')||'both').toLowerCase();
                if(mode==='relative'){
                    n.textContent='('+rel(d)+')';
                }else if(mode==='absolute'){
                    n.textContent=fmtAbs.format(d);
                }else{
                    n.textContent=fmtAbs.format(d)+' ('+rel(d)+')';
                }
            });
        }
        tick(); setInterval(tick,30000);
    }
    const tabs = document.querySelectorAll('.rd-tab');
    const panels = {
        req: document.getElementById('rd-tab-req'),
        credits: document.getElementById('rd-tab-credits'),
        reco: document.getElementById('rd-tab-reco'),
        decision: document.getElementById('rd-tab-decision'),
    };
    function setActive(btn){
        tabs.forEach(b=>{
            b.classList.remove('bg-indigo-50','text-indigo-700','border','border-b-0','border-indigo-200');
            b.classList.add('text-gray-600');
        });
        btn.classList.add('bg-indigo-50','text-indigo-700','border','border-b-0','border-indigo-200');
        btn.classList.remove('text-gray-600');
    }
    function show(name){
        Object.values(panels).forEach(p=>p.classList.add('hidden'));
        if(panels[name]) panels[name].classList.remove('hidden');
    }
    tabs.forEach(btn=>{
        btn.addEventListener('click', ()=>{
            const name = btn.getAttribute('data-tab');
            setActive(btn);
            show(name);
        });
    });
    document.querySelectorAll('input[type="checkbox"][data-group]').forEach(el=>{
        el.addEventListener('change', function(){
            const grp = this.getAttribute('data-group');
            if(this.checked && grp){
                document.querySelectorAll(`input[type="checkbox"][data-group="${grp}"]`).forEach(b=>{
                    if(b!==this) b.checked=false;
                });
            }
            const sel = this.getAttribute('data-toggle');
            if(sel){
                const tgt = document.querySelector(sel);
                if(tgt){
                    tgt.classList.toggle('hidden', !this.checked);
                    const inner = tgt.querySelector('input, textarea, select');
                    if(inner){ inner.disabled = !this.checked; if(!this.checked) inner.value=''; }
                }
            }
        });
    });
    const btn1 = document.getElementById('rdNext1');
    const btn2 = document.getElementById('rdNext2');
    if(btn1){
        btn1.addEventListener('click', ()=>{
            const second = document.querySelector('[data-tab=\"credits\"]');
            if(second){ setActive(second); show('credits'); }
        });
    }
    if(btn2){
        btn2.addEventListener('click', ()=>{
            const third = document.querySelector('[data-tab=\"reco\"]');
            if(third){ setActive(third); show('reco'); }
        });
    }
    const btn3 = document.getElementById('rdRecoNext');
    if(btn3){
        btn3.addEventListener('click', ()=>{
            const fourth = document.querySelector('[data-tab="decision"]');
            if(fourth){ setActive(fourth); show('decision'); }
        });
    }
})();
</script>
</x-app-layout>

