<?php

namespace Tests\Feature\Checkin;

use App\Entities\RdtApplicant;
use App\Entities\RdtEvent;
use App\Entities\RdtInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class RdtEventParticipantsTest extends TestCase
{
    /**
     * @test
     */
    public function can_checkin_applicant_event()
    {
        // 1. Mock data
        $rdtEvent = factory(RdtEvent::class)->create();
        $rdtApplicant = factory(RdtApplicant::class)->create();
        $rdtInvitation = factory(RdtInvitation::class)->create([
            'rdt_applicant_id' => $rdtApplicant->id,
            'rdt_event_id' => $rdtEvent->id
        ]);

        $data = [
            'event_code' => $rdtEvent->event_code,
            'per_page' => 50,
            'keyword' => $rdtApplicant->name,
        ];

        // 2. Hit endpoint
        $response = $this->postJson('/api/checkin/event/participants', $data);

        // 3. Assertion
        $this->assertDatabaseHas('rdt_invitations', [
            'registration_code' => $rdtInvitation->registration_code,
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertSuccessful()
            ->assertJsonStructure([
                'data',
                'meta'
            ])
            ->assertJsonFragment([
                'registration_code' => $rdtApplicant->registration_code,
                'lab_code_sample' => $rdtInvitation->lab_code_sample,
            ]);
    }
}
