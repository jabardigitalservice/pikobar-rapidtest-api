<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Entities\RdtApplicant;
use App\Enums\Gender;
use App\Enums\PersonCaseStatusEnum;
use App\Enums\RdtApplicantStatus;
use App\Enums\SymptomsInteraction;
use Faker\Generator as Faker;
use Faker\Provider\id_ID\Person;
use Illuminate\Support\Arr;

$factory->define(RdtApplicant::class, function (Faker $faker) {
    $faker->addProvider(new Person($faker));

    return [
        'nik'                  => $faker->nik,
        'name'                 => $faker->name,
        'gender'               => Arr::random(Gender::getValues()),
        'person_status'        => Arr::random(PersonCaseStatusEnum::getValues()),
        'birth_date'           => $faker->dateTimeThisCentury,
        'city_code'            => '32.73',
        'address'              => $faker->address,
        'email'                => $faker->email,
        'phone_number'         => $faker->phoneNumber,
        'symptoms_interaction' => Arr::random(SymptomsInteraction::getValues()),
        'symptoms_notes'       => $faker->text,
        'occupation_name'      => $faker->jobTitle,
        'workplace_name'       => $faker->company,
        'status'               => RdtApplicantStatus::NEW(),
        'registration_at'      => $faker->dateTime(),
    ];
});
