<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Master Switch
    |--------------------------------------------------------------------------
    |
    | When disabled, the LogActivity middleware short circuits immediately and
    | no rows are written. The admin module remains readable.
    |
    */

    'enabled' => env('ACTIVITY_LOG_ENABLED', true),

    /**
     * Dedicated log channel for the activity logger's own failures, so a
     * misbehaving audit logger cannot drown storage/logs/laravel.log.
     */
    'log_channel' => env('ACTIVITY_LOG_CHANNEL', 'activity'),

    'connection' => env('ACTIVITY_LOG_CONNECTION'),

    'table' => 'activity_logs',

    /*
    |--------------------------------------------------------------------------
    | Capture Scope
    |--------------------------------------------------------------------------
    |
    | The three levers that control table growth, in order of impact, are the
    | exclusion list below, retention.days, and capture.payload.
    |
    */

    'capture' => [
        'guests' => env('ACTIVITY_LOG_GUESTS', true),
        'ajax' => env('ACTIVITY_LOG_AJAX', true),
        'query_params' => true,
        'payload' => env('ACTIVITY_LOG_PAYLOAD', true),
        'payload_methods' => ['POST', 'PUT', 'PATCH', 'DELETE'],
        'user_agent' => true,
        'referer' => true,
        'exceptions' => env('ACTIVITY_LOG_EXCEPTIONS', true),

        /**
         * Traces are large and routinely contain argument values, i.e. secrets.
         * Off by default on purpose.
         */
        'exception_trace' => env('ACTIVITY_LOG_EXCEPTION_TRACE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Exclusions
    |--------------------------------------------------------------------------
    */

    'exclude' => [
        'paths' => [
            '_debugbar',
            '_debugbar/*',
            'up',
            'clear',
            'favicon.ico',
            'robots.txt',
            'build/*',
            'css/*',
            'fonts/*',
            'Images/*',
            'tailadmin/*',
            'C_/*',
            'storage/*',
            'telescope*',
            'horizon*',
        ],

        'route_names' => ['ajax.*'],

        'extensions' => [
            'css', 'js', 'map', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico',
            'woff', 'woff2', 'ttf', 'eot',
        ],

        'methods' => ['OPTIONS', 'HEAD'],

        'ips' => [],

        'status_codes' => [],

        'user_agent_patterns' => ['/bot|crawler|spider|slurp|bingpreview/i'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Duplicate Request Throttle
    |--------------------------------------------------------------------------
    |
    | Suppresses the double rows produced by rapid re-navigation to the same
    | page. Set duplicate_seconds to 0 to disable and remove the cache read.
    |
    */

    'throttle' => [
        'duplicate_seconds' => env('ACTIVITY_LOG_DEDUPE_SECONDS', 2),
        'duplicate_methods' => ['GET'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Anonymous Visitor Identity
    |--------------------------------------------------------------------------
    |
    | The visitor cookie is the primary timeline key. Session IDs change on
    | login (regenerate) and logout (invalidate), so only this cookie threads a
    | journey from first anonymous view through login to logout.
    |
    */

    'visitor' => [
        'cookie' => env('ACTIVITY_LOG_VISITOR_COOKIE', 'saudiciso_visitor'),
        'lifetime_minutes' => env('ACTIVITY_LOG_VISITOR_LIFETIME', 1051200),
        'same_site' => 'lax',
        'http_only' => true,
        'backfill_on_login' => env('ACTIVITY_LOG_BACKFILL', true),

        /**
         * Deliberately short. On a shared machine a wide window would
         * mis-attribute another person's anonymous browsing to whoever logs in
         * next.
         */
        'backfill_hours' => env('ACTIVITY_LOG_BACKFILL_HOURS', 24),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payload Masking
    |--------------------------------------------------------------------------
    |
    | A key is redacted when it matches "keys" exactly (lowercased) or contains
    | any fragment in "contains". Matching applies at every nesting depth.
    |
    */

    'masking' => [
        'keys' => [
            'password', 'password_confirmation', 'current_password', 'new_password', 'old_password',
            'secret', 'token', '_token', 'access_token', 'refresh_token', 'api_key', 'api_token',
            'authorization', 'credit_card', 'card_number', 'cvv', 'cvc', 'pin', 'ssn', 'national_id',
            'iqama', 'otp', 'remember_token', 'private_key', 'client_secret', 'signature', 'security_answer',
        ],

        'contains' => [
            'password', 'secret', 'token', 'api_key', 'authorization',
            'credit', 'card', 'cvv', 'otp', 'private_key',
        ],

        /** Dotted paths for cases where the key name alone is not enough. */
        'paths' => [],

        'drop_keys' => ['_token', '_method'],

        'replacement' => '[REDACTED]',

        'max_depth' => 6,
        'max_items' => 100,
        'max_value_length' => 500,
        'max_bytes' => 8192,
    ],

    /*
    |--------------------------------------------------------------------------
    | IP Geolocation
    |--------------------------------------------------------------------------
    |
    | The free ip-api.com tier is plaintext HTTP and allows 45 requests per
    | minute. The 30 day per-IP cache drives the hit rate to ~99% after warm
    | up; failure_ttl_minutes prevents a down provider from adding a timeout to
    | every single request.
    |
    | mode: "inline" resolves during terminate(); "deferred" writes null geo and
    | lets the activity-log:resolve-geo command backfill. Deferred requires a
    | working scheduler.
    |
    */

    'geo' => [
        'enabled' => env('ACTIVITY_LOG_GEO', true),
        'driver' => env('ACTIVITY_LOG_GEO_DRIVER', 'ip-api'),
        'mode' => env('ACTIVITY_LOG_GEO_MODE', 'inline'),
        'endpoint' => env('ACTIVITY_LOG_GEO_ENDPOINT', 'http://ip-api.com/json'),
        'fields' => 'status,message,country,countryCode,regionName,city,timezone,isp,lat,lon,query',
        'connect_timeout' => env('ACTIVITY_LOG_GEO_CONNECT_TIMEOUT', 1),
        'timeout' => env('ACTIVITY_LOG_GEO_TIMEOUT', 2),
        'cache_store' => env('ACTIVITY_LOG_GEO_CACHE_STORE', 'file'),
        'cache_prefix' => 'activity-log:geo:',
        'ttl_days' => env('ACTIVITY_LOG_GEO_TTL_DAYS', 30),
        'failure_ttl_minutes' => env('ACTIVITY_LOG_GEO_FAILURE_TTL', 60),

        /**
         * Ceiling is 40 rather than the provider's 45 because the file cache
         * store's increment() is a non-atomic read-modify-write across
         * concurrent PHP processes. The headroom absorbs the race.
         */
        'max_per_minute' => env('ACTIVITY_LOG_GEO_RATE', 40),

        'skip_ips' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Retention
    |--------------------------------------------------------------------------
    |
    | Pruning is chunked. An unbounded DELETE on a table with millions of rows
    | means one enormous transaction, a bloated redo log, and long lock holds.
    |
    */

    'retention' => [
        'days' => env('ACTIVITY_LOG_RETENTION_DAYS', 90),
        'chunk' => env('ACTIVITY_LOG_RETENTION_CHUNK', 5000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Dashboard Access
    |--------------------------------------------------------------------------
    |
    | A user may view the activity log when their ID is listed in user_ids OR
    | their role_id is listed in role_ids.
    |
    */

    'access' => [
        'user_ids' => array_values(array_filter(array_map(
            'intval', explode(',', (string) env('ACTIVITY_LOG_ACCESS_USER_IDS', '1'))
        ))),

        'role_ids' => array_values(array_filter(array_map(
            'intval', explode(',', (string) env('ACTIVITY_LOG_ACCESS_ROLE_IDS', '1'))
        ))),
    ],

    /*
    |--------------------------------------------------------------------------
    | Dashboard UI
    |--------------------------------------------------------------------------
    */

    'ui' => [
        'per_page' => 25,
        'timeline_max_events' => 500,
        'default_period' => 'Today',
    ],

];
