<?php

namespace Tests\Feature\Master;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class OccupationTypeListTest extends TestCase
{
    /**
     * @test
     */
    public function can_show_list_occupation_type()
    {
        $response = $this->get('/api/master/occupations');

        $response->assertStatus(Response::HTTP_OK)
            ->assertSuccessful()
            ->assertJsonStructure([
                'data',
            ]);
    }
}
