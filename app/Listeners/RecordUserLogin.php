<?php

namespace App\Listeners;

use App\Enums\SessionStatus;
use App\Models\UserSession;
use App\Support\UserAgentParser;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class RecordUserLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        if ($user->role_id !== 4) {
            return;
        }

        try {
            $request = request();

            // Guards against duplicate rows from a double form submission (e.g. a
            // double-click on the login button firing two POST requests): reuse the
            // just-created row instead of creating a second one for the same login.
            $duplicate = UserSession::where('user_id', $user->id)
                ->where('ip_address', $request->ip())
                ->where('status', SessionStatus::Active)
                ->where('login_at', '>=', now()->subSeconds(5))
                ->latest('id')
                ->first();

            if ($duplicate) {
                session(['user_activity_token' => $duplicate->session_token]);

                return;
            }

            $token = (string) Str::uuid();

            session(['user_activity_token' => $token]);

            $userAgent = (string) $request->userAgent();
            $agent = UserAgentParser::parse($userAgent);

            UserSession::create([
                'user_id' => $user->id,
                'session_token' => $token,
                'login_at' => now(),
                'last_activity_at' => now(),
                'status' => SessionStatus::Active,
                'login_method' => 'password',
                'ip_address' => $request->ip(),
                'browser' => $agent['browser'],
                'platform' => $agent['platform'],
                'device_type' => $agent['device_type'],
                'user_agent' => $userAgent,
            ]);
        } catch (Throwable $e) {
            Log::error('Failed to record user login activity.', [
                'user_id' => $user->id,
                'exception' => $e,
            ]);
        }
    }
}
