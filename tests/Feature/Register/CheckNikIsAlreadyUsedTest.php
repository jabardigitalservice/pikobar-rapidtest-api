<?php

namespace Tests\Feature\Register;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class CheckNikIsAlreadyUsedTest extends TestCase
{
    /**
     * @test
     */
    public function testExample()
    {
        $response = $this->post("/api/register/check-nik?nik=18127727110665946");

        $response->assertStatus(Response::HTTP_OK);
    }
}
