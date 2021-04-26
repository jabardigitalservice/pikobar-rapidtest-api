<?php

namespace Tests\Feature\Rdt;

use App\Entities\RdtApplicant;
use Illuminate\Http\Response;
use Tests\TestCase;

class RdtCheckStatusTest extends TestCase
{
    /** @test */
    public function check_applicant_with_registration_code()
    {
        // 1. Mock Data
        $rdtApplicant = factory(RdtApplicant::class)->create();

        $data = [
            'registration_code' => $rdtApplicant->registration_code,
            'g-recaptcha-response' => 'X',
        ];

        // 2. Hit Endpoint
        $response = $this->post("/api/rdt/check", $data);

        // 3. Assertion
        $response->assertStatus(Response::HTTP_OK)
                ->assertSuccessful();
    }

    /** @test */
    public function failed_check_applicant_because_validation_registration_null()
    {
        // 1. Mock Data
        factory(RdtApplicant::class)->create();

        $data = [
            'g-recaptcha-response' => 'X',
        ];

        // 2. Hit Endpoint
        $response = $this->post("/api/rdt/check", $data);

        // 3. Assertion
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function failed_check_applicant_because_validation_recaptcha_null()
    {
        // 1. Mock Data
        $rdtApplicant = factory(RdtApplicant::class)->create();

        $data = [
            'registration_code' => $rdtApplicant->registration_code,
        ];

        // 2. Hit Endpoint
        $response = $this->post("/api/rdt/check", $data);

        // 3. Assertion
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
