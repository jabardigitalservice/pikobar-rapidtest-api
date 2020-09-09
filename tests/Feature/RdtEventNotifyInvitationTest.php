<?php

namespace Tests\Feature;

use App\Entities\RdtApplicant;
use App\Entities\RdtEvent;
use App\Entities\RdtInvitation;
use App\Entities\User;
use Tests\TestCase;

class RdtEventNotifyInvitationTest extends TestCase
{
    /** @test */
    public function can_send_notify_participants()
    {
        $user = new User();
        $user->assignPermissions(['notify-participants']);

        /**
         * @var RdtEvent $rdtEvent
         */
        $rdtEvent = factory(RdtEvent::class)->create();

        factory(RdtApplicant::class, 5)->create()->each(function (RdtApplicant $rdtApplicant) use ($rdtEvent) {
            $rdtInvitation = new RdtInvitation();
            $rdtInvitation->event()->associate($rdtEvent);
            $rdtInvitation->applicant()->associate($rdtApplicant);
            $rdtInvitation->save();
        });

        $this->actingAs($user)
            ->postJson("/api/rdt/events/{$rdtEvent->id}/participants-notify", [
                'target'          => 'ALL',
                'method'          => 'BOTH',
                'invitations_ids' => []
            ])
            ->assertSuccessful()
            ->assertJsonFragment(['message' => 'OK']);

        $this->assertDatabaseCount('rdt_invitations', 5);
    }

    /** @test */
    public function cannot_send_notify_unauthenticated()
    {
        /**
         * @var RdtEvent $rdtEvent
         */
        $rdtEvent = factory(RdtEvent::class)->create();

        $this->postJson("/api/rdt/events/{$rdtEvent->id}/participants-notify")
            ->assertUnauthorized();
    }

    /** @test */
    public function cannot_send_notify_no_permission()
    {
        $user = new User();

        /**
         * @var RdtEvent $rdtEvent
         */
        $rdtEvent = factory(RdtEvent::class)->create();

        $this->actingAs($user)
            ->postJson("/api/rdt/events/{$rdtEvent->id}/participants-notify")
            ->assertForbidden();
    }
}
