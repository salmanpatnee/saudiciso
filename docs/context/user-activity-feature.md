# User Activity Tracking — Implementation Guide

Portable backend implementation guide for a login/logout session tracker plus a
per-session page-visit timeline, extracted from this project's implementation
(`app/Http/{Controllers,Middleware}`, `app/Listeners`, `app/Models`, commits
`7a226ac` and `a7c380f`). UI/blade markup is intentionally omitted — rebuild
the views to match the target project's design system. Everything below is
backend: schema, models, event capture, middleware, scheduled maintenance,
controller/query logic.

This is a reference implementation to **adapt**, not copy verbatim — role and
access-control checks in particular must be re-derived for the target
project (see "Before You Start").

---

## 1. What This Feature Does

- Records a `user_sessions` row every time a *trackable* user logs in, and
  closes it (logout time, duration) when they log out.
- Runs a lightweight heartbeat on every request so a session's
  `last_activity_at` stays fresh without hammering the DB on every hit.
- Optionally records a `session_activities` row for every page (GET request)
  a trackable user visits during a session, building a navigable timeline.
- Exposes an admin-facing dashboard: summary stats (online now, today's
  logins/logouts, active sessions, avg duration), a filterable/sortable
  session table, and a per-session detail page with a generated
  "activity summary" (most-visited page, longest single stay, top-interest
  module).
- Auto-closes abandoned sessions and prunes old page-visit rows on a
  schedule, so the tables don't grow unbounded and "online" status doesn't
  lie forever.

## 2. Before You Start — Questions to Ask

The reference implementation hardcoded two decisions to this app's specific
role system. **Do not port these hardcoded checks as-is.** Ask the following
before writing any code, and bake the answers in as config/gates instead of
inline literals:

1. **Which users should be tracked?** (the reference app tracked only
   `role_id === 4`, i.e. its plain "User" role — not admins/managers). Get
   the exact role(s)/permission(s) or user attribute that defines "trackable"
   for this project. Implement as a small helper (e.g.
   `UserActivityTracking::isTrackable(User $user): bool`) or a config array
   of role IDs, not an inline `!== 4` check scattered across listeners and
   middleware.
2. **Who can view the dashboard?** The reference app gated it behind a
   middleware that checked `auth()->user()->id !== 1` — literally one
   hardcoded user ID, not a role or permission check. Ask which real
   role/permission/gate should protect the `/user-activity*` routes in the
   target project (e.g. `can:view-user-activity`, an `admin` role, etc.) and
   use that instead.
3. **Session lifetime / "online" threshold** — see Known Issue #1 below.
   Ask whether "online" should track the app's session lifetime, or use an
   independent, explicit idle-timeout config value.
4. **Retention window** for page-visit rows (reference default: 90 days) —
   confirm this against the target project's data-retention policy.
5. **Does the target app already have a `users` table with `first_name`/
   `last_name`, or a single `name` column?** Adjust the model relation and
   any display logic accordingly (this only matters where UI reads it, but
   note it now).
6. **DB naming collisions** — confirm `user_sessions` and
   `session_activities` table names are free (Laravel's built-in session
   driver can also use a `sessions` table — this feature is unrelated to
   that and uses different table names, but double check no other package
   already claims these names).

---

## 3. Data Model

### 3.1 `user_sessions` table

One row per login → logout cycle.

```php
Schema::create('user_sessions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('session_token', 36)->unique(); // UUID, bridges PHP session -> DB row
    $table->timestamp('login_at');
    $table->timestamp('logout_at')->nullable();
    $table->timestamp('last_activity_at'); // updated by heartbeat middleware
    $table->unsignedInteger('duration_seconds')->nullable(); // set on logout / stale-close
    $table->string('status', 20)->default('Active'); // Active | Ended
    $table->string('login_method', 50)->default('password');
    $table->string('ip_address', 45)->nullable();
    $table->string('browser', 50)->nullable();
    $table->string('platform', 50)->nullable();
    $table->string('device_type', 20)->nullable();
    $table->text('user_agent')->nullable();
    $table->timestamps();

    $table->index(['user_id', 'status']);
    $table->index(['status', 'last_activity_at']);
    $table->index('login_at');
});
```

### 3.2 `session_activities` table

One row per page visit within a session. Optional sub-feature — skip if you
only need login/logout tracking.

