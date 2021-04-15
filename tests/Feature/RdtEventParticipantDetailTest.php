<?php

namespace Tests\Feature;

use App\Entities\RdtApplicant;
use App\Entities\RdtEvent;
use App\Entities\RdtInvitation;
use App\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RdtEventParticipantDetailTest extends TestCase
{
    /** @test */
    public function can_load_event_participant_detail()
    {
        // 1. Mock the data
        $rdtApplicant = factory(RdtApplicant::class)->create();
        $rdtEvent = factory(RdtEvent::class)->create();
        $rdtInvitation = factory(RdtInvitation::class)->create();
        $rdtInvitation->applicant()->associate($rdtApplicant);
        $rdtInvitation->event()->associate($rdtEvent);
        $rdtInvitation->save();

        $user = new User();

        // 2. Hit endpoint
        $response = $this->actingAs($user)->json('GET', '/api/rdt/invitation/'. $rdtInvitation->id);

        // 3. Assertion
        $response->assertSuccessful();
    }

    /** @test */
    public function can_load_event_participant_detail_unauthorized()
    {
        // 1. Mock the data
        $rdtApplicant = factory(RdtApplicant::class)->create();
        $rdtEvent = factory(RdtEvent::class)->create();
        $rdtInvitation = factory(RdtInvitation::class)->create();
        $rdtInvitation->applicant()->associate($rdtApplicant);
        $rdtInvitation->event()->associate($rdtEvent);
        $rdtInvitation->save();

        // 2. Hit endpoint
        $response = $this->json('GET', '/api/rdt/invitation/'. $rdtInvitation->id);

        // 3. Assertion
        $response->assertUnauthorized();
    }
}
