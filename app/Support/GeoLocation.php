<?php

namespace App\Support;

/**
 * Approximate, IP derived location. Never precise coordinates.
 */
final class GeoLocation
{
    public function __construct(
        public readonly ?string $countryCode = null,
        public readonly ?string $country = null,
        public readonly ?string $region = null,
        public readonly ?string $city = null,
        public readonly ?string $timezone = null,
        public readonly ?string $isp = null,
        public readonly ?float $latitude = null,
        public readonly ?float $longitude = null,
    ) {}

    /**
     * Maps onto the activity_logs geo columns.
     *
     * @return array<string, mixed>
     */
    public function toColumns(): array
    {
        return [
            'country_code' => $this->countryCode,
            'country' => $this->country,
            'region' => $this->region,
            'city' => $this->city,
            'geo_timezone' => $this->timezone,
            'isp' => $this->isp,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'countryCode' => $this->countryCode,
            'country' => $this->country,
            'region' => $this->region,
            'city' => $this->city,
            'timezone' => $this->timezone,
            'isp' => $this->isp,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            countryCode: $data['countryCode'] ?? null,
            country: $data['country'] ?? null,
            region: $data['region'] ?? null,
            city: $data['city'] ?? null,
            timezone: $data['timezone'] ?? null,
            isp: $data['isp'] ?? null,
            latitude: isset($data['latitude']) ? (float) $data['latitude'] : null,
            longitude: isset($data['longitude']) ? (float) $data['longitude'] : null,
        );
    }
}
