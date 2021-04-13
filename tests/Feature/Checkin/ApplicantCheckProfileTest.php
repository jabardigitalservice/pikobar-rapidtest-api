<?php

namespace Tests\Feature\Checkin;

use App\Entities\RdtApplicant;
use Illuminate\Http\Response;
use Tests\TestCase;

class ApplicantCheckProfileTest extends TestCase
{
    /**
     * @test
     */
    public function can_checkin_applicant_profile()
    {
        // 1. Mock data
        $rdtApplicant = factory(RdtApplicant::class)->create();

        $data = [
            'registration_code' => $rdtApplicant->registration_code,
        ];

        // 2. Hit endpoint
        $response = $this->postJson('/api/checkin/applicant-profile', $data);

        // 3. Assertion
        $this->assertDatabaseHas('rdt_applicants', [
            'name' => $rdtApplicant->name,
            'registration_code' => $rdtApplicant->registration_code,
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertSuccessful()
            ->assertJsonStructure(['data'])
            ->assertJsonFragment([
                'registration_code' => $data['registration_code'],
            ]);
    }
}
