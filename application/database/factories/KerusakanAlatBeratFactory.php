<?php

use App\Http\Models\KerusakanAlatBerat;
use Faker\Generator as Faker;

$factory->define(KerusakanAlatBerat::class, function (Faker $faker) {
    return [
        'id_kerusakan' => $faker->numberBetween(1, 10),
        'id_alat_berat_kat' => $faker->numberBetween(1, 10),
    ];
});
