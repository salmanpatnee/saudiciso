<?php

namespace App\Support;

/**
 * Used when geolocation is disabled, and the correct binding for local
 * development where every request comes from a private IP anyway.
 */
final class NullGeoResolver implements GeoLocationResolver
{
    public function resolve(string $ip): ?GeoLocation
    {
        return null;
    }
}
