<?php

namespace App\Http\Controllers;

use App\Enums\ActivityLogType;
use App\Models\ActivityLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    /**
     * Columns deliberately excluded from list queries: payload, meta,
     * query_params, url, user_agent and referer. They dominate row size, and on
     * a page of 25 rows omitting them is the difference between reading a few
     * kilobytes and a few hundred.
     *
     * @var array<int, string>
     */
    private const LIST_COLUMNS = [
        'id', 'occurred_at', 'user_id', 'user_name', 'role_name', 'is_authenticated', 'linked_at',
        'visitor_id', 'session_id', 'activity_type', 'description', 'label', 'module',
        'method', 'path', 'status_code', 'duration_ms', 'ip_address',
        'country', 'country_code', 'city', 'region', 'browser', 'platform', 'device_type',
    ];

    public function index(Request $request): View
    {
        $filters = $this->filters($request);
        [$rangeStart, $rangeEnd] = $this->resolveDateRange(
            $filters['period'],
            $filters['date_from'],
            $filters['date_to']
        );

        $logs = $this->filteredQuery($request, $rangeStart, $rangeEnd)
            ->select(self::LIST_COLUMNS)
            ->orderByDesc('occurred_at')
            ->orderByDesc('id')
            ->paginate((int) config('activity-log.ui.per_page', 25))
            ->withQueryString();

        return view('process.activity-logs.index', array_merge($filters, [
            'logs' => $logs,
            'summary' => $this->summary(),
            'types' => ActivityLogType::options(),
            'modules' => $this->moduleOptions(),
        ]));
    }

    public function show(ActivityLog $activityLog): View
    {
        $nearby = ActivityLog::query()
            ->select(self::LIST_COLUMNS)
            ->where('id', '!=', $activityLog->id)
            ->when(
                $activityLog->session_id,
                fn ($query, $sessionId) => $query->forSession($sessionId),
                fn ($query) => $query->forVisitor((string) $activityLog->visitor_id)
            )
            ->whereBetween('occurred_at', [
                $activityLog->occurred_at->copy()->subMinutes(10),
                $activityLog->occurred_at->copy()->addMinutes(10),
            ])
            ->orderBy('occurred_at')
            ->limit(20)
            ->get();

        return view('process.activity-logs.show', [
            'log' => $activityLog,
            'nearby' => $nearby,
        ]);
    }

    /**
     * Without a visitor_id this lists journeys; with one it renders that
     * visitor's chronological story.
     */
    public function timeline(Request $request): View
    {
        $filters = $this->filters($request);
        [$rangeStart, $rangeEnd] = $this->resolveDateRange(
            $filters['period'],
            $filters['date_from'],
            $filters['date_to']
        );

        $visitorId = $request->input('visitor_id');

        if (! $visitorId) {
            return view('process.activity-logs.timeline', array_merge($filters, [
                'journeys' => $this->journeys($rangeStart, $rangeEnd),
                'events' => null,
                'visitorId' => null,
                'totalEvents' => 0,
                'types' => ActivityLogType::options(),
                'modules' => $this->moduleOptions(),
            ]));
        }

        $max = (int) config('activity-log.ui.timeline_max_events', 500);

        $totalEvents = ActivityLog::query()
            ->forVisitor($visitorId)
            ->forDateRange($rangeStart, $rangeEnd)
            ->count();

        $events = ActivityLog::query()
            ->select(self::LIST_COLUMNS)
            ->forVisitor($visitorId)
            ->forDateRange($rangeStart, $rangeEnd)
            ->orderBy('occurred_at')
            ->orderBy('id')
            ->limit($max)
            ->get();

        return view('process.activity-logs.timeline', array_merge($filters, [
            'journeys' => null,
            'events' => $events,
            'visitorId' => $visitorId,
            'totalEvents' => $totalEvents,
            'types' => ActivityLogType::options(),
            'modules' => $this->moduleOptions(),
        ]));
    }

    public function destroy(ActivityLog $activityLog): RedirectResponse
    {
        try {
            $activityLog->delete();

            return redirect()->back()->with('success', 'Activity log entry deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Could not delete this activity log entry.');
        }
    }

    public function purge(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'days' => 'required|integer|min:1',
        ], [
            'days.required' => 'Specify how many days of history to keep.',
            'days.integer' => 'The retention window must be a whole number of days.',
            'days.min' => 'The retention window must be at least 1 day.',
        ]);

        try {
            $cutoff = now()->subDays((int) $validated['days']);
            $chunk = (int) config('activity-log.retention.chunk', 5000);
            $total = 0;

            do {
                $deleted = ActivityLog::query()
                    ->where('occurred_at', '<', $cutoff)
                    ->limit($chunk)
                    ->delete();

                $total += $deleted;
            } while ($deleted > 0);

            return redirect()->back()->with('success', "Purged {$total} activity log entries.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Could not purge activity log entries.');
        }
    }

    /**
     * The date range is applied unconditionally, never inside a when(). It is
     * the only thing standing between this page and a full table scan.
     */
    private function filteredQuery(Request $request, Carbon $rangeStart, Carbon $rangeEnd): \Illuminate\Database\Eloquent\Builder
    {
        $search = $request->input('search');
        $audience = $request->input('audience', 'All');
        $statusGroup = $request->input('status_group', 'All');

        return ActivityLog::query()
            ->forDateRange($rangeStart, $rangeEnd)
            ->when($search, fn ($query, $value) => $query->where(function ($sub) use ($value) {
                $sub->where('user_name', 'like', "%{$value}%")
                    ->orWhere('user_email', 'like', "%{$value}%")
                    ->orWhere('description', 'like', "%{$value}%")
                    ->orWhere('path', 'like', "{$value}%");
            }))
            ->when($audience === 'Authenticated', fn ($query) => $query->whereNotNull('user_id'))
            ->when($audience === 'Guests', fn ($query) => $query->whereNull('user_id'))
            ->when($request->input('type'), fn ($query, $value) => $query->where('activity_type', $value))
            ->when($request->input('module'), fn ($query, $value) => $query->where('module', $value))
            ->when($request->input('method'), fn ($query, $value) => $query->where('method', $value))
            ->when($request->input('role_id'), fn ($query, $value) => $query->where('role_id', $value))
            ->when($statusGroup === 'Success', fn ($query) => $query->whereBetween('status_code', [200, 299]))
            ->when($statusGroup === 'Redirect', fn ($query) => $query->whereBetween('status_code', [300, 399]))
            ->when($statusGroup === 'Client Error', fn ($query) => $query->whereBetween('status_code', [400, 499]))
            ->when($statusGroup === 'Server Error', fn ($query) => $query->where('status_code', '>=', 500))
            ->when($request->input('ip'), fn ($query, $value) => $query->where('ip_address', $value))
            ->when($request->input('country_code'), fn ($query, $value) => $query->where('country_code', $value))
            ->when($request->input('device_type'), fn ($query, $value) => $query->where('device_type', $value))
            ->when($request->input('visitor_id'), fn ($query, $value) => $query->where('visitor_id', $value))
            ->when($request->input('session_id'), fn ($query, $value) => $query->where('session_id', $value));
    }

    /**
     * One query rather than six, mirroring how UserActivityController avoids
     * per-row aggregate lookups.
     *
     * @return array<string, mixed>
     */
    private function summary(): array
    {
        $row = ActivityLog::query()
            ->forDateRange(now()->startOfDay(), now()->endOfDay())
            ->selectRaw('count(*) as total_events')
            ->selectRaw('count(distinct visitor_id) as unique_visitors')
            ->selectRaw('count(distinct user_id) as unique_users')
            ->selectRaw("sum(activity_type = 'login_failed') as failed_logins")
            ->selectRaw('sum(status_code >= 400) as error_events')
            ->first();

        return [
            'total_events' => (int) ($row->total_events ?? 0),
            'unique_visitors' => (int) ($row->unique_visitors ?? 0),
            'unique_users' => (int) ($row->unique_users ?? 0),
            'failed_logins' => (int) ($row->failed_logins ?? 0),
            'error_events' => (int) ($row->error_events ?? 0),
        ];
    }

    /**
     * A GROUP BY over a wide date range will filesort a large intermediate set
     * even with the visitor index, which is why the period filter defaults to
     * Today and pagination is small.
     */
    private function journeys(Carbon $rangeStart, Carbon $rangeEnd): LengthAwarePaginator
    {
        return ActivityLog::query()
            ->forDateRange($rangeStart, $rangeEnd)
            ->whereNotNull('visitor_id')
            ->selectRaw('visitor_id')
            ->selectRaw('min(occurred_at) as started_at')
            ->selectRaw('max(occurred_at) as ended_at')
            ->selectRaw('count(*) as events')
            ->selectRaw('max(user_name) as user_name')
            ->selectRaw('max(country) as country')
            ->selectRaw('max(city) as city')
            ->selectRaw('max(browser) as browser')
            ->selectRaw('max(device_type) as device_type')
            ->selectRaw("sum(activity_type = 'login_success') as logins")
            ->groupBy('visitor_id')
            ->orderByRaw('min(occurred_at) desc')
            ->paginate(20)
            ->withQueryString();
    }

    /**
     * Cached because DISTINCT on an unindexed column is expensive.
     */
    private function moduleOptions(): Collection
    {
        return Cache::remember(
            'activity-log:modules',
            300,
            fn () => ActivityLog::query()
                ->distinct()
                ->whereNotNull('module')
                ->orderBy('module')
                ->pluck('module')
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function filters(Request $request): array
    {
        return [
            'search' => $request->input('search'),
            'period' => $request->input('period', config('activity-log.ui.default_period', 'Today')),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'audience' => $request->input('audience', 'All'),
            'type' => $request->input('type'),
            'module' => $request->input('module'),
            'method' => $request->input('method'),
            'status_group' => $request->input('status_group', 'All'),
            'role_id' => $request->input('role_id'),
            'ip' => $request->input('ip'),
            'country_code' => $request->input('country_code'),
            'device_type' => $request->input('device_type'),
            'session_id' => $request->input('session_id'),
        ];
    }

    /**
     * Mirrors UserActivityController's range helpers. Kept local rather than
     * extracted, so the working legacy controller is not touched by this work.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    private function resolveDateRange(string $period, ?string $dateFrom, ?string $dateTo): array
    {
        return match ($period) {
            'This Week' => [now()->startOfWeek(), now()->endOfWeek()],
            'This Month' => [now()->startOfMonth(), now()->endOfMonth()],
            'Last 7 Days' => [now()->subDays(7)->startOfDay(), now()->endOfDay()],
            'Last 30 Days' => [now()->subDays(30)->startOfDay(), now()->endOfDay()],
            'Custom Range' => $this->resolveCustomRange($dateFrom, $dateTo),
            default => [now()->startOfDay(), now()->endOfDay()],
        };
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    private function resolveCustomRange(?string $dateFrom, ?string $dateTo): array
    {
        if (! $dateFrom || ! $dateTo) {
            return [now()->startOfDay(), now()->endOfDay()];
        }

        try {
            return [Carbon::parse($dateFrom)->startOfDay(), Carbon::parse($dateTo)->endOfDay()];
        } catch (\Exception) {
            return [now()->startOfDay(), now()->endOfDay()];
        }
    }
}
