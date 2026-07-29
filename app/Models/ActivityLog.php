<?php

namespace App\Models;

use App\Enums\ActivityLogType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Read and delete surface for the activity_logs table.
 *
 * Writes go through DB::table()->insert() in App\Http\Middleware\LogActivity to
 * skip model events, attribute casting and fillable traversal on the request
 * hot path.
 */
class ActivityLog extends Model
{
    /** Rows are immutable once written. */
    const UPDATED_AT = null;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'created_at' => 'datetime',
            'linked_at' => 'datetime',
            'geo_resolved_at' => 'datetime',
            'query_params' => 'array',
            'payload' => 'array',
            'meta' => 'array',
            'activity_type' => ActivityLogType::class,
            'is_authenticated' => 'boolean',
            'is_ajax' => 'boolean',
        ];
    }

    /**
     * No foreign key backs this relation, so the user may no longer exist.
     * Callers must null guard; the denormalised user_name and user_email
     * columns are the reliable display source.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForDateRange(Builder $query, Carbon $from, Carbon $to): Builder
    {
        return $query->whereBetween('occurred_at', [$from, $to]);
    }

    public function scopeForVisitor(Builder $query, string $visitorId): Builder
    {
        return $query->where('visitor_id', $visitorId);
    }

    public function scopeForSession(Builder $query, string $sessionId): Builder
    {
        return $query->where('session_id', $sessionId);
    }

    public function scopeOfType(Builder $query, ActivityLogType $type): Builder
    {
        return $query->where('activity_type', $type->value);
    }

    public function scopeGuests(Builder $query): Builder
    {
        return $query->whereNull('user_id');
    }

    public function displayActor(): string
    {
        if ($this->user_name) {
            return $this->user_name;
        }

        if ($this->visitor_id) {
            return 'Guest '.Str::substr($this->visitor_id, 0, 8);
        }

        return 'Unknown';
    }

    public function displayLocation(): string
    {
        $parts = array_filter([$this->city, $this->region, $this->country]);

        return $parts === [] ? '—' : implode(', ', $parts);
    }

    public function displayDuration(): string
    {
        if ($this->duration_ms === null) {
            return '—';
        }

        if ($this->duration_ms < 1000) {
            return $this->duration_ms.' ms';
        }

        return number_format($this->duration_ms / 1000, 2).' s';
    }

    /**
     * True when this row was captured anonymously and later attributed to a
     * user by the login backfill, rather than being authenticated at the time.
     */
    public function wasLinkedAfterTheFact(): bool
    {
        return $this->linked_at !== null && ! $this->is_authenticated;
    }
}
