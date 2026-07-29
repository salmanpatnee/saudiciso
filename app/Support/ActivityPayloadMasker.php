<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Redacts sensitive values out of request data before it is persisted.
 *
 * A key is redacted when it matches the configured key list exactly
 * (lowercased) or contains any configured fragment. Matching applies at every
 * nesting depth, so a "password" six levels down is still caught.
 */
final class ActivityPayloadMasker
{
    /**
     * @param  array<array-key, mixed>  $input
     * @return array<array-key, mixed>
     */
    public static function mask(array $input): array
    {
        $masked = self::walk($input, 0);

        foreach ((array) config('activity-log.masking.paths', []) as $path) {
            if (Arr::has($masked, $path)) {
                Arr::set($masked, $path, self::replacement());
            }
        }

        return $masked;
    }

    /**
     * JSON encodes a masked array with the flags MySQL's JSON column type
     * requires, applying the configured size cap.
     *
     * MySQL rejects invalid UTF-8 and would throw inside terminate(), after the
     * response has been sent, where the failure is invisible. File uploads,
     * binary pastes and mixed-encoding legacy data all reach $request->all(),
     * so JSON_INVALID_UTF8_SUBSTITUTE is not optional here.
     *
     * @param  array<array-key, mixed>|null  $masked
     */
    public static function encode(?array $masked): ?string
    {
        if ($masked === null || $masked === []) {
            return null;
        }

        $flags = JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

        try {
            $json = json_encode($masked, $flags);

            if ($json === false) {
                return null;
            }

            $maxBytes = (int) config('activity-log.masking.max_bytes', 8192);

            if (strlen($json) <= $maxBytes) {
                return $json;
            }

            /**
             * Over the cap, keep the forensically important part - which fields
             * were submitted - rather than the values.
             */
            $fallback = json_encode([
                '_truncated' => true,
                '_size_bytes' => strlen($json),
                '_keys' => array_keys($masked),
            ], $flags);

            return $fallback === false ? null : $fallback;
        } catch (Throwable $e) {
            Log::channel(config('activity-log.log_channel'))
                ->warning('Failed to encode masked activity payload.', ['exception' => $e]);

            return null;
        }
    }

    /**
     * @param  array<array-key, mixed>  $input
     * @return array<array-key, mixed>
     */
    private static function walk(array $input, int $depth): array
    {
        $maxDepth = (int) config('activity-log.masking.max_depth', 6);
        $maxItems = (int) config('activity-log.masking.max_items', 100);
        $dropKeys = array_map('strtolower', (array) config('activity-log.masking.drop_keys', []));

        $result = [];
        $seen = 0;

        foreach ($input as $key => $value) {
            $lowerKey = is_string($key) ? strtolower($key) : (string) $key;

            if (in_array($lowerKey, $dropKeys, true)) {
                continue;
            }

            if ($seen >= $maxItems) {
                $result['_omitted'] = count($input) - $seen;
                break;
            }

            $seen++;

            if (self::isSensitive($lowerKey)) {
                $result[$key] = self::replacement();

                continue;
            }

            $result[$key] = self::maskValue($value, $depth, $maxDepth);
        }

        return $result;
    }

    private static function maskValue(mixed $value, int $depth, int $maxDepth): mixed
    {
        if ($value instanceof UploadedFile) {
            return self::describeFile($value);
        }

        if (is_array($value)) {
            if ($depth + 1 > $maxDepth) {
                return '[depth-limited]';
            }

            return self::walk($value, $depth + 1);
        }

        if (is_string($value)) {
            $maxLength = (int) config('activity-log.masking.max_value_length', 500);

            return strlen($value) > $maxLength
                ? Str::limit($value, $maxLength).' …[truncated]'
                : $value;
        }

        if (is_scalar($value) || $value === null) {
            return $value;
        }

        return '['.get_debug_type($value).']';
    }

    /**
     * Contents are never stored. Type checking inside the recursion rather than
     * walking allFiles() separately handles arrays of files and nested files
     * for free, keeping them under their original keys.
     *
     * @return array<string, mixed>
     */
    private static function describeFile(UploadedFile $file): array
    {
        return [
            '_file' => true,
            'name' => $file->getClientOriginalName(),
            'extension' => $file->getClientOriginalExtension(),
            'mime' => $file->getClientMimeType(),
            'size' => rescue(fn () => $file->getSize(), null, false),
            'valid' => $file->isValid(),
        ];
    }

    private static function isSensitive(string $lowerKey): bool
    {
        $keys = array_map('strtolower', (array) config('activity-log.masking.keys', []));

        if (in_array($lowerKey, $keys, true)) {
            return true;
        }

        foreach ((array) config('activity-log.masking.contains', []) as $fragment) {
            if ($fragment !== '' && str_contains($lowerKey, strtolower($fragment))) {
                return true;
            }
        }

        return false;
    }

    private static function replacement(): string
    {
        return (string) config('activity-log.masking.replacement', '[REDACTED]');
    }
}
