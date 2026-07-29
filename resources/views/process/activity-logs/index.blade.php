@extends('layouts.user')
@section('title', 'Activity Logs')

@section('content')
    <div>
        <x-table.action-wrapper title="Activity Logs">
            <x-action.button label="Timeline View" route_name="activity-logs.timeline" />
        </x-table.action-wrapper>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5 mb-6 px-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <span class="text-sm text-gray-500 dark:text-gray-400">Events Today</span>
                <h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">
                    {{ number_format($summary['total_events']) }}</h4>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <span class="text-sm text-gray-500 dark:text-gray-400">Unique Visitors</span>
                <h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">
                    {{ number_format($summary['unique_visitors']) }}</h4>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <span class="text-sm text-gray-500 dark:text-gray-400">Signed-in Users</span>
                <h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">
                    {{ number_format($summary['unique_users']) }}</h4>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <span class="text-sm text-gray-500 dark:text-gray-400">Failed Logins</span>
                <h4 class="mt-2 text-title-sm font-bold text-error-600 dark:text-error-500">
                    {{ number_format($summary['failed_logins']) }}</h4>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <span class="text-sm text-gray-500 dark:text-gray-400">Errors &amp; Denials</span>
                <h4 class="mt-2 text-title-sm font-bold text-warning-600 dark:text-white/90">
                    {{ number_format($summary['error_events']) }}</h4>
            </div>
        </div>

        @include('process.activity-logs.partials.filters', ['action' => route('activity-logs.index')])

        <div class="rounded-[18px] border border-[rgba(5,7,57,.08)] max-h-[70vh] overflow-auto custom-scrollbar">
            <table class="w-full border-collapse">
                <x-table.thead class="sticky top-0 z-30">
                    <x-table.th label="S.No" />
                    <x-table.th label="Actor" class="sticky left-0 z-40 bg-brand-950 border-r border-white/10" />
                    <x-table.th label="When" />
                    <x-table.th label="Activity" />
                    <x-table.th label="Description" />
                    <x-table.th label="Method" />
                    <x-table.th label="Path" />
                    <x-table.th label="Status" />
                    <x-table.th label="Duration" />
                    <x-table.th label="IP Address" />
                    <x-table.th label="Location" />
                    <x-table.th label="Device" />
                    <x-table.th label="Details" />
                </x-table.thead>
                <x-table.tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <x-table.td><x-table.serial :loop="$loop" :paginator="$logs" /></x-table.td>
                            <x-table.td
                                class="sticky left-0 z-10 bg-gray-50 dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800">
                                <span class="whitespace-nowrap">{{ $log->displayActor() }}</span>
                                @if ($log->role_name)
                                    <span class="block text-xs text-gray-400">{{ $log->role_name }}</span>
                                @endif
                                @if ($log->wasLinkedAfterTheFact())
                                    {{-- Anonymous at capture time, attributed to this user at login. --}}
                                    <span
                                        class="mt-1 inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-600/20"
                                        title="Captured anonymously, linked to this user when they logged in">Linked</span>
                                @endif
                            </x-table.td>
                            <x-table.td>
                                <time class="whitespace-nowrap"
                                    data-local-datetime="{{ $log->occurred_at->toIso8601String() }}">{{ $log->occurred_at->format('d M Y, h:i A') }}</time>
                            </x-table.td>
                            <x-table.td><x-activity-type-badge :type="$log->activity_type" /></x-table.td>
                            <x-table.td>{{ $log->description ?? '—' }}</x-table.td>
                            <x-table.td>{{ $log->method }}</x-table.td>
                            <x-table.td><span class="text-xs">/{{ $log->path }}</span></x-table.td>
                            <x-table.td><x-activity-status-badge :status="$log->status_code" /></x-table.td>
                            <x-table.td>{{ $log->displayDuration() }}</x-table.td>
                            <x-table.td>{{ $log->ip_address ?? '—' }}</x-table.td>
                            <x-table.td>{{ $log->displayLocation() }}</x-table.td>
                            <x-table.td>
                                {{ ($log->browser ?? 'Unknown') . ' / ' . ($log->platform ?? 'Unknown') }}
                                <span class="block text-xs text-gray-400">{{ $log->device_type ?? 'Unknown' }}</span>
                            </x-table.td>
                            <x-table.td action_col="true">
                                <x-action.view route_name="activity-logs.show" param="{{ $log->id }}" />
                                @if ($log->visitor_id)
                                    <a href="{{ route('activity-logs.timeline', ['visitor_id' => $log->visitor_id]) }}"
                                        title="View visitor timeline"
                                        class="inline-flex items-center justify-center p-1.5 rounded-lg text-gray-500 transition-colors hover:bg-gray-50 hover:text-blue-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                    </a>
                                @endif
                                <x-action.delete route_name="activity-logs.destroy" param="{{ $log->id }}" />
                            </x-table.td>
                        </tr>
                    @empty
                        <tr>
                            <x-table.td class="text-center" colspan="13">No activity found for the selected filters.
                            </x-table.td>
                        </tr>
                    @endforelse
                </x-table.tbody>
            </table>
        </div>

        <x-pagination>
            {{ $logs->links() }}
        </x-pagination>
    </div>

    @include('process.activity-logs.partials.local-time')
@endsection
