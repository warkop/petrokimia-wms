<?php

use App\Http\Models\RencanaAreaTkbm;
use Faker\Generator as Faker;

$factory->define(RencanaAreaTkbm::class, function (Faker $faker) {
    return [
        'id_rencana' => $faker->numberBetween(1, 100),
        'id_tkbm' => $faker->numberBetween(1, 100),
        'id_area' => $faker->numberBetween(1, 100),
    ];
});