```php
Schema::create('session_activities', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_session_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // denormalized for direct user-scoped queries
    $table->string('type', 30)->default('page_visit');
    $table->string('route_name', 150)->nullable();
    $table->string('url', 255);
    $table->string('method', 10)->default('GET');
    $table->string('label', 150)->nullable();  // human-readable page name
    $table->string('module', 60)->nullable();   // top-level section/module the page belongs to
    $table->json('meta')->nullable();           // free slot for future extra context
    $table->timestamp('occurred_at');
    $table->timestamp('created_at')->useCurrent();
    // no updated_at — rows are immutable

    $table->index(['user_session_id', 'occurred_at']);
    $table->index(['user_id', 'occurred_at']);
    $table->index('type');
});
```

---

## 4. Enums

```php
enum SessionStatus: string
{
    case Active = 'Active';
    case Ended = 'Ended';
}

enum ActivityType: string
{
    case PageVisit = 'page_visit';
    // add more cases here if you track other activity types later
}
```

---

## 5. Models

### 5.1 `UserSession`

```php
class UserSession extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'login_at' => 'datetime',
            'logout_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'status' => SessionStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(SessionActivity::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', SessionStatus::Active);
    }

    public function scopeForDateRange(Builder $query, Carbon $from, Carbon $to): Builder
    {
        return $query->whereBetween('login_at', [$from, $to]);
    }

    public function scopeStale(Builder $query, Carbon $cutoff): Builder
    {
        return $query->active()->where('last_activity_at', '<', $cutoff);
    }

    public function isCurrentlyOnline(): bool
    {
        return $this->status === SessionStatus::Active
            && $this->last_activity_at !== null
            && $this->last_activity_at->gte(self::onlineCutoff());
    }

    public function liveDurationSeconds(): int
    {
        if ($this->status === SessionStatus::Active) {
            return now()->diffInSeconds($this->login_at);
        }

        return $this->duration_seconds ?? 0;
    }

    public function durationForHumans(): string
    {
        return self::formatDuration($this->liveDurationSeconds());
    }

    public static function formatDuration(?int $seconds): string
    {
        $seconds ??= 0;

        if ($seconds < 60) {
            return '<1m';
        }

        $days = intdiv($seconds, 86400);
        $hours = intdiv($seconds % 86400, 3600);
        $minutes = intdiv($seconds % 3600, 60);

        if ($days > 0) {
            return "{$days}d {$hours}h";
        }

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }

        return "{$minutes}m";
    }

    public static function onlineCutoff(): Carbon
    {
        // See Known Issue #1 — reconsider this formula for the new project.
        return now()->subMinutes((int) config('session.lifetime') + 5);
    }
}
```

### 5.2 `SessionActivity`

