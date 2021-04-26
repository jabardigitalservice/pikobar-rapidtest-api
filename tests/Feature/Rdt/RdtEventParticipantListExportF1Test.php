<?php

namespace Tests\Feature\Rdt;

use App\Entities\RdtEvent;
use App\Entities\User;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class RdtEventParticipantListExportF1Test extends TestCase
{
    /** @test */
    public function can_export_f1_applicant_event()
    {
        Excel::fake();

        $user = new User();
        $rdtEvent = factory(RdtEvent::class)->create();

        $response = $this->actingAs($user)->getJson("/api/rdt/events/{$rdtEvent->id}/participants-export-f1");

        $response->assertStatus(Response::HTTP_OK)
            ->assertSuccessful();
    }
}
