<?php

namespace App\Support;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * ip-api.com resolver.
 *
 * The free tier is plaintext HTTP and allows 45 lookups per minute; exceeding
 * it returns HTTP 429 and bans the calling server for a minute. Four layers
 * keep that from happening: a long lived per-IP cache, negative caching of
 * failures, a global per-minute ceiling, and honouring the provider's own
 * X-Rl / X-Ttl headers.
 */
final class IpApiResolver implements GeoLocationResolver
{
    private const MISS = '_miss';

    public function resolve(string $ip): ?GeoLocation
    {
        if (! $this->isResolvable($ip)) {
            return null;
        }

        $store = $this->store();
        $key = config('activity-log.geo.cache_prefix').sha1($ip);

        $cached = $store->get($key);

        if (is_array($cached)) {
            return isset($cached[self::MISS]) ? null : GeoLocation::fromArray($cached);
        }

        if (! $this->underRateCeiling()) {
            return null;
        }

        try {
            $response = Http::acceptJson()
                ->connectTimeout((int) config('activity-log.geo.connect_timeout', 1))
                ->timeout((int) config('activity-log.geo.timeout', 2))
                ->get(rtrim((string) config('activity-log.geo.endpoint'), '/').'/'.$ip, [
                    'fields' => config('activity-log.geo.fields'),
                    'lang' => 'en',
                ]);

            $this->honourProviderHeaders($response);

            $data = $response->successful() ? $response->json() : null;

            if (! is_array($data) || ($data['status'] ?? null) !== 'success') {
                $this->rememberMiss($key);

                return null;
            }

            $location = new GeoLocation(
                countryCode: $data['countryCode'] ?? null,
                country: $data['country'] ?? null,
                region: $data['regionName'] ?? null,
                city: $data['city'] ?? null,
                timezone: $data['timezone'] ?? null,
                isp: $data['isp'] ?? null,
                latitude: isset($data['lat']) ? (float) $data['lat'] : null,
                longitude: isset($data['lon']) ? (float) $data['lon'] : null,
            );

            $store->put(
                $key,
                $location->toArray(),
                now()->addDays((int) config('activity-log.geo.ttl_days', 30))
            );

            return $location;
        } catch (Throwable $e) {
            /**
             * Negative caching matters more than the lookup itself: without it a
             * down or rate limiting provider adds a full timeout to every
             * single request.
             */
            $this->rememberMiss($key);

            Log::channel(config('activity-log.log_channel'))
                ->warning('IP geolocation lookup failed.', ['ip' => $ip, 'exception' => $e]);

            return null;
        }
    }

    /**
     * Rejects loopback, private and reserved ranges before any cache or network
     * work. Loopback is checked first because every local request hits it, so
     * development overhead stays at zero.
     */
    private function isResolvable(string $ip): bool
    {
        if ($ip === '' || $ip === '127.0.0.1' || $ip === '::1') {
            return false;
        }

        if (in_array($ip, (array) config('activity-log.geo.skip_ips', []), true)) {
            return false;
        }

        return (bool) filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }

    /**
     * The ceiling is deliberately below the provider's own limit: the file
     * cache store's increment() is a non-atomic read-modify-write across
     * concurrent PHP processes, and the headroom absorbs that race.
     */
    private function underRateCeiling(): bool
    {
        $store = $this->store();
        $key = $this->bucketKey();
        $ceiling = (int) config('activity-log.geo.max_per_minute', 40);

        $store->add($key, 0, now()->addMinutes(2));

        return (int) $store->increment($key) <= $ceiling;
    }

    /**
     * ip-api reports remaining calls in X-Rl and seconds until reset in X-Ttl.
     * Saturating the bucket on X-Rl: 0 backs us off before a ban rather than
     * after one.
     */
    private function honourProviderHeaders(Response $response): void
    {
        if ($response->header('X-Rl') !== '0') {
            return;
        }

        $ttl = (int) $response->header('X-Ttl');

        $this->store()->put(
            $this->bucketKey(),
            (int) config('activity-log.geo.max_per_minute', 40) + 1,
            now()->addSeconds(max($ttl, 60))
        );
    }

    private function rememberMiss(string $key): void
    {
        $this->store()->put(
            $key,
            [self::MISS => true],
            now()->addMinutes((int) config('activity-log.geo.failure_ttl_minutes', 60))
        );
    }

    private function bucketKey(): string
    {
        return 'activity-log:geo:rl:'.now()->format('YmdHi');
    }

    private function store(): Repository
    {
        return Cache::store(config('activity-log.geo.cache_store'));
    }
}
