<?php

namespace App\Support;

use Illuminate\Support\Str;

final class PageVisitLabeler
{
    /**
     * Route names whose auto-derived label would read awkwardly
     * (e.g. the last segment is a generic "index"/"show").
     *
     * @var array<string, string>
     */
    private const LABEL_OVERRIDES = [
        'process.user-activity.index' => 'User Activity',
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
