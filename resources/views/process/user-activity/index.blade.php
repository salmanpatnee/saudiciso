@extends('layouts.user')
@section('title', 'User Activity')

@section('content')
    <div>
        <x-table.action-wrapper title="User Activity" />

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5 mb-6 px-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <span class="text-sm text-gray-500 dark:text-gray-400">Online Now</span>
                <h4 class="mt-2 text-title-sm font-bold text-success-600 dark:text-success-500">{{ $summary['online_now'] }}</h4>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <span class="text-sm text-gray-500 dark:text-gray-400">Today's Logins</span>
                <h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">{{ $summary['todays_logins'] }}</h4>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <span class="text-sm text-gray-500 dark:text-gray-400">Today's Logouts</span>
                <h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">{{ $summary['todays_logouts'] }}</h4>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <span class="text-sm text-gray-500 dark:text-gray-400">Total Active Sessions</span>
                <h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">{{ $summary['active_sessions'] }}</h4>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
                <span class="text-sm text-gray-500 dark:text-gray-400">Avg. Duration Today</span>
                <h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">{{ $summary['avg_duration_today'] }}</h4>
            </div>
        </div>

        <div class="px-2 mb-4" x-data="{ period: '{{ $period }}' }">
            <form method="GET" action="{{ route('user-activity.index') }}">
                <x-form.grid-4-col>
                    <x-form.input label="Search" name="search" value="{{ $search }}"
                        placeholder="Search by name or username" onchange="this.form.submit()" />

                    <x-form.select label="Period" name="period"
                        :custom_data="['Today', 'This Week', 'This Month', 'Custom Range']" :value="$period"
                        x-model="period" onchange="this.form.submit()" />

                    <x-form.select label="Presence" name="presence" :custom_data="['All', 'Online', 'Offline']"
                        :value="$presence" onchange="this.form.submit()" />

                    <x-form.select label="Sort by" name="sort_by"
                        :custom_data="['Newest Login', 'Oldest Login', 'Newest Logout', 'Longest Duration', 'Shortest Duration']"
                        :value="$sortBy" onchange="this.form.submit()" />
                </x-form.grid-4-col>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3 mt-4" x-show="period === 'Custom Range'" x-cloak>
                    <x-form.label label="From" for="date_from" />
                    <input type="date" id="date_from" name="date_from" value="{{ $dateFrom }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" />

                    <x-form.label label="To" for="date_to" />
                    <input type="date" id="date_to" name="date_to" value="{{ $dateTo }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10" />

                    <x-form.submit label="Filter" isUpdate="true" type="submit" />
                </div>
            </form>
        </div>

        <div class="rounded-[18px] border border-[rgba(5,7,57,.08)] max-h-[70vh] overflow-auto custom-scrollbar">
            <table class="w-full border-collapse">
            <x-table.thead class="sticky top-0 z-30">
                <x-table.th label="S.No" />
                <x-table.th label="User" class="sticky left-0 z-40 bg-brand-950 border-r border-white/10" />
                <x-table.th label="Status" />
                <x-table.th label="Login Time" />
                <x-table.th label="Logout Time" />
                <x-table.th label="Active Duration" />
                <x-table.th label="Total Logins" />
                <x-table.th label="Last Activity" />
                <x-table.th label="IP Address" />
                <x-table.th label="Browser / Device" />
                <x-table.th label="Details" />
            </x-table.thead>
            <x-table.tbody>
                @forelse ($sessions as $session)
                    <tr>
                        <x-table.td><x-table.serial :loop="$loop" :paginator="$sessions" /></x-table.td>
                        <x-table.td class="sticky left-0 z-10 bg-gray-50 dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800">{{ $session->user->first_name . ' ' . $session->user->last_name }}</x-table.td>
                        <x-table.td><x-online-indicator :online="$session->isCurrentlyOnline()" /></x-table.td>
                        <x-table.td>
                            <time data-local-datetime="{{ $session->login_at->toIso8601String() }}">{{ $session->login_at->format('d M Y, h:i A') }}</time>
                        </x-table.td>
                        <x-table.td>
                            @if ($session->logout_at)
                                <time data-local-datetime="{{ $session->logout_at->toIso8601String() }}">{{ $session->logout_at->format('d M Y, h:i A') }}</time>
                            @else
                                —
                            @endif
                        </x-table.td>
                        <x-table.td>{{ $session->durationForHumans() }}</x-table.td>
                        <x-table.td>{{ $totalLoginsByUser[$session->user_id] ?? 0 }}</x-table.td>
                        <x-table.td>{{ $session->last_activity_at?->diffForHumans() ?? '—' }}</x-table.td>
                        <x-table.td>{{ $session->ip_address ?? '—' }}</x-table.td>
                        <x-table.td>{{ ($session->browser ?? 'Unknown') . ' / ' . ($session->platform ?? 'Unknown') }}</x-table.td>
                        <x-table.td action_col="true">
                            <x-action.view route_name="user-activity.show" param="{{ $session->id }}" />
                            <x-action.delete route_name="user-activity.destroy" param="{{ $session->id }}" />
                        </x-table.td>
                    </tr>
                @empty
                    <tr>
                        <x-table.td class="text-center" colspan="11">No activity found for the selected filters.</x-table.td>
                    </tr>
                @endforelse
            </x-table.tbody>
            </table>
        </div>

        <x-pagination>
            {{ $sessions->links() }}
        </x-pagination>
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
