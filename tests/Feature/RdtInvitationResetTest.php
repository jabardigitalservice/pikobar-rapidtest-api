<?php

namespace Tests\Feature;

use App\Entities\RdtApplicant;
use App\Entities\RdtEvent;
use App\Entities\RdtInvitation;
use App\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RdtInvitationResetTest extends TestCase
{
    /** @test */
    public function can_update_lab_result_invitation()
    {
        // 1. Mock the data
        $rdtApplicant = factory(RdtApplicant::class)->create();
        $rdtEvent = factory(RdtEvent::class)->create();
        $rdtInvitation = factory(RdtInvitation::class)->create();
        $rdtInvitation->applicant()->associate($rdtApplicant);
        $rdtInvitation->event()->associate($rdtEvent);
        $rdtInvitation->save();

        $user = new User();

        $data = [
            'lab_code_sample' => null,
            'attended_at' => null,
            'attend_location' => null
        ];

        // 2. Hit endpoint
        $response = $this->actingAs($user)->json('PUT', '/api/rdt/invitation/' . $rdtInvitation->id . '/reset', $data);

        // 3. Assertion
        $response->assertOk();
    }

    /** @test */
    public function can_update_lab_result_invitation_unauthorized()
    {
        // 1. Mock the data
        $rdtApplicant = factory(RdtApplicant::class)->create();
        $rdtEvent = factory(RdtEvent::class)->create();
        $rdtInvitation = factory(RdtInvitation::class)->create();
        $rdtInvitation->applicant()->associate($rdtApplicant);
        $rdtInvitation->event()->associate($rdtEvent);
        $rdtInvitation->save();

        $data = [
            'lab_code_sample' => null,
            'attended_at' => null,
            'attend_location' => null
        ];

        // 2. Hit endpoint
        $response = $this->json('PUT', '/api/rdt/invitation/' . $rdtInvitation->id . '/reset', $data);

        // 3. Assertion
        $response->assertUnauthorized();
    }
}
