<?php

namespace Tests\Feature;

use App\Entities\RdtEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class RdtCheckEventTest extends TestCase
{
    /** @test */
    function rdt_check_event()
    {
        // 1. Create mock
        $rdtEvent = factory(RdtEvent::class)->create();
        $data = [
            'event_code' => $rdtEvent->event_code,
        ];
        // 2. Hit Api Endpoint
        $response = $this->json("GET", "/api/rdt/check-event", $data);
        // 3. Verify and Assertion
        $response->assertStatus(Response::HTTP_OK);
        $response->assertSuccessful();
        $response->assertJsonFragment([
            'end_at'     => $rdtEvent->end_at,
            'event_name' => $rdtEvent->event_name,
            'start_at'   => $rdtEvent->start_at,
            'status'     => $rdtEvent->status,
        ]);
    }
}
