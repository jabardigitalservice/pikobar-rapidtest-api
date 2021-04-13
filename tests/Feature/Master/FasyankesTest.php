<?php

namespace Tests\Feature\Master;

use Illuminate\Http\Response;
use Tests\TestCase;

class FasyankesTest extends TestCase
{
    /**
     * @test
     */
    public function can_show_list_fasyankes()
    {
        $response = $this->get('/api/master/fasyankes');

        $response->assertStatus(Response::HTTP_OK)
            ->assertSuccessful()
            ->assertJsonStructure([
                'data',
            ]);
    }

    /**
     * @test
     */
    public function can_show_list_fasyankes_with_fasyankes_name()
    {
        $response = $this->get("/api/master/fasyankes?name=PKM Cimahi");

        $response->assertStatus(Response::HTTP_OK)
            ->assertSuccessful()
            ->assertJsonStructure([
                'data',
            ]);
    }

    /**
     * @test
     */
    public function can_show_list_fasyankes_with_type()
    {
        $response = $this->get("/api/master/fasyankes?type=rumah_sakit");

        $response->assertStatus(Response::HTTP_OK)
            ->assertSuccessful()
            ->assertJsonStructure([
                'data',
            ]);
    }
}
