<?php

namespace Tests\Feature;

use App\Entities\RdtEvent;
use App\Entities\User;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SyncToLabkesTest extends TestCase
{
    /** @test */
    public function syncronize_to_labkes()
    {
        // 1. Mocking Data
        Http::fake();
        $labkesUrl = config('app.labkes_url') . 'api/v1/tes-masif/bulk';
        $labkesApiKey = config('app.labkes_api_key');
        $rdtEvent = factory(RdtEvent::class)->create();
        $user = new User();
        $data = [
            'person_status' => 'CONFIRMED',
        ];

        Http::post($labkesUrl, ['data' => $data, 'api_key' => $labkesApiKey]);

        // 2. Hit endpoint
        $response = $this->actingAs($user)->post("/api/synctolabkes/{$rdtEvent->id}", $data);

        // 3. Assertion
        $response->assertStatus(200);
    }
}
