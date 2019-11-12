<?php

use App\Http\Models\Karu;
use Faker\Generator as Faker;

$factory->define(Karu::class, function (Faker $faker) {
    return [
        'nama' => $faker->unique()->name(),
        'no_hp' => $faker->phoneNumber,
        'start_date' => now(),
        'created_at' => now(),
        'created_by' => 1,
        'nik' => $faker->creditCardNumber(),
    ];
});