```php
class SessionActivity extends Model
{
    const UPDATED_AT = null; // immutable log rows, no updated_at column

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'type' => ActivityType::class,
            'meta' => 'array',
            'occurred_at' => 'datetime',
        ];
    }

    public function userSession(): BelongsTo
    {
        return $this->belongsTo(UserSession::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

---

## 6. Event Capture: Login / Logout

Uses Laravel's built-in `Illuminate\Auth\Events\Login` / `Logout` events —
fires regardless of which controller/guard performs the login, no changes
needed to existing auth controllers.

### 6.1 Register listeners

If the target project has event auto-discovery enabled, listeners can just
live in `app/Listeners` with a `handle(Login $event)` method and be
auto-wired. If discovery is disabled (check `EventServiceProvider::
shouldDiscoverEvents()`), register explicitly:

```php
protected $listen = [
    Login::class => [RecordUserLogin::class],
    Logout::class => [RecordUserLogout::class],
];
```

### 6.2 `RecordUserLogin`

```php
class RecordUserLogin
{
    public function handle(Login $event): void
    {
        $user = $event->user;

        if (! UserActivityTracking::isTrackable($user)) { // <- your role/gate check
            return;
        }

        try {
            $request = request();

            // Guards against duplicate rows from a double form submission
            // (e.g. a double-click on the login button firing two POST
            // requests): reuse the just-created row instead of creating a
            // second one for the same login.
            $duplicate = UserSession::where('user_id', $user->id)
                ->where('ip_address', $request->ip())
                ->where('status', SessionStatus::Active)
                ->where('login_at', '>=', now()->subSeconds(5))
                ->latest('id')
                ->first();

            if ($duplicate) {
                session([
                    'user_activity_token' => $duplicate->session_token,
                    'user_activity_session_id' => $duplicate->id,
                ]);

                return;
            }

            $token = (string) Str::uuid();

            session(['user_activity_token' => $token]);

            $userAgent = (string) $request->userAgent();
            $agent = UserAgentParser::parse($userAgent);

            $session = UserSession::create([
                'user_id' => $user->id,
                'session_token' => $token,
                'login_at' => now(),
                'last_activity_at' => now(),
                'status' => SessionStatus::Active,
                'login_method' => 'password',
                'ip_address' => $request->ip(),
                'browser' => $agent['browser'],
                'platform' => $agent['platform'],
                'device_type' => $agent['device_type'],
                'user_agent' => $userAgent,
            ]);

            session(['user_activity_session_id' => $session->id]);
        } catch (Throwable $e) {
            Log::error('Failed to record user login activity.', [
                'user_id' => $user->id,
                'exception' => $e,
            ]);
        }
    }
}
```

**Design notes:**
- Wrapped in try/catch so a tracking failure never breaks the actual login
  flow — this must never become a hard dependency of authentication.
- The PHP session stores `user_activity_token` (bridges to the DB row via
  `session_token`) and `user_activity_session_id` (used directly by
  middleware to avoid a lookup). Both are needed.
- Dedup window of 5 seconds on identical `user_id` + `ip_address` + status
  is a pragmatic fix for double-submit bugs, not a general concurrency
  control — fine to reuse as-is.

### 6.3 `RecordUserLogout`

```php
class RecordUserLogout
{
    public function handle(Logout $event): void
    {
        $user = $event->user;

        if (! $user || ! UserActivityTracking::isTrackable($user)) {
            return;
        }

        try {
            $token = session('user_activity_token');

            if (! $token) {
                return;
            }

            $session = UserSession::where('session_token', $token)
                ->where('status', SessionStatus::Active)
                ->first();

            if (! $session) {
                return;
            }

            $now = now();

            $session->update([
                'logout_at' => $now,
                'duration_seconds' => $session->login_at->diffInSeconds($now),
                'status' => SessionStatus::Ended,
            ]);
        } catch (Throwable $e) {
            Log::error('Failed to record user logout activity.', [
                'user_id' => $user->id,
                'exception' => $e,
            ]);
        }
    }
}
```

---

## 7. Middleware

Both middleware are registered globally on the `web` group (append, so they
run after the framework's own session/auth middleware) and both no-op
immediately for non-trackable users — cheap for everyone else.

### 7.1 `TrackUserActivity` (heartbeat)

Keeps `last_activity_at` fresh without writing on every single request.

```php
class TrackUserActivity
{
    private const PING_INTERVAL_SECONDS = 60;

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $user = auth()->user();

        if (! $user || ! UserActivityTracking::isTrackable($user)) {
            return $response;
        }

        $token = session('user_activity_token');

        if (! $token) {
            return $response;
        }

        $lastPing = (int) session('user_activity_last_ping', 0);

        if (now()->timestamp - $lastPing < self::PING_INTERVAL_SECONDS) {
            return $response;
        }

        try {
            UserSession::where('session_token', $token)
                ->where('status', SessionStatus::Active)
                ->update(['last_activity_at' => now()]);

            session(['user_activity_last_ping' => now()->timestamp]);
        } catch (Throwable $e) {
            Log::error('Failed to record user activity heartbeat.', [
                'user_id' => $user->id,
                'exception' => $e,
            ]);
        }

        return $response;
    }
}
```

**Design notes:**
- The 60-second throttle is stored in the PHP session itself (not the DB),
  so it costs nothing extra to check.
- Runs `$next($request)` first, so it only pings on responses that actually
  completed the request lifecycle.

### 7.2 `TrackPageVisit` (page-visit timeline — optional)

```php
class TrackPageVisit
{
    private const DEDUPE_WINDOW_SECONDS = 3;

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $user = auth()->user();

        if (! $user || ! UserActivityTracking::isTrackable($user)) {
            return $response;
        }

        $userSessionId = session('user_activity_session_id');

        if (! $userSessionId) {
            return $response;
        }

        if (! $request->isMethod('get')
            || $response->getStatusCode() !== 200
            || $request->ajax()
            || $request->wantsJson()) {
            return $response;
        }

