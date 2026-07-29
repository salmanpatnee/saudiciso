<?php

namespace App\Support;

use App\Enums\ActivityLogType;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Maps a finished request to an audit activity type.
 *
 * Extending the taxonomy takes one of three forms, all one liners:
 *
 * 1. New global rule - add a case to ActivityLogType and an entry to rules().
 * 2. Per action override - any controller may call
 *    $request->attributes->set('activity_log.type', ActivityLogType::Export);
 *    which the first rule honours without touching this class.
 * 3. Event driven - listeners call ActivityAnnotator::type(), which writes the
 *    same attribute.
 */
final class ActivityTypeClassifier
{
    public static function classify(Request $request, Response $response): ActivityLogType
    {
        foreach (self::rules() as [$matches, $type]) {
            if ($matches($request, $response)) {
                return $type instanceof ActivityLogType
                    ? $type
                    : $request->attributes->get(ActivityAnnotator::KEY_TYPE);
            }
        }

        return ActivityLogType::Other;
    }

    /**
     * Ordered; first match wins, so the order is the semantics.
     *
     * @return array<int, array{0: callable(Request, Response): bool, 1: ActivityLogType|null}>
     */
    private static function rules(): array
    {
        return [
            [fn (Request $r): bool => $r->attributes->get(ActivityAnnotator::KEY_TYPE) instanceof ActivityLogType, null],

            [fn (Request $r, Response $s): bool => in_array($s->getStatusCode(), [401, 403], true), ActivityLogType::PermissionDenied],

            [fn (Request $r, Response $s): bool => $s->getStatusCode() === 404, ActivityLogType::NotFound],

            [fn (Request $r, Response $s): bool => $s->getStatusCode() >= 500, ActivityLogType::Exception],

            [fn (Request $r, Response $s): bool => self::isValidationFailure($r, $s), ActivityLogType::ValidationFailed],

            [fn (Request $r): bool => count($r->allFiles()) > 0, ActivityLogType::FileUpload],

            [fn (Request $r, Response $s): bool => self::isDownload($s), ActivityLogType::FileDownload],

            [fn (Request $r): bool => (bool) $r->routeIs('*.store'), ActivityLogType::Create],

            [fn (Request $r): bool => $r->routeIs('*.update') || $r->isMethod('PUT') || $r->isMethod('PATCH'), ActivityLogType::Update],

            [fn (Request $r): bool => $r->isMethod('DELETE') || $r->routeIs('*.destroy'), ActivityLogType::Delete],

            [fn (Request $r): bool => $r->expectsJson() || $r->is('api/*'), ActivityLogType::ApiRequest],

            [fn (Request $r): bool => $r->isMethod('POST'), ActivityLogType::FormSubmit],

            [fn (Request $r, Response $s): bool => $r->isMethod('GET') && $s->getStatusCode() < 400 && ! $r->ajax(), ActivityLogType::PageVisit],
        ];
    }

    /**
     * On web routes a failed $request->validate() produces a 302 redirect with
     * a flashed errors bag, not a 422. Only JSON requests get the 422.
     */
    public static function isValidationFailure(Request $request, Response $response): bool
    {
        if ($response->getStatusCode() === 422) {
            return true;
        }

        if (! $response->isRedirect()) {
            return false;
        }

        if (! in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return false;
        }

        return $request->hasSession() && $request->session()->get('errors') !== null;
    }

    private static function isDownload(Response $response): bool
    {
        if ($response instanceof BinaryFileResponse || $response instanceof StreamedResponse) {
            return true;
        }

        $disposition = (string) $response->headers->get('Content-Disposition');

        return str_contains($disposition, 'attachment');
    }
}
