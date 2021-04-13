<?php

namespace Tests\Feature\Master;

use App\Entities\Area;
use Illuminate\Http\Response;
use Tests\TestCase;

class AreaTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        // Provide mocking data for testing
        $this->area = factory(Area::class)->create();
    }

    /**
     * @test
     */
    public function can_show_all_area()
    {
        $response = $this->get("/api/master/areas");

        $response->assertStatus(Response::HTTP_OK)
            ->assertSuccessful()
            ->assertJsonStructure([
                'data',
            ]);
    }

    /**
     * @test
     */
    public function can_show_all_area_with_depth()
    {
        $response = $this->get("/api/master/areas?depth=50");

        $response->assertStatus(Response::HTTP_OK)
            ->assertSuccessful()
            ->assertJsonStructure([
                'data',
            ]);
    }

    /**
     * @test
     */
    public function can_show_all_area_with_code_kemendagri()
    {
        $response = $this->get("/api/master/areas?parent_code_kemendagri=$this->area->code_kemendagri");

        $response->assertStatus(Response::HTTP_OK)
            ->assertSuccessful()
            ->assertJsonStructure([
                'data',
            ]);
    }

    /**
     * @test
     */
    public function can_show_specific_area()
    {
        $response = $this->get("/api/master/areas/{$this->area->id}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertSuccessful()
            ->assertJsonStructure([
                'data',
            ]);
    }
}