        $routeName = $request->route()?->getName();

        if (! $routeName) {
            return $response;
        }

        $lastRoute = session('page_visit_last_route');
        $lastRouteAt = (int) session('page_visit_last_route_at', 0);

        if ($lastRoute === $routeName && now()->timestamp - $lastRouteAt < self::DEDUPE_WINDOW_SECONDS) {
            return $response;
        }

        try {
            SessionActivity::create([
                'user_session_id' => $userSessionId,
                'user_id' => $user->id,
                'route_name' => $routeName,
                'url' => $request->path(),
                'method' => $request->method(),
                'label' => PageVisitLabeler::label($routeName),
                'module' => PageVisitLabeler::module($routeName),
                'occurred_at' => now(),
            ]);

            session([
                'page_visit_last_route' => $routeName,
                'page_visit_last_route_at' => now()->timestamp,
            ]);
        } catch (Throwable $e) {
            Log::error('Failed to record page visit activity.', [
                'user_id' => $user->id,
                'exception' => $e,
            ]);
        }

        return $response;
    }
}
```

**Why filter GET + status 200 + not ajax/json:** only logs successful,
full-page navigations — not form POSTs, API/XHR polling, or failed
requests, which would otherwise pollute the timeline with noise. Adjust the
filter if the target project is more SPA-like (e.g. Inertia/Livewire apps
may need to special-case their own navigation requests, since those often
go through XHR).

### 7.3 Registration (Laravel 11 `bootstrap/app.php` style)

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\TrackUserActivity::class,
        \App\Http\Middleware\TrackPageVisit::class,
    ]);
})
```

On Laravel 10-structure apps (`Http/Kernel.php`), append both to the `web`
middleware group array instead.

---

## 8. Support / Helper Classes

### 8.1 `UserAgentParser`

Simple string-matching parser — no external package dependency. Order
matters (e.g. check `Edg/` before `Chrome/`, since Edge's UA also contains
"Chrome").

```php
final class UserAgentParser
{
    public static function parse(string $userAgent): array
    {
        return [
            'browser' => self::browser($userAgent),
            'platform' => self::platform($userAgent),
            'device_type' => self::deviceType($userAgent),
        ];
    }

    public static function browser(string $userAgent): string
    {
        return match (true) {
            str_contains($userAgent, 'Edg/') => 'Edge',
            str_contains($userAgent, 'Chrome/') => 'Chrome',
            str_contains($userAgent, 'Firefox/') => 'Firefox',
            str_contains($userAgent, 'Safari/') => 'Safari',
            default => 'Unknown',
        };
    }

    public static function platform(string $userAgent): string
    {
        return match (true) {
            str_contains($userAgent, 'Windows') => 'Windows',
            str_contains($userAgent, 'iPhone'), str_contains($userAgent, 'iPad') => 'iOS',
            str_contains($userAgent, 'Mac OS X') => 'macOS',
            str_contains($userAgent, 'Android') => 'Android',
            str_contains($userAgent, 'Linux') => 'Linux',
            default => 'Unknown',
        };
    }

    public static function deviceType(string $userAgent): string
    {
        return match (true) {
            str_contains($userAgent, 'iPad'), str_contains($userAgent, 'Tablet') => 'Tablet',
            str_contains($userAgent, 'Mobile'), str_contains($userAgent, 'Android') => 'Mobile',
            default => 'Desktop',
        };
    }
}
```

If the target project already has a UA-parsing package, prefer that instead
— this is a minimal reference implementation, not exhaustive.

### 8.2 `PageVisitLabeler`

Turns a route name into a human-readable label + module, driven off route
*naming convention* (`module.resource.action`), with an override list for
cases where the derived label reads awkwardly.

```php
final class PageVisitLabeler
{
    /**
     * Route names whose auto-derived label would read awkwardly.
     */
    private const LABEL_OVERRIDES = [
        // 'route.name.here' => 'Custom Label',
    ];

    public static function label(string $routeName): string
    {
        if (array_key_exists($routeName, self::LABEL_OVERRIDES)) {
            return self::LABEL_OVERRIDES[$routeName];
        }

        $segments = explode('.', $routeName);
        $last = end($segments);

        if (in_array($last, ['index', 'show', 'create', 'edit'], true)) {
            return self::module($routeName);
        }

        return Str::headline($last);
    }

    public static function module(string $routeName): string
    {
        $segments = explode('.', $routeName);

        return Str::headline($segments[0]);
    }
}
```

