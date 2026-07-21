@props(['online' => false])

<span class="inline-flex items-center gap-1.5 text-theme-sm font-medium {{ $online ? 'text-success-600 dark:text-success-500' : 'text-gray-500 dark:text-gray-400' }}">
    <span class="h-2 w-2 rounded-full {{ $online ? 'bg-success-500 animate-pulse' : 'bg-gray-400' }}"></span>
    {{ $online ? 'Online' : 'Offline' }}
</span>
