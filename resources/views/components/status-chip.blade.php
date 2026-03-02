@php
    $s = mb_strtolower(trim((string)($status ?? '')));
    $label = $label ?? ucfirst($s);
    $cls = 'bg-gray-100 text-gray-800';
    if ($s === 'approved') $cls = 'bg-green-100 text-green-700';
    elseif ($s === 'pending') $cls = 'bg-yellow-100 text-yellow-800';
    elseif ($s === 'rejected') $cls = 'bg-red-100 text-red-700';
@endphp
<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $cls }}">{{ $label }}</span>
