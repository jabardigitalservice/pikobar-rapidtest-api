<?php

namespace App\Http\Controllers\Rdt;

use App\Entities\RdtApplicant;
use App\Entities\RdtEvent;
use App\Entities\RdtInvitation;
use App\Enums\RdtApplicantStatus;
use App\Enums\RdtEventStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\RdtEventResource;
use Illuminate\Http\Request;

class RdtEventParticipantAddController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Entities\RdtEvent  $rdtEvent
     * @return \App\Http\Resources\RdtEventResource
     */
    public function __invoke(Request $request, RdtEvent $rdtEvent)
    {
        $applicantIds = $request->input('applicants');

        foreach ($applicantIds as $applicantId) {
            /**
             * @var RdtApplicant $applicant
             */
            $applicant = RdtApplicant::find($applicantId['rdt_applicant_id']);

            if ($applicant === null) {
                continue;
            }

            // Jika status peserta masih NEW, ubah jadi APPROVED
            $applicant->status = RdtApplicantStatus::APPROVED();
            $applicant->pikobar_session_id = $rdtEvent->event_code;
            $applicant->save();

            // Cek existing undangan/invitation.
            // Jika sudah terdaftar di event yang sama, update kloter saja.
            // Jika belum terdaftar, insert baru.
            $invitation = RdtInvitation::firstOrNew([
                'rdt_event_id' => $rdtEvent->id,
                'rdt_applicant_id' => $applicantId['rdt_applicant_id']]);

            $invitation->rdt_event_schedule_id = $applicantId['rdt_event_schedule_id'];
            $invitation->save();
        }

        $rdtEvent->load('invitations');

        return new RdtEventResource($rdtEvent);
    }
}