**Requires the target project's routes to be named consistently**
(`module.action` or `module.resource.action`, e.g. `reports.index`,
`billing.invoices.show`). If route naming is inconsistent, this needs
reworking — possibly a static lookup table keyed by route name instead of
convention-derived.

### 8.3 Trackability check (new — reference didn't have this abstraction)

Create this instead of inlining `role_id === 4` everywhere:

```php
final class UserActivityTracking
{
    public static function isTrackable(?Authenticatable $user): bool
    {
        if (! $user) {
            return false;
        }

        // Adapt to the target project's role/permission system, e.g.:
        // return $user->hasRole('User');
        // return in_array($user->role_id, config('user-activity.trackable_role_ids'));
        return in_array($user->role_id, config('user-activity.trackable_role_ids', []), true);
    }
}
```

---

## 9. Scheduled Maintenance Commands

### 9.1 `sessions:close-stale`

Force-closes sessions that have gone quiet past the online cutoff, so
"Active" status doesn't lie forever for users who closed the tab without
logging out.

```php
class CloseStaleUserSessions extends Command
{
    protected $signature = 'sessions:close-stale';
    protected $description = 'Close user_sessions rows inactive past the session lifetime.';

    public function handle(): int
    {
        $closed = UserSession::query()
            ->stale(UserSession::onlineCutoff())
            ->update([
                'status' => SessionStatus::Ended->value,
                'logout_at' => DB::raw('last_activity_at'),
                'duration_seconds' => DB::raw('TIMESTAMPDIFF(SECOND, login_at, last_activity_at)'),
            ]);

        $this->info("Closed {$closed} stale user session(s).");

        return self::SUCCESS;
    }
}
```

`TIMESTAMPDIFF` is MySQL-specific — swap for the equivalent on
Postgres/SQLite if the target project uses a different driver (e.g.
Postgres: `EXTRACT(EPOCH FROM (last_activity_at - login_at))`).

### 9.2 `sessions:prune-activities`

```php
class PruneSessionActivities extends Command
{
    private const RETENTION_DAYS = 90; // confirm against target project policy

    protected $signature = 'sessions:prune-activities';
    protected $description = 'Delete session_activities rows older than the retention window.';

    public function handle(): int
    {
        $deleted = SessionActivity::where('occurred_at', '<', now()->subDays(self::RETENTION_DAYS))->delete();

        $this->info("Pruned {$deleted} session activity record(s).");

        return self::SUCCESS;
    }
}
```

### 9.3 Scheduler registration

```php
$schedule->command('sessions:close-stale')->everyFiveMinutes()->withoutOverlapping();
$schedule->command('sessions:prune-activities')->daily()->withoutOverlapping();
```

---

## 10. Controller & Routes

### 10.1 Routes

```php
Route::middleware(['auth', /* your dashboard-access gate here */])->group(function () {
    Route::get('/user-activity', [UserActivityController::class, 'index'])->name('user-activity.index');
    Route::get('/user-activity/{session}', [UserActivityController::class, 'show'])->name('user-activity.show');
});
```

`{session}` route-model-binds directly to `UserSession` (implicit binding on
the `id` primary key — no custom key needed).

### 10.2 `index()` — dashboard

Query/filter logic (framework-agnostic on the UI side — reimplement the
filter *inputs* however the target project's UI conventions look, but the
underlying query shape below is what to reproduce):

