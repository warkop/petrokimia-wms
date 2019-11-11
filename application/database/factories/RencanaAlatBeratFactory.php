<?php

use App\Http\Models\RencanaAlatBerat;
use Faker\Generator as Faker;

$factory->define(RencanaAlatBerat::class, function (Faker $faker) {
    return [
        'id_rencana' => $faker->numberBetween(1, 100),
        'id_alat_berat' => $faker->numberBetween(1, 100),
    ];
});
