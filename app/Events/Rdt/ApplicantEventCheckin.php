<?php

namespace App\Events\Rdt;

use App\Channels\WhatsappChannel;
use App\Entities\RdtApplicant;
use App\Entities\RdtInvitation;
use App\Notifications\CheckinThankYou;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ApplicantEventCheckin
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var RdtInvitation
     */
    public $rdtInvitation;

    /**
     * @var RdtApplicant
     */
    public $rdtApplicant;

    /**
     * Create a new event instance.
     *
     * @param RdtApplicant $rdtApplicant
     * @param RdtInvitation $rdtInvitation
     */
    public function __construct(RdtApplicant $rdtApplicant, RdtInvitation $rdtInvitation)
    {
        $this->rdtInvitation = $rdtInvitation;
        $this->rdtApplicant = $rdtApplicant;

        Log::info('APPLICANT_EVENT_CHECKIN', [
            'applicant_id'      => $rdtApplicant->id,
            'registration_code' => $rdtApplicant->registration_code,
            'invitation_id'     => $rdtInvitation->id,
            'event_id'          => $rdtInvitation->rdt_event_id,
            'event_schedule_id' => $rdtInvitation->rdt_event_schedule_id,
            'attended_at'       => $rdtInvitation->attended_at,
        ]);

        $notify = config('notifications.messages.checkin_thankyou');

        if ($notify) {
            $rdtApplicant->notifyNow(
                new CheckinThankYou($rdtInvitation->event, $rdtInvitation),
                [WhatsappChannel::class]
            );
        }
    }
}
