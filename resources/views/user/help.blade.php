<x-app-layout>
    <div class="hr-shell min-h-[calc(100vh-4rem)] bg-[#f4f5f9]" x-data="{ sidebarCollapsed: false }">
        <div class="grid min-h-[calc(100vh-4rem)] grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] lg:transition-[grid-template-columns] lg:duration-200 lg:ease-out" :class="sidebarCollapsed ? 'lg:grid-cols-[88px_minmax(0,1fr)]' : 'lg:grid-cols-[280px_minmax(0,1fr)]'">
            @include('user.partials.sidebar')
            <section class="p-4 sm:p-6 lg:p-8" x-data="{ q: '' }">
                <div class="max-w-5xl">
                    <h1 class="text-4xl font-semibold tracking-tight text-gray-900">Help & Support</h1>
                    <p class="mt-2 text-gray-600">Guides, FAQs, and contact options.</p>

                    <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-3">
                        <article class="glass p-5">
                            <div class="text-lg font-semibold text-gray-900">Quick Actions</div>
                            <div class="mt-3 space-y-2">
                                <a href="{{ route('leaves.create') }}" class="block rounded-xl bg-indigo-600 px-4 py-2 text-white shadow hover:bg-indigo-700">Create Leave Request</a>
                                <a href="{{ route('leaves.index') }}" class="block rounded-xl border border-gray-300 bg-white px-4 py-2 text-gray-900 shadow-sm hover:bg-gray-50">View My Requests</a>
                                <a href="{{ route('user.credits') }}" class="block rounded-xl border border-gray-300 bg-white px-4 py-2 text-gray-900 shadow-sm hover:bg-gray-50">View Leave Credits</a>
                                
                            </div>
                        </article>

                        <article class="glass p-5 lg:col-span-2">
                            <div class="text-lg font-semibold text-gray-900">FAQs</div>
                            <div class="mt-3">
                            
                            </div>
                            <div class="mt-3 space-y-4">
                                <div x-show="q === '' || 'apply'.includes(q.toLowerCase()) || 'apply for leave'.includes(q.toLowerCase())">
                                    <div class="font-medium text-gray-900">How do I apply for leave?</div>
                                    <div class="text-sm text-gray-700">Go to Leave Application, select the type of leave, fill out details, and submit. You can track approvals on your Requests page.</div>
                                </div>
                                <div x-show="q === '' || 'status'.includes(q.toLowerCase()) || 'approval'.includes(q.toLowerCase())">
                                    <div class="font-medium text-gray-900">Where can I see my approval status?</div>
                                    <div class="text-sm text-gray-700">Notifications show status changes. You can also check the Status column on your Requests page.</div>
                                </div>
                                <div x-show="q === '' || 'download'.includes(q.toLowerCase()) || 'pdf'.includes(q.toLowerCase())">
                                    <div class="font-medium text-gray-900">How do I download the approved form?</div>
                                    <div class="text-sm text-gray-700">When approved, open the notification or your Requests page and click View PDF to open or download the official form.</div>
                                </div>
                                <div x-show="q === '' || 'edit'.includes(q.toLowerCase()) || 'submitted'.includes(q.toLowerCase())">
                                    <div class="font-medium text-gray-900">Can I edit a submitted request?</div>
                                    <div class="text-sm text-gray-700">Only pending requests can be edited. If already approved or rejected, create a new request if changes are needed.</div>
                                </div>
                                <div x-show="q === '' || 'credits'.includes(q.toLowerCase())">
                                    <div class="font-medium text-gray-900">How are credits deducted?</div>
                                    <div class="text-sm text-gray-700">Credits are held upon submission and applied on final approval. See Recent Holds on the Credits page.</div>
                                </div>
                            </div>
                        </article>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
                        <article class="glass p-5">
                            <div class="text-lg font-semibold text-gray-900">Troubleshooting</div>
                            <ul class="mt-3 space-y-2 text-sm text-gray-700">
                                <li>PDF not showing names or checks: refresh and ensure approvals are completed by HR, DC, and RD/ARD.</li>
                                <li>Avatar not updating: re-upload photo; browser cache clears via versioned URL.</li>
                                <li>Missing notifications: check the bell icon; use the Notifications page for history.</li>
                                <li>Wrong status color: chips use Approved, Pending, Rejected everywhere for consistency.</li>
                            </ul>
                        </article>
                        <article class="glass p-5">
                            <div class="text-lg font-semibold text-gray-900">Contact</div>
                            <div class="mt-3 space-y-2 text-sm text-gray-700">
                                <div>HR Office: for credits and verification</div>
                                <div>Division Chief: for recommendations</div>
                                <div>RD/ARD: final approval concerns</div>
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-700">Email:</span>
                                    <a href="https://mail.google.com/mail/?view=cm&fs=1&to=aquinokevin688@gmail.com" target="_blank" rel="noopener" class="text-indigo-600 hover:underline">aquinokevin688@gmail.com</a>
                                </div>
                                <div>Phone: <span class="text-gray-900">09514964991</span></div>
                            </div>
                        </article>
                        <article class="glass p-5">
                            <div class="text-lg font-semibold text-gray-900">System Tips</div>
                            <ul class="mt-3 space-y-2 text-sm text-gray-700">
                                <li>Keep profile information updated to reflect on PDF.</li>
                                <li>Ensure details of leave fields are complete for accurate checks.</li>
                                <li>Use the sidebar to navigate faster between sections.</li>
                                <li>Use Notifications to jump directly to PDF view on approval.</li>
                            </ul>
                        </article>
                    </div>

                    @php
                        $role = auth()->user()?->role;
                        $route = null;
                        if ($role === 'hr') $route = route('hr.downloads');
                        elseif ($role === 'dc' || $role === 'lgmed') $route = route('dc.downloads');
                        elseif ($role === 'rd') $route = route('rd.downloads');
                        elseif ($role === 'ard') $route = route('ard.downloads');
                    @endphp
                    @if($route)
                    <div class="mt-6 glass p-5">
                        <div class="text-lg font-semibold text-gray-900">Staff Shortcuts</div>
                        <div class="mt-3 space-y-2">
                            <a href="{{ $route }}" class="inline-flex items-center rounded-xl bg-amber-500 px-4 py-2 text-white shadow hover:bg-amber-600">Approved Requests Downloads</a>
                        </div>
                    </div>
                    @endif
                </div>
            </section>
            </div>
        </div>
    </div>
</x-app-layout>
