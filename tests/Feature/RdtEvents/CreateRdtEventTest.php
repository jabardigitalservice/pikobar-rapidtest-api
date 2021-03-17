<?php

namespace Tests\Feature\RdtEvents;

use App\Entities\RdtEvent;
use App\Entities\User;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CreateRdtEventTest extends TestCase
{
    /** @test */
    public function can_create_event()
    {
        $user = new User();
        $user->assignPermissions(['create-events']);

        /**
         * @var RdtEvent $rdtEvent
         */
        $rdtEvent = factory(RdtEvent::class)->make();

        $this->actingAs($user)
            ->postJson('/api/rdt/events',[
            'event_name'        => 'Event Name',
            'host_name'         => $rdtEvent->host_name,
            'event_location'    => $rdtEvent->event_location,
            'city_code'         => '32.73',
            'status'            => $rdtEvent->status,
            'jenis_registrasi'  => $rdtEvent->jenis_registrasi,
            'start_at'          => $startAt = new Carbon(),
            'end_at'            => $endAt = new Carbon(),
            'schedules'         => [
                [
                    'start_at' => $startAt,
                    'end_at' => $endAt,
                ]
            ],
        ])
        ->assertSuccessful()
        ->assertJsonStructure(['data' => ['event_name', 'event_code']])
        ->assertJsonFragment([
            'event_name' => 'Event Name',
            'host_name' => 'Host Name',
        ]);
    }
}
