<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Enums\RdtEventStatus;
use App\Entities\RdtEvent;
use App\Enums\JenisRegistrasi;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

$factory->define(RdtEvent::class, function (Faker $faker) {
    $faker->addProvider(new \Faker\Provider\id_ID\Address($faker));

    $start = new Carbon();
    $start->hours(8)->minutes(0)->seconds(0);

    return [
        'event_name'     => 'Event Name',
        'host_name'      => 'Host Name',
        'event_location' => $faker->address,
        'start_at'       => $start->addDays(1),
        'end_at'         => $start->copy()->addHours(6),
        'status'         => RdtEventStatus::PUBLISHED(),
        'jenis_registrasi' => JenisRegistrasi::rujukan()->getValue(),
    ];
});
