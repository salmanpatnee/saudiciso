<?php

namespace App\Enums;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Audit taxonomy for the activity_logs table.
 *
 * Deliberately separate from App\Enums\ActivityType, which is cast on
 * SessionActivity and backed by session_activities.type. Sharing one enum
 * would mean an audit-only change silently redefines the legacy feature's data.
 *
 * Adding a new type is a one line addition here plus one rule in
 * App\Support\ActivityTypeClassifier::rules().
 */
enum ActivityLogType: string
{
    case PageVisit = 'page_visit';
    case FormSubmit = 'form_submit';
    case Create = 'create';
    case Update = 'update';
    case Delete = 'delete';
    case FileUpload = 'file_upload';
    case FileDownload = 'file_download';
    case Export = 'export';
    case ApiRequest = 'api_request';
    case LoginSuccess = 'login_success';
    case LoginFailed = 'login_failed';
    case Logout = 'logout';
    case Lockout = 'lockout';
    case Registration = 'registration';
    case PasswordReset = 'password_reset';
    case PasswordChanged = 'password_changed';
    case EmailVerification = 'email_verification';
    case PermissionDenied = 'permission_denied';
    case ValidationFailed = 'validation_failed';
    case NotFound = 'not_found';
    case Exception = 'exception';
    case Other = 'other';

    public function label(): string
    {
        return Str::headline($this->value);
    }

    /**
     * Tailwind badge classes, so the blade stays free of conditionals.
     *
     * Restricted to the palette present in the vendored TailAdmin build at
     * public/tailadmin/build/style.css. That stylesheet is a committed artifact
     * built without a lockfile, so classes outside it simply do not render.
     */
    public function color(): string
    {
        return match ($this) {
            self::LoginSuccess, self::Registration, self::EmailVerification,
            self::Create, self::FileUpload => 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20',

            self::LoginFailed, self::PermissionDenied, self::Lockout,
            self::Exception, self::Delete => 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20',

            self::ValidationFailed, self::NotFound => 'bg-yellow-50 text-yellow-700',

            self::Update, self::PasswordReset, self::PasswordChanged,
            self::FileDownload, self::Export, self::FormSubmit,
            self::ApiRequest => 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20',

            default => 'bg-gray-100 text-gray-700',
        };
    }

    /**
     * Dot colour for the timeline rail, kept separate because a badge's
     * background tint is too pale to read as a marker.
     */
    public function dotColor(): string
    {
        return match ($this) {
            self::LoginFailed, self::PermissionDenied, self::Lockout,
            self::Exception, self::Delete => 'bg-red-600',

            self::LoginSuccess, self::Registration, self::EmailVerification,
            self::Create, self::FileUpload => 'bg-success-500',

            self::ValidationFailed, self::NotFound => 'bg-warning-500',

            default => 'bg-brand-500',
        };
    }

    /**
     * Sentence-leading verb used by App\Support\ActivityDescriber.
     */
    public function verb(): string
    {
        return match ($this) {
            self::PageVisit => 'Viewed',
            self::FormSubmit => 'Submitted',
            self::Create => 'Created',
            self::Update => 'Updated',
            self::Delete => 'Deleted',
            self::FileUpload => 'Uploaded to',
            self::FileDownload => 'Downloaded from',
            self::Export => 'Exported',
            self::ApiRequest => 'Called',
            self::LoginSuccess => 'Logged in at',
            self::LoginFailed => 'Failed login at',
            self::Logout => 'Logged out from',
            self::Lockout => 'Locked out at',
            self::Registration => 'Registered at',
            self::PasswordReset => 'Reset password at',
            self::PasswordChanged => 'Changed password at',
            self::EmailVerification => 'Verified email at',
            self::PermissionDenied => 'Denied access to',
            self::ValidationFailed => 'Failed validation on',
            self::NotFound => 'Hit missing page',
            self::Exception => 'Errored on',
            self::Other => 'Accessed',
        };
    }

    /**
     * Shaped for the x-form.select component, which reads object properties.
     *
     * @return \Illuminate\Support\Collection<int, object>
     */
    public static function options(): Collection
    {
        return collect(self::cases())->map(
            fn (self $case): object => (object) ['value' => $case->value, 'label' => $case->label()]
        );
    }
}
