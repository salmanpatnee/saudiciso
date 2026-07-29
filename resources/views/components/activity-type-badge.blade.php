@props(['type' => null])

@if ($type)
    <span
        class="inline-flex items-center whitespace-nowrap rounded-full px-2 py-0.5 text-xs font-medium {{ $type->color() }}">
        {{ $type->label() }}
    </span>
@else
    <span class="text-gray-400">—</span>
@endif
