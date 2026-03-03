<x-app-layout>
    <div class="hr-shell min-h-[calc(100vh-4rem)] bg-[#f4f5f9]" x-data="{ sidebarCollapsed: false }">
        <div class="grid min-h-[calc(100vh-4rem)] grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] lg:transition-[grid-template-columns] lg:duration-200 lg:ease-out" :class="sidebarCollapsed ? 'lg:grid-cols-[88px_minmax(0,1fr)]' : 'lg:grid-cols-[280px_minmax(0,1fr)]'">
            @include('hr.partials.sidebar')

            <section class="h-full overflow-auto p-6 sm:p-8 lg:p-10">
                <div class="max-w-6xl">
                    <h1 class="mb-6 text-4xl font-semibold tracking-tight text-gray-900">Dashboard</h1>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="glass text-gray-900">
                            <div class="flex gap-4 p-4">
                                <div class="flex-1">
                                    <div class="text-[52px] leading-none font-medium">{{ $stats['requests'] ?? 0 }}</div>
                                    <div class="mt-2 text-[11px] leading-tight uppercase tracking-wide text-gray-800">Total Requests</div>
                                </div>
                                <div class="w-px bg-gray-400/70"></div>
                                <div class="flex-1 text-[10px] uppercase tracking-wide text-gray-800 space-y-2">
                                    <div>
                                        <div class="text-[16px] leading-none font-semibold text-gray-900">{{ $stats['approved'] ?? 0 }}</div>
                                        Approved
                                    </div>
                                    <div>
                                        <div class="text-[16px] leading-none font-semibold text-gray-900">{{ $stats['pending'] ?? 0 }}</div>
                                        Pending
                                    </div>
                                    <div>
                                        <div class="text-[16px] leading-none font-semibold text-gray-900">{{ $stats['rejected'] ?? 0 }}</div>
                                        Rejected
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="glass text-gray-900">
                            <div class="p-4">
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-indigo-600" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M4 4h16v2H4V4zm0 4h16v2H4V8zm0 4h12v2H4v-2zm0 4h8v2H4v-2z"/></svg>
                                    <div class="text-[11px] uppercase tracking-wide text-gray-800">
                                        {{ (($stats['pending'] ?? 0) > 0) ? 'New Request' : 'Request' }}
                                    </div>
                                </div>
                                <div class="mt-4 text-[34px] leading-none font-medium">
                                    {{ (($stats['pending'] ?? 0) > 0) ? ($stats['pending'] ?? 0) : ($stats['requests'] ?? 0) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div x-data="hrCalendar()" class="mt-8 space-y-4">
                        <div class="glass p-4">
                            <div class="flex flex-wrap items-end gap-4">
                                <div>
                                    <label class="block text-xs font-medium uppercase tracking-wide text-gray-600">Department</label>
                                    <input x-model="filters.department" type="text" placeholder="e.g. Finance" class="mt-1 h-10 w-48 rounded-lg border-gray-300 bg-white px-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium uppercase tracking-wide text-gray-600">Employee</label>
                                    <input x-model="filters.employee" type="text" placeholder="e.g. Juan Dela Cruz" class="mt-1 h-10 w-56 rounded-lg border-gray-300 bg-white px-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium uppercase tracking-wide text-gray-600">Leave Type</label>
                                    <input x-model="filters.type" type="text" placeholder="e.g. Vacation" class="mt-1 h-10 w-48 rounded-lg border-gray-300 bg-white px-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium uppercase tracking-wide text-gray-600">Start</label>
                                    <input x-model="filters.start" type="date" class="mt-1 h-10 rounded-lg border-gray-300 bg-white px-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium uppercase tracking-wide text-gray-600">End</label>
                                    <input x-model="filters.end" type="date" class="mt-1 h-10 rounded-lg border-gray-300 bg-white px-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium uppercase tracking-wide text-gray-600">Status</label>
                                    <select x-model="filters.status" class="mt-1 h-10 rounded-lg border-gray-300 bg-white px-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Any</option>
                                        <option value="pending">Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </div>
                                <div class="ml-auto flex items-center gap-2">
                                    <div class="inline-flex rounded-lg bg-gray-100 p-1">
                                        <button @click="setView('month')" :class="view==='month' ? 'bg-white shadow text-gray-900' : 'text-gray-600'" class="px-3 py-1.5 rounded-md text-sm">Month</button>
                                        <button @click="setView('week')" :class="view==='week' ? 'bg-white shadow text-gray-900' : 'text-gray-600'" class="px-3 py-1.5 rounded-md text-sm">Week</button>
                                        <button @click="setView('day')" :class="view==='day' ? 'bg-white shadow text-gray-900' : 'text-gray-600'" class="px-3 py-1.5 rounded-md text-sm">Day</button>
                                    </div>
                                    <button @click="prev()" class="h-10 rounded-lg bg-white px-3 text-sm shadow-sm ring-1 ring-gray-200 hover:bg-gray-50">Prev</button>
                                    <button @click="today()" class="h-10 rounded-lg bg-white px-3 text-sm shadow-sm ring-1 ring-gray-200 hover:bg-gray-50">Today</button>
                                    <button @click="next()" class="h-10 rounded-lg bg-white px-3 text-sm shadow-sm ring-1 ring-gray-200 hover:bg-gray-50">Next</button>
                                    <button @click="reload()" class="h-10 rounded-lg bg-indigo-600 px-4 text-white shadow-sm hover:bg-indigo-700">Reload</button>
                                    <button @click="exportData('csv')" class="h-10 rounded-lg bg-white px-4 text-sm shadow-sm ring-1 ring-gray-200 hover:bg-gray-50">Export Excel</button>
                                    <button @click="exportData('pdf')" class="h-10 rounded-lg bg-white px-4 text-sm shadow-sm ring-1 ring-gray-200 hover:bg-gray-50">Export PDF</button>
                                </div>
                            </div>
                            <template x-if="error">
                                <div class="mt-3 rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700" x-text="error"></div>
                            </template>
                        </div>

                        <div class="glass p-4">
                            <div class="flex items-center justify-between">
                                <div class="text-lg font-semibold text-gray-800" x-text="rangeLabel"></div>
                                <div class="flex items-center gap-4 text-xs">
                                    <template x-for="legend in legends" :key="legend.name">
                                        <div class="flex items-center gap-2">
                                            <span class="h-3 w-3 rounded" :style="`background:${legend.color}`"></span>
                                            <span x-text="legend.name"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div x-show="view==='month'" class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-7 gap-3">
                                <template x-for="d in monthDays" :key="d.iso">
                                    <div class="rounded-lg border border-gray-200 bg-white p-2 min-h-[130px] cursor-pointer hover:bg-gray-50" @click="openDay(d.iso)">
                                        <div class="flex items-center justify-between text-xs text-gray-500">
                                            <span x-text="d.label"></span>
                                            <span x-text="d.date"></span>
                                        </div>
                                        <div class="mt-2 space-y-1">
                                            <template x-for="e in eventsForDay(d.iso).slice(0,3)" :key="e.id+'-'+d.iso">
                                                <div class="group relative flex items-center gap-2 rounded-md px-2 py-1 text-xs" :style="`background:${typeColor(e.type)}20;border:1px solid ${typeColor(e.type)}40`" :title="tooltip(e)">
                                                    <div class="truncate" x-text="e.user.name + ' • ' + e.type"></div>
                                                    <div class="ml-auto" x-text="e.days + 'd'"></div>
                                                </div>
                                            </template>
                                            <template x-if="eventsForDay(d.iso).length > 3">
                                                <div class="text-xs text-gray-500">+ <span x-text="eventsForDay(d.iso).length - 3"></span> more</div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <div x-show="view==='week'" class="mt-4 grid grid-cols-1 lg:grid-cols-7 gap-3">
                                <template x-for="d in weekDays" :key="d.iso">
                                    <div class="rounded-lg border border-gray-200 bg-white p-2 min-h-[130px] cursor-pointer hover:bg-gray-50" @click="openDay(d.iso)">
                                        <div class="flex items-center justify-between text-xs text-gray-500">
                                            <span x-text="d.label"></span>
                                            <span x-text="d.date"></span>
                                        </div>
                                        <div class="mt-2 space-y-1">
                                            <template x-for="e in eventsForDay(d.iso)" :key="e.id+'-'+d.iso">
                                                <div class="group relative flex items-center gap-2 rounded-md px-2 py-1 text-xs" :style="`background:${typeColor(e.type)}20;border:1px solid ${typeColor(e.type)}40`" :title="tooltip(e)">
                                                    <div class="truncate" x-text="e.user.name + ' • ' + e.type"></div>
                                                    <div class="ml-auto" x-text="e.days + 'd'"></div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <div x-show="view==='day'" class="mt-4">
                                <div class="rounded-lg border border-gray-200 bg-white">
                                    <template x-if="eventsForDay(currentIso).length === 0">
                                        <div class="p-6 text-sm text-gray-600">No leave events today.</div>
                                    </template>
                                    <template x-for="e in eventsForDay(currentIso)" :key="e.id">
                                        <div class="flex items-center gap-3 border-t border-gray-100 px-4 py-3 text-sm first:border-t-0" :style="`background:${typeColor(e.type)}20;border-left:3px solid ${typeColor(e.type)}`" :title="tooltip(e)">
                                            <div class="font-medium truncate" x-text="e.user.name"></div>
                                            <div class="text-gray-600 truncate" x-text="e.type"></div>
                                            <div class="ml-auto text-gray-700" x-text="fmt(e.start)+' → '+fmt(e.end)"></div>
                                            <div class="text-gray-900 font-medium" x-text="e.days + ' days'"></div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <script>
                        function hrCalendar(){
                            const today = new Date();
                            const iso = (d)=>d.toISOString().slice(0,10);
                            const addDays=(d,n)=>{const x=new Date(d);x.setDate(x.getDate()+n);return x};
                            const startOfWeek=(d)=>{const x=new Date(d);const day=x.getDay();return addDays(x,-((day+6)%7))};
                            const startOfMonth=(d)=>{const x=new Date(d);x.setDate(1);return x};
                            const endOfMonth=(d)=>{const x=new Date(d);x.setMonth(x.getMonth()+1);x.setDate(0);return x};
                            const typePalette = {
                                'Vacation':'#22c55e','Sick':'#ef4444','Personal':'#3b82f6','Emergency':'#f59e0b','Maternity':'#a855f7','Paternity':'#06b6d4'
                            };
                            return {
                                view:'month',
                                cursor: today,
                                currentIso: iso(today),
                                rangeLabel:'',
                                error:'',
                                events:[],
                                legends: Object.entries(typePalette).map(([name,color])=>({name,color})),
                                filters:{ department:'', employee:'', type:'', start:'', end:'', status:'' },
                                modalOpen:false,
                                modalIso:'',
                                modalLabel:'',
                                modalPage:1,
                                pageSize:12,
                                init(){
                                    this.computeRange();
                                    this.reload();
                                    setInterval(()=>this.reload(true),15000);
                                    ['department','employee','type','start','end','status'].forEach(k=>{
                                        this.$watch('filters.'+k, ()=> this.reload());
                                    });
                                },
                                typeColor(type){ return typePalette[type] || '#64748b'; },
                                setView(v){ this.view=v; this.computeRange(); },
                                prev(){ if(this.view==='month'){ this.cursor.setMonth(this.cursor.getMonth()-1); } else { this.cursor=addDays(this.cursor,-7); } this.computeRange(); },
                                next(){ if(this.view==='month'){ this.cursor.setMonth(this.cursor.getMonth()+1); } else { this.cursor=addDays(this.cursor,7); } this.computeRange(); },
                                today(){ this.cursor = new Date(); this.currentIso = iso(this.cursor); this.computeRange(); },
                                openDay(dayIso){
                                    this.modalIso = dayIso;
                                    const d = new Date(dayIso);
                                    this.modalLabel = d.toLocaleDateString(undefined,{weekday:'long', month:'long', day:'numeric', year:'numeric'});
                                    this.modalPage = 1;
                                    this.modalOpen = true;
                                },
                                closeModal(){ this.modalOpen = false; },
                                modalPageCount(){
                                    const total = this.eventsForDay(this.modalIso).length;
                                    return Math.max(1, Math.ceil(total / this.pageSize));
                                },
                                pageEvents(){
                                    const list = this.eventsForDay(this.modalIso);
                                    const start = (this.modalPage - 1) * this.pageSize;
                                    const end = start + this.pageSize;
                                    return list.slice(start, end);
                                },
                                prevPage(){ if (this.modalPage > 1) this.modalPage--; },
                                nextPage(){ if (this.modalPage < this.modalPageCount()) this.modalPage++; },
                                computeRange(){
                                    if(this.view==='month'){
                                        const s=startOfMonth(this.cursor), e=endOfMonth(this.cursor);
                                        this.rangeLabel = s.toLocaleString(undefined,{month:'long', year:'numeric'});
                                        const days=[];
                                        const startGrid=startOfWeek(s);
                                        for(let i=0;i<42;i++){
                                            const cur=addDays(startGrid,i);
                                            days.push({ iso: iso(cur), label: cur.toLocaleDateString(undefined,{weekday:'short'}), date: cur.getDate() });
                                        }
                                        this.monthDays=days;
                                    }else if(this.view==='week'){
                                        const s=startOfWeek(this.cursor);
                                        this.rangeLabel = s.toLocaleDateString(undefined,{month:'short',day:'numeric'})+' → '+addDays(s,6).toLocaleDateString(undefined,{month:'short',day:'numeric'});
                                        this.weekDays=[...Array(7)].map((_,i)=>{const d=addDays(s,i);return { iso: iso(d), label: d.toLocaleDateString(undefined,{weekday:'short'}), date: d.getDate() }});
                                    }else{
                                        this.rangeLabel = new Date(this.cursor).toLocaleDateString(undefined,{weekday:'long', month:'long', day:'numeric'});
                                        this.currentIso = iso(this.cursor);
                                    }
                                },
                                async reload(silent=false){
                                    try{
                                        const p=new URLSearchParams();
                                        if(this.filters.department) p.set('department', this.filters.department);
                                        if(this.filters.employee) p.set('employee', this.filters.employee);
                                        if(this.filters.type) p.set('type', this.filters.type);
                                        if(this.filters.start) p.set('start', this.filters.start);
                                        if(this.filters.end) p.set('end', this.filters.end);
                                        if(this.filters.status) p.set('status', this.filters.status);
                                        const res = await fetch('{{ route('hr.calendar.data') }}?'+p.toString(), { headers:{'Accept':'application/json'} });
                                        if(!res.ok) throw new Error('Failed to load calendar data ('+res.status+')');
                                        const json = await res.json();
                                        this.events = Array.isArray(json.events) ? json.events : [];
                                        this.error='';
                                    }catch(e){
                                        if(!silent) this.error = e.message || 'Failed to load calendar data';
                                    }
                                },
                                exportData(fmt){
                                    const p=new URLSearchParams();
                                    if(this.filters.department) p.set('department', this.filters.department);
                                    if(this.filters.employee) p.set('employee', this.filters.employee);
                                    if(this.filters.type) p.set('type', this.filters.type);
                                    if(this.filters.start) p.set('start', this.filters.start);
                                    if(this.filters.end) p.set('end', this.filters.end);
                                    if(this.filters.status) p.set('status', this.filters.status);
                                    p.set('format', fmt);
                                    window.open('{{ route('hr.calendar.export') }}?'+p.toString(), '_blank');
                                },
                                eventsForDay(isoDay){
                                    return this.events.filter(e=> e.start<=isoDay && e.end>=isoDay );
                                },
                                tooltip(e){
                                    return `${e.user.name} • ${e.type}\n${e.start} → ${e.end}\n${e.days} day(s)\nStatus: ${e.status}\nDept: ${e.user.department||'-'}\nReason: ${e.reason||'-'}\nApprover: ${e.approver||'-'}\nContact: ${e.user.email||'-'} ${e.user.phone?'/ '+e.user.phone:''}`;
                                },
                                fmt(x){
                                    const d=new Date(x); return d.toLocaleDateString(undefined,{month:'short',day:'numeric'});
                                }
                            }
                        }
                        </script>
                        <div x-cloak x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center">
                            <div class="absolute inset-0 bg-black/40" @click="closeModal()" aria-hidden="true"></div>
                            <div role="dialog" aria-modal="true" class="relative w-full max-w-md rounded-2xl bg-white shadow-lg">
                                <div class="p-5">
                                    <h3 class="text-xl font-semibold text-gray-900" x-text="modalLabel"></h3>
                                    <p class="mt-1 text-sm text-gray-600" x-text="modalIso"></p>
                                    <div class="mt-4 text-sm">
                                        <template x-if="eventsForDay(modalIso).length===0">
                                            <div class="text-gray-600">No leave events.</div>
                                        </template>
                                        <template x-for="e in pageEvents()" :key="e.id">
                                            <div class="mt-2 flex items-center gap-2">
                                                <span class="inline-block h-2 w-2 rounded" :style="`background:${typeColor(e.type)}`"></span>
                                                <div class="font-medium truncate" x-text="e.user.name"></div>
                                                <div class="ml-auto text-gray-700" x-text="e.type + ' • ' + e.days + 'd'"></div>
                                            </div>
                                        </template>
                                        <div class="mt-4 flex items-center justify-between text-xs text-gray-700" x-show="eventsForDay(modalIso).length>0">
                                            <div>
                                                <span x-text="((modalPage-1)*pageSize)+1"></span>–
                                                <span x-text="Math.min(modalPage*pageSize, eventsForDay(modalIso).length)"></span>
                                                of
                                                <span x-text="eventsForDay(modalIso).length"></span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <button class="rounded-md px-3 py-1 ring-1 ring-gray-300 disabled:opacity-50" @click="prevPage()" :disabled="modalPage<=1">Prev</button>
                                                <span>Page <span x-text="modalPage"></span> / <span x-text="modalPageCount()"></span></span>
                                                <button class="rounded-md px-3 py-1 ring-1 ring-gray-300 disabled:opacity-50" @click="nextPage()" :disabled="modalPage>=modalPageCount()">Next</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-6 flex justify-end">
                                        <button class="rounded-lg bg-white px-4 py-2 text-sm shadow-sm ring-1 ring-gray-200 hover:bg-gray-50" @click="closeModal()">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
