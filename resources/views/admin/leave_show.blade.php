<x-app-layout>
    <div class="py-0">
        <div class="px-0">
            <div class="grid grid-cols-1 lg:grid-cols-[256px_minmax(0,1fr)] gap-6">
                @include('admin.partials.sidebar')
                <section>
                    @if (session('status'))
                        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
                    @endif
                    <div class="bg-white p-6 shadow sm:rounded-lg">
                        <div class="flex items-center justify-between mb-4">
                            <div class="text-lg font-medium">Leave Request Details</div>
                            <a href="{{ route('admin.leaves') }}" class="px-3 py-2 bg-gray-200 text-gray-800 rounded">Back</a>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <div class="text-sm text-gray-500">User</div>
                                <div class="text-gray-900 font-medium">{{ $leave->user->name ?? '-' }}</div>
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
                                <div class="text-gray-900">{{ $leave->start_date }} → {{ $leave->end_date }} ({{ $leave->days }} days)</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500">Current Status</div>
                                <div>
                                    <span class="px-2 py-1 rounded text-sm {{ $leave->status === 'approved' ? 'bg-green-100 text-green-800' : ($leave->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">{{ ucfirst($leave->status) }}</span>
                                </div>
                            </div>
                            <div class="md:col-span-2">
                                <div class="text-sm text-gray-500">Reason</div>
                                <div class="text-gray-900 whitespace-pre-line">{{ $leave->reason ?: '—' }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500">Approved By</div>
                                <div class="text-gray-900">
                                    @if ($leave->approver)
                                        {{ trim(($leave->approver->first_name ?? '').' '.($leave->approver->middle_name ?? '').' '.($leave->approver->last_name ?? '')) }}
                                    @else
                                        —
                                    @endif
                                </div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500">Approved At</div>
                                @if ($leave->approved_at)
                                <div class="text-gray-900">
                                    <time datetime="{{ $leave->approved_at->toIso8601String() }}" data-realtime="true"></time>
                                </div>
                                @else
                                <div class="text-gray-900">—</div>
                                @endif
                            </div>
                        </div>
                        <div class="border-t mt-6 pt-6">
                            <form action="{{ route('admin.leaves.status', $leave) }}" method="POST" class="flex flex-wrap items-center gap-3">
                                @csrf
                                <label class="text-sm text-gray-600">Change Status</label>
                                <select name="status" class="border-gray-300 rounded-md shadow-sm">
                                    <option value="pending" @selected($leave->status==='pending')>Pending</option>
                                    <option value="approved" @selected($leave->status==='approved')>Approved</option>
                                    <option value="rejected" @selected($leave->status==='rejected')>Rejected</option>
                                </select>
                                <button class="px-4 py-2 bg-indigo-600 text-white rounded">Update Status</button>
                            </form>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
<script>
(function(){
 const nodes=document.querySelectorAll('[data-realtime="true"]');
 if(!nodes.length)return;
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
   nodes.forEach(n=>{
     const iso=n.getAttribute('datetime');
     if(!iso)return;
     const d=new Date(iso);
     n.textContent=fmtAbs.format(d)+' ('+rel(d)+')';
   });
 }
 tick();
 setInterval(tick,30000);
})();
</script>
</x-app-layout>
