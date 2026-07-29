@extends('layouts.user')
@section('title', 'Activity Timeline')

@section('content')
    <div>
        <x-table.action-wrapper title="Activity Timeline">
            <x-action.button label="Back to Logs" route_name="activity-logs.index" />
        </x-table.action-wrapper>

        @include('process.activity-logs.partials.filters', ['action' => route('activity-logs.timeline')])

        @if ($journeys !== null)
            {{-- No visitor selected: list the journeys in range. --}}
            <div class="rounded-[18px] border border-[rgba(5,7,57,.08)] overflow-auto custom-scrollbar">
                <table class="w-full border-collapse">
                    <x-table.thead>
                        <x-table.th label="Visitor" />
                        <x-table.th label="Identified As" />
                        <x-table.th label="First Seen" />
                        <x-table.th label="Last Seen" />
                        <x-table.th label="Events" />
                        <x-table.th label="Logins" />
                        <x-table.th label="Location" />
                        <x-table.th label="Device" />
                        <x-table.th label="Journey" />
                    </x-table.thead>
                    <x-table.tbody>
                        @forelse ($journeys as $journey)
                            <tr>
                                <x-table.td><span
                                        class="text-xs">{{ \Illuminate\Support\Str::substr($journey->visitor_id, 0, 8) }}</span>
                                </x-table.td>
                                <x-table.td>{{ $journey->user_name ?? 'Anonymous' }}</x-table.td>
                                <x-table.td>
                                    <time class="whitespace-nowrap"
                                        data-local-datetime="{{ \Illuminate\Support\Carbon::parse($journey->started_at)->toIso8601String() }}">{{ $journey->started_at }}</time>
                                </x-table.td>
                                <x-table.td>
                                    <time class="whitespace-nowrap"
                                        data-local-datetime="{{ \Illuminate\Support\Carbon::parse($journey->ended_at)->toIso8601String() }}">{{ $journey->ended_at }}</time>
                                </x-table.td>
                                <x-table.td>{{ number_format($journey->events) }}</x-table.td>
                                <x-table.td>{{ (int) $journey->logins }}</x-table.td>
                                <x-table.td>{{ trim(implode(', ', array_filter([$journey->city, $journey->country]))) ?: '—' }}
                                </x-table.td>
                                <x-table.td>{{ ($journey->browser ?? 'Unknown') . ' / ' . ($journey->device_type ?? 'Unknown') }}
                                </x-table.td>
                                <x-table.td action_col="true">
                                    <a href="{{ route('activity-logs.timeline', ['visitor_id' => $journey->visitor_id, 'period' => $period, 'date_from' => $date_from, 'date_to' => $date_to]) }}"
                                        class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 transition hover:bg-gray-50">
                                        View journey
                                    </a>
                                </x-table.td>
                            </tr>
                        @empty
                            <tr>
                                <x-table.td class="text-center" colspan="9">No visitor journeys in this period.
                                </x-table.td>
                            </tr>
                        @endforelse
                    </x-table.tbody>
                </table>
            </div>

            <x-pagination>
                {{ $journeys->links() }}
            </x-pagination>
        @else
            {{-- A specific visitor's story, grouped by session with gap markers. --}}
            @php
                $sessionGroups = $events->groupBy(fn($event) => $event->session_id ?? 'no-session');
                $maxEvents = (int) config('activity-log.ui.timeline_max_events', 500);
            @endphp

            <div class="px-2 mb-4 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="font-bold text-[#050739]">Visitor <span class="text-sm">{{ $visitorId }}</span></h3>
                <p class="mt-1 text-sm text-gray-500">
                    {{ number_format($totalEvents) }} event(s) in this period.
                    @if ($totalEvents > $maxEvents)
                        Showing the first {{ number_format($maxEvents) }}; narrow the date range to see the rest.
                    @endif
                </p>
            </div>

            <div class="px-2">
                @forelse ($sessionGroups as $sessionId => $group)
                    @php $first = $group->first(); @endphp

                    <div class="mb-4 rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-white/[0.03]">
                        <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600 dark:text-gray-300">
                            <span class="font-semibold text-[#050739] dark:text-white/90">
                                {{ $sessionId === 'no-session' ? 'No session' : 'Session ' . \Illuminate\Support\Str::substr($sessionId, 0, 10) }}
                            </span>
                            <span>{{ $first->displayLocation() }}</span>
                            <span>{{ ($first->browser ?? 'Unknown') . ' / ' . ($first->platform ?? 'Unknown') }}</span>
                            <span>{{ $first->device_type ?? 'Unknown' }}</span>
                            <span>{{ $first->ip_address ?? '—' }}</span>
                            <span>{{ $group->count() }} event(s)</span>
                        </div>
                    </div>

                    <ol class="relative ml-3 border-l border-gray-200 dark:border-gray-800 mb-8">
                        @foreach ($group as $event)
                            @php
                                $previous = $group->values()->get($loop->index - 1);
                                $gapMinutes = $previous
                                    ? $previous->occurred_at->diffInMinutes($event->occurred_at)
                                    : 0;
                            @endphp

                            @if ($gapMinutes > 30)
                                <li class="ml-3 mb-6 text-xs uppercase tracking-wide text-gray-400">
                                    — {{ \App\Models\UserSession::formatDuration($gapMinutes * 60) }} gap —
                                </li>
                            @endif

                            <li class="ml-3 mb-6">
                                <span
                                    class="absolute -left-1 mt-1.5 h-3 w-3 rounded-full border border-white {{ $event->activity_type->dotColor() }}"></span>

                                <div class="flex flex-wrap items-center gap-2">
                                    <time class="text-sm font-semibold text-gray-800 dark:text-white/90"
                                        data-local-time="{{ $event->occurred_at->toIso8601String() }}">{{ $event->occurred_at->format('h:i:s A') }}</time>
                                    <x-activity-type-badge :type="$event->activity_type" />
                                    <x-activity-status-badge :status="$event->status_code" />
                                    <span class="text-xs text-gray-400">{{ $event->displayDuration() }}</span>
                                </div>

                                <p class="mt-1 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $event->description ?? '—' }}
                                    @if ($event->user_name && $loop->first)
                                        <span class="text-gray-400">· {{ $event->user_name }}</span>
                                    @endif
                                </p>

                                <p class="mt-0.5 text-xs text-gray-400">
                                    {{ $event->method }} /{{ $event->path }}
                                    <a href="{{ route('activity-logs.show', $event->id) }}"
                                        class="ml-2 text-brand-600 hover:underline">details</a>
                                </p>
                            </li>
                        @endforeach
                    </ol>
                @empty
                    <p class="text-sm text-gray-500">No activity recorded for this visitor in the selected period.</p>
                @endforelse
            </div>
        @endif
    </div>

    @include('process.activity-logs.partials.local-time')
@endsection
