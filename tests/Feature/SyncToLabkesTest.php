<?php

namespace Tests\Feature;

use App\Entities\RdtApplicant;
use App\Entities\RdtEvent;
use App\Entities\RdtInvitation;
use App\Entities\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SyncToLabkesTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->labkesUrl = config('app.labkes_url') . 'api/v1/tes-masif/bulk';
        $this->labkesApiKey = config('app.labkes_api_key');
        $this->rdtEvent = factory(RdtEvent::class)->create();
        $this->rdtApplicant = factory(RdtApplicant::class)->create();
    }

    /** @test */
    public function syncronize_to_labkes()
    {
        // 1. Mocking Data
        Http::fake([
            config('app.labkes_url') => Http::response([
                'message' => 'Tes Masif Berhasil Ditambahkan',
            ], Response::HTTP_OK, [
                'Content-Type' => 'application/json',
            ]),
        ]);

        $payloads = factory(RdtInvitation::class)->create([
            'rdt_applicant_id' => $this->rdtApplicant->id,
            'rdt_event_id' => $this->rdtEvent->id,
        ]);

        $user = new User();

        Http::post($this->labkesUrl, ['data' => $payloads, 'api_key' => $this->labkesApiKey]);

        // 2. Hit endpoint
        $this->actingAs($user)->post("/api/synctolabkes/{$this->rdtEvent->id}")
            ->assertStatus(Response::HTTP_OK)
            ->assertSuccessful();

        // 3. Assertion
        Http::assertSent(function ($request) {
            return $request->hasHeader('Content-Type', 'application/json');
        });
    }

    /** @test */
    public function failed_syncronize_to_labkes_because_code_sample_is_null()
    {
        // 1. Mocking Data
        Http::fake([
            config('app.labkes_url') => Http::response([
                'message' => 'Tes Masif Berhasil Ditambahkan',
            ], Response::HTTP_OK, [
                'Content-Type' => 'application/json',
            ]),
        ]);

        $payloads = factory(RdtInvitation::class)->create([
            'rdt_applicant_id' => $this->rdtApplicant->id,
            'rdt_event_id' => $this->rdtEvent->id,
            'lab_code_sample' => null,
        ]);

        $user = new User();

        Http::post($this->labkesUrl, ['data' => $payloads, 'api_key' => $this->labkesApiKey]);

        // 2. Hit endpoint
        $this->actingAs($user)->post("/api/synctolabkes/{$this->rdtEvent->id}");

        // 3. Assertion
        Http::assertSent(function ($request) {
            return $request->hasHeader('Content-Type', 'application/json');
        });
    }
}
