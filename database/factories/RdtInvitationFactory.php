<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Entities\RdtApplicant;
use App\Entities\RdtEvent;
use App\Entities\RdtInvitation;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(RdtInvitation::class, function (Faker $faker) {
    $applicant = factory(RdtApplicant::class)->create();
    $event = factory(RdtEvent::class)->create();
    $eventSchedule = $event->schedules()->first();
    $today = new Carbon();

    return [
        'rdt_applicant_id' => $applicant->id,
        'rdt_event_id' => $event->id,
        'rdt_event_schedule_id' => $eventSchedule,
        'attend_location' => 'PUSKESMAS',
        'test_type' => 'PCR',
        'lab_code_sample' => sprintf('%s%s', 'L', $applicant->registration_code),
        'lab_result_type' => 'NEGATIVE',
        'notified_at' => $today->subDay(),
        'attended_at' => $today,
        'result_at' => now(),
    ];
});
