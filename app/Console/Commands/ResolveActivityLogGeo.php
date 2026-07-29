<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Support\GeoLocationResolver;
use Illuminate\Console\Command;

class ResolveActivityLogGeo extends Command
{
    protected $signature = 'activity-log:resolve-geo {--limit=40 : Maximum distinct IP addresses to resolve this run}';

    protected $description = 'Backfill approximate location data for activity log rows whose IP has not been resolved yet.';

    /**
     * Resolution goes through the bound resolver, so it inherits the per-IP
     * cache, negative caching and rate ceiling for free.
     *
     * The default limit sits under the free provider tier's per-minute
     * allowance, which at a five minute schedule leaves comfortable headroom.
     */
    public function handle(GeoLocationResolver $resolver): int
    {
        $limit = (int) $this->option('limit');

        $ips = ActivityLog::query()
            ->whereNull('geo_resolved_at')
            ->whereNotNull('ip_address')
            ->distinct()
            ->limit($limit)
            ->pluck('ip_address');

        if ($ips->isEmpty()) {
            $this->info('No unresolved IP addresses found.');

            return self::SUCCESS;
        }

        $resolved = 0;

        foreach ($ips as $ip) {
            $location = $resolver->resolve($ip);

            if ($location === null) {
                continue;
            }

            ActivityLog::query()
                ->where('ip_address', $ip)
                ->whereNull('geo_resolved_at')
                ->update(array_merge($location->toColumns(), ['geo_resolved_at' => now()]));

            $resolved++;
        }

        $this->info("Resolved {$resolved} of {$ips->count()} distinct IP address(es).");

        return self::SUCCESS;
    }
}
