<?php

namespace Tests\Feature\RdtEvents;

use App\Entities\RdtEvent;
use App\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListRdtEventTest extends TestCase
{
    /** @test */
    public function can_show_all_rdt_events()
    {
        // 1. Mock the data
        factory(RdtEvent::class, 50)->create();

        $user = new User();
        $user->assignPermissions(['list-events']);

        // 2. Hit endpoint
        $response = $this->actingAs($user)->json('GET', '/api/rdt/events');

        // 3. Assertion
        $response->assertSuccessful()
            ->assertJsonStructure(['data', 'meta'])
            ->assertJsonFragment((['total' => 50]));
    }

    /** @test */
    public function can_show_all_rdt_events_with_per_page()
    {
        // 1. Mock the data
        factory(RdtEvent::class, 50)->create();

        $user = new User();
        $user->assignPermissions(['list-events']);

        $data = [
            'per_page' => 15
        ];

        // 2. Hit endpoint
        $response = $this->actingAs($user)->json('GET', '/api/rdt/events', $data);

        // 3. Assertion
        $response->assertSuccessful()
            ->assertJsonStructure(['data', 'meta'])
            ->assertJsonFragment((['per_page' => 15]));
    }

    /** @test */
    public function failed_to_show_events_because_unauthorized()
    {
        $response = $this->json('GET', '/api/rdt/events');
        $response->assertUnauthorized();
    }

    /** @test */
    public function failed_to_show_events_because_no_permission()
    {
        $user = new User();

        $response = $this->actingAs($user)->json('GET', '/api/rdt/events');
        $response->assertForbidden();
    }
}
