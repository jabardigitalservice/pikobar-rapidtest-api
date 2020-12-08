<?php

namespace Tests\Feature;

use App\Entities\RdtApplicant;
use App\Entities\RdtEvent;
use App\Entities\RdtInvitation;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class RdtCheckinTest extends TestCase
{
    /** @test */
    public function can_event_check()
    {
        /**
         * @var RdtEvent $rdtEvent
         */
        $rdtEvent = factory(RdtEvent::class)->create();

        $this->postJson('/api/rdt/event-check', [
            'event_code' => $rdtEvent->event_code,
        ])
            ->assertSuccessful()
            ->assertJsonStructure(['data' => ['event_code', 'event_name', 'start_at', 'end_at', 'invitations']])
            ->assertJsonFragment([
                'event_code' => $rdtEvent->event_code,
                'event_name' => $rdtEvent->event_name,
                'start_at'   => $rdtEvent->start_at,
                'end_at'     => $rdtEvent->end_at,
            ]);
    }

    /** @test */
    public function cannot_event_check_past()
    {
        /**
         * @var RdtEvent $rdtEvent
         */
        $rdtEvent = factory(RdtEvent::class)->make();

        $start = new Carbon();
        $start->hours(8)->minutes(0)->seconds(0);

        $rdtEvent->start_at = $start->subWeek();
        $rdtEvent->end_at   = $start->copy()->subWeek()->addHours(6);
        $rdtEvent->save();

        $this->postJson('/api/rdt/event-check', [
            'event_code' => $rdtEvent->event_code,
        ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonFragment([
                'error' => 'EVENT_PAST',
            ]);
    }

    /** @test */
    public function can_checkin()
    {
        /**
         * @var RdtApplicant $rdtApplicant
         * @var RdtEvent $rdtEvent
         */
        $rdtApplicant = factory(RdtApplicant::class)->create();
        $rdtEvent     = factory(RdtEvent::class)->create();

        $rdtInvitation = new RdtInvitation();
        $rdtInvitation->applicant()->associate($rdtApplicant);
        $rdtInvitation->event()->associate($rdtEvent);
        $rdtInvitation->save();

        $this->postJson('/api/rdt/checkin', [
            'event_code'        => $rdtEvent->event_code,
            'registration_code' => $rdtApplicant->registration_code,
            'lab_code_sample'   => 'L0001',
            'location'          => 'PUSKESMAS CIPEDES',
        ])
            ->assertSuccessful()
            ->assertJsonStructure(['data' => ['registration_code', 'name', 'status']])
            ->assertJsonFragment([
                'registration_code' => $rdtApplicant->registration_code,
                'name'              => $rdtApplicant->name,
            ]);

        $this->assertDatabaseHas('rdt_invitations', [
            'rdt_event_id'      => $rdtEvent->id,
            'registration_code' => $rdtApplicant->registration_code,
            'lab_code_sample'   => 'L0001',
            'attend_location'   => 'PUSKESMAS CIPEDES',
        ]);
    }

    /** @test */
    public function can_checkin_without_invitation()
    {
        /**
         * @var RdtApplicant $rdtApplicant
         * @var RdtEvent $rdtEvent
         */
        $rdtApplicant = factory(RdtApplicant::class)->create();
        $rdtEvent     = factory(RdtEvent::class)->create();

        $this->postJson('/api/rdt/checkin', [
            'event_code'        => $rdtEvent->event_code,
            'registration_code' => $rdtApplicant->registration_code,
            'lab_code_sample'   => 'L0001',
            'location'          => 'PUSKESMAS CIPEDES',
        ])
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonFragment([
            'error' => 'APPLICANT_NOT_INVITED_AT_THIS_EVENT',
        ]);
    }

    /** @test */
    public function cannot_checkin_past()
    {
        /**
         * @var RdtApplicant $rdtApplicant
         * @var RdtEvent $rdtEvent
         */
        $rdtApplicant = factory(RdtApplicant::class)->create();
        $rdtEvent     = factory(RdtEvent::class)->make();

        $start = new Carbon();
        $start->hours(8)->minutes(0)->seconds(0);

        $rdtEvent->start_at = $start->subWeek();
        $rdtEvent->end_at   = $start->copy()->subWeek()->addHours(6);
        $rdtEvent->save();

        $rdtInvitation = new RdtInvitation();
        $rdtInvitation->applicant()->associate($rdtApplicant);
        $rdtInvitation->event()->associate($rdtEvent);
        $rdtInvitation->save();

        $this->postJson('/api/rdt/checkin', [
            'event_code'        => $rdtEvent->event_code,
            'registration_code' => $rdtApplicant->registration_code,
            'lab_code_sample'   => 'L0001',
            'location'          => 'PUSKESMAS CIPEDES',
        ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonFragment([
                'error' => 'EVENT_PAST',
            ]);
    }

    /** @test */
    public function cannot_checkin_already_checkin()
    {
        /**
         * @var RdtApplicant $rdtApplicant
         * @var RdtEvent $rdtEvent
         */
        $rdtApplicant = factory(RdtApplicant::class)->create();
        $rdtEvent     = factory(RdtEvent::class)->create();

        // Already checkin
        $rdtInvitation = new RdtInvitation();
        $rdtInvitation->applicant()->associate($rdtApplicant);
        $rdtInvitation->event()->associate($rdtEvent);

        $rdtInvitation->attended_at = now();
        $rdtInvitation->save();

        $this->postJson('/api/rdt/checkin', [
            'event_code'        => $rdtEvent->event_code,
            'registration_code' => $rdtApplicant->registration_code,
            'lab_code_sample'   => 'L0001',
            'location'          => 'PUSKESMAS CIPEDES',
        ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonFragment([
                'error' => 'ALREADY_CHECKIN',
            ]);
    }

    /** @test */
    public function cannot_checkin_already_used_lab_code()
    {
        /**
         * @var RdtApplicant $rdtApplicantOne
         * @var RdtApplicant $rdtApplicantTwo
         * @var RdtEvent $rdtEvent
         */
        $rdtApplicantOne = factory(RdtApplicant::class)->create();
        $rdtApplicantTwo = factory(RdtApplicant::class)->create();
        $rdtEvent        = factory(RdtEvent::class)->create();

        // Other Person
        $rdtInvitation = new RdtInvitation();
        $rdtInvitation->applicant()->associate($rdtApplicantOne);
        $rdtInvitation->event()->associate($rdtEvent);

        $rdtInvitation->attended_at     = now();
        $rdtInvitation->lab_code_sample = 'L0001'; // simulate already used on database
        $rdtInvitation->save();

        // Current Person
        $rdtInvitation = new RdtInvitation();
        $rdtInvitation->applicant()->associate($rdtApplicantTwo);
        $rdtInvitation->event()->associate($rdtEvent);
        $rdtInvitation->save();

        $this->postJson('/api/rdt/checkin', [
            'event_code'        => $rdtEvent->event_code,
            'registration_code' => $rdtApplicantTwo->registration_code,
            'lab_code_sample'   => 'L0001',
            'location'          => 'PUSKESMAS CIPEDES',
        ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonFragment([
                'error' => 'ALREADY_USED_LAB_CODE_SAMPLE',
            ]);
    }
}