```php
public function index(Request $request): View
{
    $search = $request->input('search');
    $period = $request->input('period', 'Today');           // Today | This Week | This Month | Custom Range
    $dateFrom = $request->input('date_from');
    $dateTo = $request->input('date_to');
    $presence = $request->input('presence', 'All');          // All | Online | Offline
    $sortBy = $request->input('sort_by', 'Newest Login');
    [$sortColumn, $sortDirection] = $this->resolveSort($sortBy);

    [$rangeStart, $rangeEnd] = $this->resolveDateRange($period, $dateFrom, $dateTo);
    $onlineCutoff = UserSession::onlineCutoff();

    $todayStart = now()->startOfDay();
    $todayEnd = now()->endOfDay();

    $summary = [
        'online_now' => UserSession::query()->active()
            ->where('last_activity_at', '>=', $onlineCutoff)
            ->distinct('user_id')
            ->count('user_id'),
        'todays_logins' => UserSession::query()->forDateRange($todayStart, $todayEnd)->count(),
        'todays_logouts' => UserSession::query()->whereBetween('logout_at', [$todayStart, $todayEnd])->count(),
        'active_sessions' => UserSession::query()->active()->count(),
        'avg_duration_today' => UserSession::formatDuration((int) UserSession::query()
            ->forDateRange($todayStart, $todayEnd)
            ->whereNotNull('duration_seconds')
            ->avg('duration_seconds')),
    ];

    $sessions = UserSession::query()
        ->with('user:id,first_name,last_name,username') // adjust columns to target schema
        ->when($search, fn ($query, $value) => $query->whereHas('user', function ($userQuery) use ($value) {
            $userQuery->where('first_name', 'like', "%{$value}%")
                ->orWhere('last_name', 'like', "%{$value}%")
                ->orWhere('username', 'like', "%{$value}%");
        }))
        ->forDateRange($rangeStart, $rangeEnd)
        ->when($presence === 'Online', fn ($query) => $query->active()
            ->where('last_activity_at', '>=', $onlineCutoff))
        ->when($presence === 'Offline', fn ($query) => $query->where(function ($offlineQuery) use ($onlineCutoff) {
            $offlineQuery->where('status', SessionStatus::Ended)
                ->orWhere('last_activity_at', '<', $onlineCutoff);
        }))
        ->orderBy($sortColumn, $sortDirection)
        ->paginate(25)
        ->withQueryString();

    // Per-user total login count in one grouped query, keyed for O(1) lookup
    // in the view — avoids an N+1 count() call per row.
    $totalLoginsByUser = UserSession::query()
        ->selectRaw('user_id, count(*) as total_logins')
        ->whereIn('user_id', $sessions->pluck('user_id')->unique())
        ->groupBy('user_id')
        ->pluck('total_logins', 'user_id');

    return view('...', compact('sessions', 'summary', 'totalLoginsByUser', 'search', 'period', 'dateFrom', 'dateTo', 'presence', 'sortBy'));
}

private function resolveSort(string $sortBy): array
{
    return match ($sortBy) {
        'Oldest Login' => ['login_at', 'asc'],
        'Newest Logout' => ['logout_at', 'desc'],
        'Longest Duration' => ['duration_seconds', 'desc'],
        'Shortest Duration' => ['duration_seconds', 'asc'],
        default => ['login_at', 'desc'],
    };
}

private function resolveDateRange(string $period, ?string $dateFrom, ?string $dateTo): array
{
    return match ($period) {
        'This Week' => [now()->startOfWeek(), now()->endOfWeek()],
        'This Month' => [now()->startOfMonth(), now()->endOfMonth()],
        'Custom Range' => $this->resolveCustomRange($dateFrom, $dateTo),
        default => [now()->startOfDay(), now()->endOfDay()],
    };
}

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
```

### 10.3 `show()` — session detail + activity summary

```php
public function show(UserSession $session): View
{
    $session->load('user:id,first_name,last_name,username');

    $activities = $session->activities()
        ->orderBy('occurred_at')
        ->paginate(25)
        ->withQueryString();

    return view('...', [
        'session' => $session,
        'activities' => $activities,
        'summary' => $this->buildActivitySummary($session),
    ]);
}

/**
 * @return array{top_page_label: string, top_page_visits: int, longest_stay_label: string, longest_stay_duration: string, top_module: string}|null
 */
private function buildActivitySummary(UserSession $session): ?array
{
    // Full unpaginated set — this is a separate, cheap query independent
    // of the paginated $activities used for display above.
    $rows = $session->activities()->orderBy('occurred_at')->get(['label', 'module', 'occurred_at']);

    if ($rows->count() < 2) {
        return null; // not enough data to summarize
    }

    $boundary = $session->logout_at ?? now();

    // "Time spent on a page" = gap until the *next* recorded visit (or the
    // session boundary for the last page visited).
    $withDurations = $rows->map(function ($row, $i) use ($rows, $boundary) {
        $until = $rows->get($i + 1)?->occurred_at ?? $boundary;

        return [
            'label' => $row->label,
            'module' => $row->module,
            'seconds' => $row->occurred_at->diffInSeconds($until),
        ];
    });

    $topPage = $rows->countBy('label')->sortDesc();
    $longestStay = $withDurations->sortByDesc('seconds')->first();
    $topModule = $withDurations->groupBy('module')
        ->map(fn ($group) => $group->sum('seconds'))
        ->sortDesc();

    return [
        'top_page_label' => $topPage->keys()->first(),
        'top_page_visits' => $topPage->first(),
        'longest_stay_label' => $longestStay['label'],
        'longest_stay_duration' => UserSession::formatDuration($longestStay['seconds']),
        'top_module' => $topModule->keys()->first(),
    ];
}
```

