<x-app-layout>
    @if (Auth::user()->role === 'hr')
    <div class="hr-shell min-h-[calc(100vh-4rem)] bg-[#f4f5f9]" x-data="{ sidebarCollapsed: false }">
        <div class="grid min-h-[calc(100vh-4rem)] grid-cols-1 lg:grid-cols-[280px_minmax(0,1fr)] lg:transition-[grid-template-columns] lg:duration-200 lg:ease-out" :class="sidebarCollapsed ? 'lg:grid-cols-[88px_minmax(0,1fr)]' : 'lg:grid-cols-[280px_minmax(0,1fr)]'">
            @include('hr.partials.sidebar')
            <section class="p-4 sm:p-6 lg:p-8">
                @if (session('status'))
                    <div class="mb-4 rounded-lg bg-green-100 p-3 text-green-800">{{ session('status') }}</div>
                @endif
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow">
                    <div class="mb-4 flex items-center justify-between">
                        <div class="text-xl font-semibold text-gray-800">Categories</div>
                        <a href="{{ route('categories.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-white transition hover:bg-indigo-700">Add Category</a>
                    </div>
                    @php
                        $sick = []; $vac = []; $other = [];
                        foreach ($categories as $c) {
                            $n = mb_strtolower($c->name ?? '');
                            if (str_contains($n, 'sick')) { $sick[] = $c; }
                            elseif (str_contains($n, 'vacation')) { $vac[] = $c; }
                            else { $other[] = $c; }
                        }
                        $sections = [
                            ['title' => 'SICK LEAVE', 'items' => $sick, 'query' => 'sick'],
                            ['title' => 'VACATION LEAVE', 'items' => $vac, 'query' => 'vacation'],
                            ['title' => 'NO MINUS CREDITS LEAVE', 'items' => $other, 'query' => 'other'],
                        ];
                    @endphp
                    @foreach ($sections as $sec)
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-2">
                                <div class="text-sm font-semibold text-gray-800">{{ $sec['title'] }}</div>
                                <a href="{{ route('categories.create', ['group' => $sec['query']]) }}" class="rounded-lg bg-indigo-600 px-3 py-1.5 text-white hover:bg-indigo-700">Add Category</a>
                            </div>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Type of Leave</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Vacation Leave</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Sick Leave</th>
                                        <th class="px-4 py-2"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @forelse ($sec['items'] as $category)
                                    <tr>
                                        <td class="px-4 py-2">- {{ $category->name }}@if($category->description) ({{ $category->description }}) @endif</td>
                                        <td class="px-4 py-2">{{ number_format((float)($category->vl_default_credits ?? 0), 3) }}</td>
                                        <td class="px-4 py-2">{{ number_format((float)($category->sl_default_credits ?? 0), 3) }}</td>
                                        <td class="space-x-2 px-4 py-2 text-right">
                                            <a href="{{ route('categories.edit', $category) }}" class="text-indigo-600 hover:underline">Edit</a>
                                            <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Delete this category?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td class="px-4 py-4 text-gray-500" colspan="4">No categories.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                    <div class="mt-4 flex items-center justify-between">
                        <div>
                            {{ $categories->links() }}
                        </div>
                        <a href="{{ route('hr.leaves') }}" class="rounded border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50">Next</a>
                    </div>
                </div>
            </section>
        </div>
    </div>
    @else
    <div class="py-0">
        <div class="px-0">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-[256px_minmax(0,1fr)]">
                @include('admin.partials.sidebar')
                <section>
                    @if (session('status'))
                        <div class="mb-4 rounded bg-green-100 p-3 text-green-800">{{ session('status') }}</div>
                    @endif
                    <div class="bg-white p-6 shadow sm:rounded-lg">
                        <div class="mb-4 flex items-center justify-between">
                            <div class="text-lg font-medium">Categories</div>
                            <a href="{{ route('categories.create') }}" class="rounded bg-indigo-600 px-4 py-2 text-white">Add Category</a>
                        </div>
                        @php
                            $sick = []; $vac = []; $other = [];
                            foreach ($categories as $c) {
                                $n = mb_strtolower($c->name ?? '');
                                if (str_contains($n, 'sick')) { $sick[] = $c; }
                                elseif (str_contains($n, 'vacation')) { $vac[] = $c; }
                                else { $other[] = $c; }
                            }
                            $sections = [
                                ['title' => 'SICK LEAVE', 'items' => $sick, 'query' => 'sick'],
                                ['title' => 'VACATION LEAVE', 'items' => $vac, 'query' => 'vacation'],
                                ['title' => 'NO MINUS CREDITS LEAVE', 'items' => $other, 'query' => 'other'],
                            ];
                        @endphp
                        @foreach ($sections as $sec)
                            <div class="mb-6">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="text-sm font-semibold text-gray-800">{{ $sec['title'] }}</div>
                                    <a href="{{ route('categories.create', ['group' => $sec['query']]) }}" class="rounded bg-indigo-600 px-3 py-1.5 text-white">Add Category</a>
                                </div>
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Type of Leave</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Vacation Leave</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Sick Leave</th>
                                            <th class="px-4 py-2"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        @forelse ($sec['items'] as $category)
                                        <tr>
                                            <td class="px-4 py-2">- {{ $category->name }}@if($category->description) ({{ $category->description }}) @endif</td>
                                            <td class="px-4 py-2">{{ number_format((float)($category->vl_default_credits ?? 0), 3) }}</td>
                                            <td class="px-4 py-2">{{ number_format((float)($category->sl_default_credits ?? 0), 3) }}</td>
                                            <td class="space-x-2 px-4 py-2 text-right">
                                                <a href="{{ route('categories.edit', $category) }}" class="text-indigo-600 hover:underline">Edit</a>
                                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Delete this category?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td class="px-4 py-4 text-gray-500" colspan="4">No categories.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                        @php
                            $nextUrl = Auth::user()->role === 'admin' ? route('admin.leaves') : route('leaves.index');
                        @endphp
                        <div class="mt-4 flex items-center justify-between">
                            <div>
                                {{ $categories->links() }}
                            </div>
                            <a href="{{ $nextUrl }}" class="rounded border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50">Next</a>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    @endif
</x-app-layout>
