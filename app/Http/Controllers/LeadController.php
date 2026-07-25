<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Mail\LeadNotificationMail;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LeadController extends Controller
{
    /**
     * Store a newly created lead in storage.
     */
    public function store(StoreLeadRequest $request): JsonResponse
    {
        $lead = Lead::create($request->validated());

        // Send email notification to the configured lead notification recipient
        try {
            Mail::send(new LeadNotificationMail($lead));

            Log::info('Lead notification email sent successfully', [
                'lead_id' => $lead->id,
                'recipient' => config('mail.lead_notification_to'),
                'timestamp' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send lead notification email', [
                'lead_id' => $lead->id,
                'recipient' => config('mail.lead_notification_to'),
                'error' => $e->getMessage(),
                'timestamp' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your inquiry! We will be in touch soon.',
        ]);
    }
}
