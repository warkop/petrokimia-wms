<?php

use App\Http\Models\RencanaTkbm;
use Faker\Generator as Faker;

$factory->define(RencanaTkbm::class, function (Faker $faker) {
    return [
        'id_rencana' => $faker->numberBetween(1, 100),
        'id_tkbm' => $faker->numberBetween(1, 100),
    ];
});
