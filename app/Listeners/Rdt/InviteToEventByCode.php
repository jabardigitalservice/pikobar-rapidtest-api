<?php

namespace App\Listeners\Rdt;

use App\Entities\RdtEvent;
use App\Entities\RdtInvitation;
use App\Enums\RdtApplicantStatus;
use App\Events\Rdt\ApplicantRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class InviteToEventByCode
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param \App\Events\Rdt\ApplicantRegistered $event
     * @return void
     */
    public function handle(ApplicantRegistered $event)
    {
        $applicant = $event->applicant;

        // Jika pendaftar tidak memiliki pikobar_session_id, skip proses
        if ($applicant->pikobar_session_id === null) {
            Log::info('APPLICANT_REGISTER_NO_SESSION_ID', [
                'applicant' => $applicant->toArray(),
            ]);

            return;
        }

        // Jika pendaftar dari Prixa, skip proses
        if (strlen($applicant->pikobar_session_id) === 36) {
            Log::info('APPLICANT_REGISTER_FROM_PERIKSA_MANDIRI', [
                'applicant' => $applicant->toArray(),
            ]);

            return;
        }

        /**
         * @var RdtEvent $rdtEvent
         */
        $rdtEvent = RdtEvent::where('event_code', $applicant->pikobar_session_id)->first();

        if ($rdtEvent === null) {
            Log::info('APPLICANT_REGISTER_INVITE_TO_EVENT_NOTFOUND', [
                'applicant' => $applicant->toArray(),
            ]);

            return;
        }

        $eventEndAt = $rdtEvent->end_at;
        $now = now();

        // Jika event sudah berakhir, jangan auto-invite ke event.
        if ($now->gt($eventEndAt)) {
            $applicant->pikobar_session_id = null;
            $applicant->save();

            Log::info('APPLICANT_REGISTER_INVITE_TO_EVENT_ENDED', [
                'applicant' => $applicant->toArray(),
                'event' => $rdtEvent->toArray(),
            ]);

            return;
        }

        $invitation = new RdtInvitation();
        $invitation->registration_code = $applicant->registration_code;

        $invitation->event()->associate($rdtEvent);
        $invitation->applicant()->associate($applicant);
        if ($applicant->pikobar_session_id != null) {
            $firstEventSchedule = $rdtEvent->schedules()->first();
            $invitation->rdt_event_schedule_id = $firstEventSchedule->id;
        }
        
        $invitation->save();

        $applicant->status = RdtApplicantStatus::APPROVED();
        $applicant->save();

        Log::info('APPLICANT_REGISTER_INVITE_TO_EVENT', [
            'applicant' => $applicant->toArray(),
            'event' => $rdtEvent->toArray(),
            'invitation' => $invitation->toArray(),
        ]);
    }
}
