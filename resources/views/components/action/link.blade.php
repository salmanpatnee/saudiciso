@props([
    'link' => '#',
    'class' => '',
    'label' => '',
    'label_ar' => '',
])


<a {{ $attributes->merge(['class' => "bg-gradient-to-br from-[#f0cf3a] to-[#fff1a1] font-bold hover:-translate-y-0.5 hover:shadow-[0_14px_32px_rgba(240,207,58,.45)] inline-flex items-center p-3 rounded-full shadow-[0_10px_24px_rgba(240,207,58,.3)] text-sm text-[#04130e] transition-all $class"]) }}
    href="{{ $link }}">
    <span class="inline mx-2">{{ $label }}</span>
    <span class="inline text-xs font-semibold leading-tight " dir="rtl" lang="ar">{{ $label_ar }}</span>

</a>