---

## 11. Wiring Checklist

1. Migrations: `user_sessions`, `session_activities`.
2. Enums: `SessionStatus`, `ActivityType`.
3. Models: `UserSession`, `SessionActivity` (+ relation on your `User`
   model isn't required — `UserSession`/`SessionActivity` `belongsTo` is
   enough; add a `hasMany` back on `User` only if you need
   `$user->sessions`).
4. Support classes: `UserAgentParser`, `PageVisitLabeler`,
   `UserActivityTracking` (the trackability gate — new addition, see §8.3).
5. Listeners: `RecordUserLogin`, `RecordUserLogout`; register on `Login`/
   `Logout` events (check whether the target project auto-discovers events).
6. Middleware: `TrackUserActivity`, `TrackPageVisit`; append both to the
   `web` middleware group.
7. Console commands: `CloseStaleUserSessions`, `PruneSessionActivities`;
   register in the scheduler.
8. Controller: `UserActivityController` with `index`/`show`.
9. Routes: two GET routes behind the real dashboard-access gate (§2, item 2).
10. Config: `config/user-activity.php` with `trackable_role_ids` (or
    equivalent) and any tunables you pull out per Known Issues below.
11. Views: rebuild for target project's UI (out of scope here).

---

## 12. Known Issues in the Reference Implementation (fix, don't copy)

1. **"Online" cutoff is way too loose.** `onlineCutoff()` computes
   `now() - (session.lifetime + 5) minutes`. With this app's default
   `SESSION_LIFETIME=120`, that's a **125-minute** window — a session shown
   as "Online" could have been idle for over two hours. This piggybacks the
   PHP session's expiry (a security/logout concern) onto a UI presence
   concept, which is the wrong coupling. **Recommendation:** introduce a
   dedicated, small config value (e.g. `user-activity.online_threshold_minutes`,
   default 5) independent of `session.lifetime`, and use that for
   `onlineCutoff()`.
2. **Dashboard access gate is a single hardcoded user ID**, not a role or
   permission (`auth()->user()?->id !== 1`). This silently breaks the moment
   a second admin is added, or that user ID changes. **Recommendation:**
   use a real role/permission gate from day one in the new project (§2,
   item 2) — don't reintroduce this pattern.
3. **`TIMESTAMPDIFF` in `CloseStaleUserSessions` is MySQL-only.** If the
   target project uses Postgres/SQLite, swap for the portable equivalent or
   compute the diff in PHP and pass a literal value instead of `DB::raw`.
4. **No index on `session_activities.route_name`/`label`/`module`**
   individually — fine at the reference app's scale, but if the new project
   expects heavy querying/filtering by module or label (not just by session
   or user), add indexes for those columns.
5. **Heartbeat and page-visit dedupe state live in the PHP session**, not
   the DB. This is intentional (cheap, no query needed to check the
   throttle) but means the throttle resets if the session driver changes or
   the session is invalidated — acceptable trade-off, just flagging it's a
   deliberate design choice to preserve, not an oversight.

---

## 13. Suggested Implementation Order

1. Config (`config/user-activity.php`) + `UserActivityTracking` gate class
   — decide trackability rules first, everything else depends on it.
2. Migrations + Enums + Models.
3. Listeners + event registration — verify login/logout create/close rows
   correctly before adding middleware.
4. `TrackUserActivity` heartbeat middleware — verify `last_activity_at`
   updates on a throttle.
5. `sessions:close-stale` command — verify stale sessions get force-closed.
6. Controller `index()` + routes + minimal view — get the dashboard
   rendering with real data before adding the page-visit layer.
7. (Optional) `TrackPageVisit` middleware + `SessionActivity` +
   `PageVisitLabeler` + `sessions:prune-activities`.
8. Controller `show()` + activity summary + detail view.
