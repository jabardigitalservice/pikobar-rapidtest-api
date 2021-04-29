<?php

namespace Tests\Feature;

use App\Entities\RdtEvent;
use App\Entities\User;
use Illuminate\Http\Response;
use Tests\TestCase;

class RdtEventParticipantListTest extends TestCase
{
    /** @test */
    public function can_show_event_all_list_participants_without_perpage()
    {
        // 1. Mock the data
        $rdtEvent = factory(RdtEvent::class)->create();

        $user = new User();
        $user->assignPermissions(['view-events']);

        // 2. Hit endpoint
        $response = $this->actingAs($user)->json('GET', '/api/rdt/events/' . $rdtEvent->id . '/participants');

        // 3. Assertion
        $response->assertSuccessful()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['data']);
    }

    /** @test */
    public function can_show_event_list_participants_with_search()
    {
        // 1. Mock the data
        $rdtEvent = factory(RdtEvent::class)->create();

        $user = new User();
        $user->assignPermissions(['view-events']);

        $data = [
            'search' => 'Event Name',
        ];

        // 2. Hit endpoint
        $response = $this->actingAs($user)->json('GET', '/api/rdt/events/' . $rdtEvent->id . '/participants', $data);

        // 3. Assertion
        $response->assertSuccessful()
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['data']);
    }

    /** @test */
    public function can_show_event_list_participants_with_perpage()
    {
        // 1. Mock the data
        $rdtEvent = factory(RdtEvent::class)->create();

        $user = new User();
        $user->assignPermissions(['view-events']);

        $data = [
            'per_page' => 50,
        ];

        // 2. Hit endpoint
        $response = $this->actingAs($user)->json('GET', '/api/rdt/events/' . $rdtEvent->id . '/participants', $data);

        // 3. Assertion
        $response->assertSuccessful()
            ->assertJsonStructure(['data', 'meta'])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment((['per_page' => 50]));
    }
}
