@extends('layouts.user')
@section('title', 'Activity Detail')

@section('content')
    @php
        $panels = [
            'Query Parameters' => $log->query_params,
            'Request Payload' => $log->payload,
            'Metadata' => $log->meta,
        ];
    @endphp

    <div>
        <x-table.action-wrapper title="Activity Detail">
            @if ($log->visitor_id)
                <x-action.button label="View Timeline" route_name="activity-logs.timeline"
                    :route_param="['visitor_id' => $log->visitor_id]" />
            @endif
            <x-action.button label="Back to Logs" route_name="activity-logs.index" />
        </x-table.action-wrapper>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 mb-6 px-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <span class="text-sm text-gray-500 dark:text-gray-400">Actor</span>
                <h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">{{ $log->displayActor() }}</h4>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $log->user_email ?? 'Anonymous visitor' }}
                    @if ($log->role_name)
                        · {{ $log->role_name }}
                    @endif
                </p>
                @if ($log->wasLinkedAfterTheFact())
                    <p class="mt-2 text-xs text-blue-700">Captured anonymously; attributed to this user at login on
                        <time
                            data-local-datetime="{{ $log->linked_at->toIso8601String() }}">{{ $log->linked_at->format('d M Y, h:i A') }}</time>.
                    </p>
                @endif
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <span class="text-sm text-gray-500 dark:text-gray-400">Activity</span>
                <div class="mt-2"><x-activity-type-badge :type="$log->activity_type" /></div>
                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ $log->description ?? '—' }}</p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <span class="text-sm text-gray-500 dark:text-gray-400">When</span>
                <h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">
                    <time
                        data-local-datetime="{{ $log->occurred_at->toIso8601String() }}">{{ $log->occurred_at->format('d M Y, h:i A') }}</time>
                </h4>
                <p class="mt-1 text-sm text-gray-500">{{ $log->occurred_at->diffForHumans() }}</p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <span class="text-sm text-gray-500 dark:text-gray-400">Response</span>
                <div class="mt-2"><x-activity-status-badge :status="$log->status_code" /></div>
                <p class="mt-2 text-sm text-gray-500">Took {{ $log->displayDuration() }}</p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <span class="text-sm text-gray-500 dark:text-gray-400">Network</span>
                <h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">{{ $log->ip_address ?? '—' }}
                </h4>
                <p class="mt-1 text-sm text-gray-500">{{ $log->displayLocation() }}</p>
                @if ($log->isp)
                    <p class="text-xs text-gray-400">{{ $log->isp }}</p>
                @endif
                @if ($log->geo_timezone)
                    <p class="text-xs text-gray-400">{{ $log->geo_timezone }}</p>
                @endif
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <span class="text-sm text-gray-500 dark:text-gray-400">Device</span>
                <h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">
                    {{ $log->browser ?? 'Unknown' }}</h4>
                <p class="mt-1 text-sm text-gray-500">{{ $log->platform ?? 'Unknown' }} ·
                    {{ $log->device_type ?? 'Unknown' }}</p>
            </div>
        </div>

        <div class="px-2 mb-6">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="font-bold text-[#050739] mb-4">Request</h3>
                <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-400">Method</dt>
                        <dd class="text-sm text-gray-800 overflow-x-auto">{{ $log->method }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-400">Route Name</dt>
                        <dd class="text-sm text-gray-800 overflow-x-auto">{{ $log->route_name ?? '—' }}</dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="text-xs uppercase tracking-wide text-gray-400">URL</dt>
                        <dd class="text-sm text-gray-800 overflow-x-auto">{{ $log->url ?? '—' }}
                        </dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="text-xs uppercase tracking-wide text-gray-400">Controller Action</dt>
                        <dd class="text-sm text-gray-800 overflow-x-auto">
                            {{ $log->controller_action ?? '—' }}</dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="text-xs uppercase tracking-wide text-gray-400">Referer</dt>
                        <dd class="text-sm text-gray-800 overflow-x-auto">{{ $log->referer ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-400">AJAX</dt>
                        <dd class="text-sm text-gray-800 overflow-x-auto">{{ $log->is_ajax ? 'Yes' : 'No' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-400">Module</dt>
                        <dd class="text-sm text-gray-800 overflow-x-auto">{{ $log->module ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-400">Visitor ID</dt>
                        <dd class="text-sm text-gray-800 overflow-x-auto">
                            {{ $log->visitor_id ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-400">Session ID (hashed)</dt>
                        <dd class="text-sm text-gray-800 overflow-x-auto">
                            {{ $log->session_id ?? '—' }}</dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="text-xs uppercase tracking-wide text-gray-400">User Agent</dt>
                        <dd class="text-sm text-gray-800 overflow-x-auto">{{ $log->user_agent ?? '—' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="px-2 mb-6 grid grid-cols-1 gap-4 lg:grid-cols-3">
            @foreach ($panels as $panelTitle => $panelData)
                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                    <h3 class="font-bold text-[#050739]">{{ $panelTitle }}</h3>
                    <p class="mt-1 mb-3 text-xs text-gray-400">Sensitive fields are redacted at capture time, so
                        <span class="font-semibold">[REDACTED]</span> means the value was never stored.
                    </p>
                    @if ($panelData)
                        <pre class="max-h-64 overflow-auto rounded-lg bg-gray-50 p-3 text-xs text-gray-800">{{ json_encode($panelData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                    @else
                        <p class="text-sm text-gray-400">Nothing recorded.</p>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="px-2">
            <x-table.action-wrapper title="Nearby Activity" />

            <div class="rounded-[18px] border border-[rgba(5,7,57,.08)] overflow-auto custom-scrollbar">
                <table class="w-full border-collapse">
                    <x-table.thead>
                        <x-table.th label="Time" />
                        <x-table.th label="Activity" />
                        <x-table.th label="Description" />
                        <x-table.th label="Method" />
                        <x-table.th label="Path" />
                        <x-table.th label="Status" />
                        <x-table.th label="Details" />
                    </x-table.thead>
                    <x-table.tbody>
                        @forelse ($nearby as $row)
                            <tr>
                                <x-table.td>
                                    <time class="whitespace-nowrap"
                                        data-local-time="{{ $row->occurred_at->toIso8601String() }}">{{ $row->occurred_at->format('h:i:s A') }}</time>
                                </x-table.td>
                                <x-table.td><x-activity-type-badge :type="$row->activity_type" /></x-table.td>
                                <x-table.td>{{ $row->description ?? '—' }}</x-table.td>
                                <x-table.td>{{ $row->method }}</x-table.td>
                                <x-table.td><span class="text-xs">/{{ $row->path }}</span></x-table.td>
                                <x-table.td><x-activity-status-badge :status="$row->status_code" /></x-table.td>
                                <x-table.td action_col="true">
                                    <x-action.view route_name="activity-logs.show" param="{{ $row->id }}" />
                                </x-table.td>
                            </tr>
                        @empty
                            <tr>
                                <x-table.td class="text-center" colspan="7">No surrounding activity recorded.
                                </x-table.td>
                            </tr>
                        @endforelse
                    </x-table.tbody>
                </table>
            </div>
        </div>
    </div>

    @include('process.activity-logs.partials.local-time')
@endsection
