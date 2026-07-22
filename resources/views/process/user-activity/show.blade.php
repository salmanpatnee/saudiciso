@extends('layouts.user')
@section('title', 'Session Details')

@section('content')
    <div>
        <x-table.action-wrapper title="Session Details">
            <x-action.button label="Back to User Activity" route_name="user-activity.index" />
        </x-table.action-wrapper>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6 px-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <span class="text-sm text-gray-500 dark:text-gray-400">User</span>
                <h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">{{ $session->user->first_name . ' ' . $session->user->last_name }}</h4>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <span class="text-sm text-gray-500 dark:text-gray-400">Status</span>
                <h4 class="mt-2"><x-online-indicator :online="$session->isCurrentlyOnline()" /></h4>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <span class="text-sm text-gray-500 dark:text-gray-400">Login Time</span>
                <h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">
                    <time data-local-datetime="{{ $session->login_at->toIso8601String() }}">{{ $session->login_at->format('d M Y, h:i A') }}</time>
                </h4>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <span class="text-sm text-gray-500 dark:text-gray-400">Logout Time</span>
                <h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">
                    @if ($session->logout_at)
                        <time data-local-datetime="{{ $session->logout_at->toIso8601String() }}">{{ $session->logout_at->format('d M Y, h:i A') }}</time>
                    @else
                        —
                    @endif
                </h4>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <span class="text-sm text-gray-500 dark:text-gray-400">Duration</span>
                <h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">{{ $session->durationForHumans() }}</h4>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <span class="text-sm text-gray-500 dark:text-gray-400">Browser / Platform</span>
                <h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">{{ ($session->browser ?? 'Unknown') . ' / ' . ($session->platform ?? 'Unknown') }}</h4>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <span class="text-sm text-gray-500 dark:text-gray-400">IP Address</span>
                <h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">{{ $session->ip_address ?? '—' }}</h4>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-2xl border border-brand-200/70 bg-gradient-to-br from-brand-50 via-white to-brand-50/40 p-6 mb-6 mx-2 shadow-sm dark:border-brand-800/60 dark:from-brand-500/10 dark:via-white/[0.02] dark:to-brand-500/5">
            <span class="text-xs font-semibold uppercase tracking-wider text-brand-600 dark:text-brand-400">Activity Summary</span>
            <p class="mt-1.5 text-[15px] leading-relaxed text-gray-700 dark:text-gray-300">
                @if ($summary)
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $session->user->first_name }}</span>
                    visited
                    <span class="inline-flex items-center rounded-full bg-brand-50 px-2 py-0.5 text-theme-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">{{ $summary['top_page_label'] }}</span>
                    most ({{ $summary['top_page_visits'] }} {{ $summary['top_page_visits'] === 1 ? 'visit' : 'visits' }}),
                    stayed longest on
                    <span class="inline-flex items-center rounded-full bg-brand-50 px-2 py-0.5 text-theme-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">{{ $summary['longest_stay_label'] }}</span>
                    ({{ $summary['longest_stay_duration'] }}), and shows the strongest interest in
                    <span class="inline-flex items-center rounded-full bg-brand-50 px-2 py-0.5 text-theme-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">{{ $summary['top_module'] }}</span>
                    based on time spent this session.
                @else
                    Not enough page-visit activity was recorded this session to summarize.
                @endif
            </p>
        </div>

        <div class="mx-2">
            <div class="rounded-[18px] border border-[rgba(5,7,57,.08)] max-h-[70vh] overflow-auto custom-scrollbar">
                <table class="w-full border-collapse">
                    <x-table.thead class="sticky top-0 z-30">
                        <x-table.th label="Time" />
                        <x-table.th label="Page" />
                        <x-table.th label="Route" />
                    </x-table.thead>
                    <x-table.tbody>
                        @forelse ($activities as $activity)
                            <tr>
                                <x-table.td>
                                    <time data-local-datetime="{{ $activity->occurred_at->toIso8601String() }}">{{ $activity->occurred_at->format('d M Y, h:i A') }}</time>
                                </x-table.td>
                                <x-table.td>{{ $activity->label ?? $activity->url }}</x-table.td>
                                <x-table.td>{{ $activity->url }}</x-table.td>
                            </tr>
                        @empty
                            <tr>
                                <x-table.td class="text-center" colspan="3">No page visits recorded for this session.</x-table.td>
                            </tr>
                        @endforelse
                    </x-table.tbody>
                </table>
            </div>

            <x-pagination>
                {{ $activities->links() }}
            </x-pagination>
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-local-datetime]').forEach(function (el) {
            var date = new Date(el.getAttribute('data-local-datetime'));

            if (isNaN(date.getTime())) {
                return;
            }

            el.textContent = new Intl.DateTimeFormat(undefined, {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: true,
            }).format(date);
        });
    </script>
@endsection
