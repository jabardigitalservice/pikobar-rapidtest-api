<?php

namespace Tests\Feature\Register;

use App\Entities\RdtApplicant;
use Illuminate\Http\Response;
use Tests\TestCase;

class CheckNikIsAlreadyUsedTest extends TestCase
{
    /**
     * @test
     */
    public function check_nik_applicant()
    {
        // 1. Hit Endpoint
        $response = $this->post("/api/register/check-nik?nik=18127727110665946");

        // 2. Assertion
        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function check_nik_applicant_already_used()
    {
        // 1. Mock Data
        $rdtApplicant = factory(RdtApplicant::class)->create();

        // 2. Hit Endpoint
        $response = $this->post("/api/register/check-nik?nik={$rdtApplicant->nik}");

        // 3. Assertion
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
