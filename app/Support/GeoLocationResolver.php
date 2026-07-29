<?php

namespace App\Support;

/**
 * Swapping geolocation providers means adding one implementation of this
 * interface plus one arm in the match in AppServiceProvider::register().
 */
interface GeoLocationResolver
{
    /**
     * Returns null when the IP is private, the lookup fails, or the provider is
     * rate limited. Callers must treat null as "unknown", never as an error
     * worth losing the audit row over.
     */
    public function resolve(string $ip): ?GeoLocation;
}
