<?php

namespace Tests\Feature\Rdt;

use App\Entities\RdtEvent;
use App\Entities\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;
use Illuminate\Support\Str;

class RdtEventParticipantListExportF2Test extends TestCase
{
    /** @test */
    public function can_export_f2_applicant_event()
    {
        Excel::fake();

        $user = new User();
        $rdtEvent = factory(RdtEvent::class)->create();

        $response = $this->actingAs($user)->json('GET', '/api/rdt/events/' . $rdtEvent->id . '/participants-export-f2');

        $response->assertStatus(Response::HTTP_OK)
                 ->assertSuccessful();
    }
}
