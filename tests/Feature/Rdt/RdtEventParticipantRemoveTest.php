<?php

namespace Tests\Feature\Rdt;

use App\Entities\RdtApplicant;
use App\Entities\RdtEvent;
use App\Entities\User;
use Illuminate\Http\Response;
use Tests\TestCase;

class RdtEventParticipantRemoveTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->rdtApplicant = factory(RdtApplicant::class)->create();
        $this->rdtEvent = factory(RdtEvent::class)->create();
    }

    /** @test */
    public function rdt_remove_participants_test()
    {
        // 1. Mock Data
        $user = new User();

        $data = [
            'applicants' => [
                'rdt_applicant_id' => $this->rdtApplicant->id,
            ],
        ];

        // 2. Hit Endpoint
        $response = $this->actingAs($user)->post("/api/rdt/events/{$this->rdtEvent->id}/participants-remove", $data);

        // 3. Assertion
        $response->assertStatus(Response::HTTP_OK)
            ->assertSuccessful();
    }

    /** @test */
    public function failed_rdt_remove_participants_test_because_unauthorized()
    {
        // 1. Mock Data
        $data = [
            'applicants' => [
                'rdt_applicant_id' => $this->rdtApplicant->id,
            ],
        ];

        // 2. Hit Endpoint
        $response = $this->post("/api/rdt/events/{$this->rdtEvent->id}/participants-remove", $data);

        // 3. Assertion
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
