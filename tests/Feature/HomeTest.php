<?php

namespace Tests\Feature;

use Illuminate\Http\Response;
use Tests\TestCase;

class HomeTest extends TestCase
{
    /** @test */
    public function test_basic_app()
    {
        $response = $this->get('/');

        $response->assertStatus(Response::HTTP_OK)
            ->assertSuccessful();
    }
}
