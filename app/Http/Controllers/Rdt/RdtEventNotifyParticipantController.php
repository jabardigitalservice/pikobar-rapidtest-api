<?php

namespace App\Http\Controllers\Rdt;

use App\Entities\RdtEvent;
use App\Entities\RdtInvitation;
use App\Http\Controllers\Controller;
use App\Notifications\RdtEventInvitation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class RdtEventNotifyParticipantController extends Controller
{

    public function __invoke(Request $request, RdtEvent $rdtEvent)
    {
        Gate::authorize('notify-participants');

        $notifyTarget  = $request->input('target', 'ALL');
        $notifyMethod  = $request->input('method', 'BOTH');
        $invitationIds = $request->input('invitations_ids');

        $invitations = $rdtEvent->invitations;

        if ($notifyTarget === 'SELECTED') {
            $invitations = $rdtEvent->invitations()->whereIn('id', $invitationIds)->get();
        }

        $invitations->load(['applicant']);

        foreach ($invitations as $invitation) {
            $this->notifyEachInvitation($rdtEvent, $invitation, $notifyMethod);
        }

        return response()->json(['message' => 'OK']);
    }

    protected function notifyEachInvitation(RdtEvent $rdtEvent, RdtInvitation $invitation, $notifyMethod)
    {
        $applicant = $invitation->applicant;

        if ($notifyMethod === 'BOTH') {
            $applicant->notify(new RdtEventInvitation($rdtEvent));

            $invitation->notified_at = Carbon::now();
            $invitation->save();
        }

        Log::info('EVENT_NOTIFY_PARTICIPANT', [
            'event_id'                    => $rdtEvent->id, 'invitation_id' => $invitation->id,
            'applicant_id'                => $applicant->id,
            'applicant_registration_code' => $applicant->registration_code,
        ]);
    }
}
