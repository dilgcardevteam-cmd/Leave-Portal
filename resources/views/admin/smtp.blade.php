<x-app-layout>
    <div class="py-0">
        <div class="px-0">
            <div class="grid grid-cols-1 lg:grid-cols-[256px_minmax(0,1fr)] gap-6">
                @include('admin.partials.sidebar')
                <section>
                    <div class="rounded-2xl bg-gradient-to-r from-[#0d1f4d] to-[#1e3a8a] p-6 text-white shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-2xl sm:text-3xl font-semibold">SMTP Settings</div>
                                <div class="mt-1 text-sm text-white/80">Review mail configuration and send a test email.</div>
                            </div>
                            <a href="https://laravel.com/docs/mail" target="_blank" class="inline-flex items-center rounded-xl bg-white/10 px-4 py-2 text-white shadow hover:bg-white/20">Docs</a>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-6 lg:grid-cols-3">
                        <article class="glass p-6">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M4 4h16v2H4V4zm0 4h16v10H4V8zm2 2h12v6H6v-6z"/></svg>
                                </div>
                                <div class="text-lg font-semibold text-gray-900">General</div>
                            </div>
                            <div class="mt-4 space-y-3 text-sm">
                                <div class="flex items-center justify-between">
                                    <div class="text-gray-600">Default Mailer</div>
                                    <div class="font-mono text-gray-900">{{ $mail['default'] ?? '—' }}</div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="text-gray-600">From Address</div>
                                    <div class="font-mono text-gray-900">{{ $from['address'] ?? '—' }}</div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="text-gray-600">From Name</div>
                                    <div class="font-mono text-gray-900">{{ $from['name'] ?? '—' }}</div>
                                </div>
                            </div>
                        </article>

                        <article class="glass p-6 lg:col-span-2">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 13l8-5-8-5-8 5 8 5zm0 2l-8-5v7l8 5 8-5v-7l-8 5z"/></svg>
                                </div>
                                <div class="text-lg font-semibold text-gray-900">SMTP Transport</div>
                                <span class="ml-auto inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700">env-driven</span>
                            </div>
                            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div class="rounded-xl border border-gray-200 bg-white p-4">
                                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Host</div>
                                    <div class="mt-1 font-mono text-gray-900">{{ $smtp['host'] ?? '—' }}</div>
                                </div>
                                <div class="rounded-xl border border-gray-200 bg-white p-4">
                                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Port</div>
                                    <div class="mt-1 font-mono text-gray-900">{{ $smtp['port'] ?? '—' }}</div>
                                </div>
                                <div class="rounded-xl border border-gray-200 bg-white p-4">
                                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Scheme</div>
                                    <div class="mt-1 font-mono text-gray-900">{{ $smtp['scheme'] ?? '—' }}</div>
                                </div>
                                <div class="rounded-xl border border-gray-200 bg-white p-4">
                                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">URL</div>
                                    <div class="mt-1 font-mono text-gray-900 break-all">{{ $smtp['url'] ?? '—' }}</div>
                                </div>
                                <div class="rounded-xl border border-gray-200 bg-white p-4">
                                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Username</div>
                                    <div class="mt-1 font-mono text-gray-900">{{ $smtp['username'] ?? '—' }}</div>
                                </div>
                                <div class="rounded-xl border border-gray-200 bg-white p-4">
                                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Password</div>
                                    <div class="mt-1 font-mono text-gray-900">{{ isset($smtp['password']) && $smtp['password'] ? '••••••••' : '—' }}</div>
                                </div>
                                <div class="rounded-xl border border-gray-200 bg-white p-4 sm:col-span-2">
                                    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">EHLO Domain</div>
                                    <div class="mt-1 font-mono text-gray-900">{{ $smtp['local_domain'] ?? '—' }}</div>
                                </div>
                            </div>
                        </article>
                    </div>

                    @if (session('status'))
                        <div class="mt-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('status') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
                    @endif

                    <div class="mt-4 grid grid-cols-1 gap-6 lg:grid-cols-3">
                        <article class="glass p-6">
                            <div class="text-lg font-semibold text-gray-900">Send Test Email</div>
                            <p class="mt-1 text-sm text-gray-600">Verify SMTP by sending a sample message.</p>
                            <form method="POST" action="{{ route('admin.smtp.test') }}" class="mt-4 flex flex-wrap items-center gap-3">
                                @csrf
                                <input type="email" name="to" placeholder="Recipient email" class="w-64 rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('to', Auth::user()->email) }}" required>
                                <button type="submit" class="inline-flex items-center rounded-xl bg-[#0d1f4d] px-4 py-2 text-white shadow-sm hover:brightness-95">Send</button>
                            </form>
                        </article>
                        <article class="glass p-6 lg:col-span-2">
                            <div class="text-lg font-semibold text-gray-900">How to Update</div>
                            <ol class="mt-2 list-decimal list-inside text-sm text-gray-700 space-y-1">
                                <li>Set MAIL_MAILER=smtp and related variables in .env.</li>
                                <li>Run php artisan config:clear then php artisan config:cache.</li>
                                <li>Use “Send Test Email” to confirm delivery.</li>
                            </ol>
                            <div class="mt-3 text-xs text-gray-500">Do not commit secrets to source control.</div>
                        </article>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
