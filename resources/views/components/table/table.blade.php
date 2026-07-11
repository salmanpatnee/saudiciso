@props([
    'action_col' => 'false',
])

<div class="rounded-[18px] border border-[rgba(5,7,57,.08)] overflow-hidden">
    <div class="max-w-full overflow-x-auto custom-scrollbar">
        <table class="w-full border-collapse">
            {{ $slot }}
        </table>
    </div>
</div>
