<?php

namespace Tests\Feature\Rdt;

use App\Entities\RdtApplicant;
use Illuminate\Http\Response;
use Tests\TestCase;

class RdtSurveyStoreTest extends TestCase
{
    /** @test */
    public function rdt_survey_applicant()
    {
        // 1. Mock Data
        $rdtApplicant = factory(RdtApplicant::class)->create();

        $data = [
            'registration_code' => $rdtApplicant->registration_code,
        ];

        // 2. Hit Endpoint
        $response = $this->post("/api/rdt/survey", $data);

        // 3. Assertion
        $response->assertStatus(Response::HTTP_CREATED)
            ->assertSuccessful();
    }
}
