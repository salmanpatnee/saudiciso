@props(['status' => null])

@php
    $tone = match (true) {
        $status === null => 'bg-gray-100 text-gray-600',
        $status >= 500 => 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20',
        $status >= 400 => 'bg-yellow-50 text-yellow-700',
        $status >= 300 => 'bg-gray-100 text-gray-700',
        default => 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20',
    };
@endphp

<span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold {{ $tone }}">
    {{ $status ?? '—' }}
</span>
