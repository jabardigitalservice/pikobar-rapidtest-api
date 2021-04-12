<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Entities\RdtEvent;
use App\Entities\RdtEventSchedule;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(RdtEventSchedule::class, function (Faker $faker) {
    $rdtEvent = factory(RdtEvent::class)->create();
    $today = new Carbon();

    return [
        'rdt_event_id' => $rdtEvent->id,
        'start_at' => $today,
        'end_at' => $today->addDay(15),
    ];
});
