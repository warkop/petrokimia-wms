<?php

use App\Http\Models\AreaStok;
use Faker\Generator as Faker;

$factory->define(AreaStok::class, function (Faker $faker) {
    return [
        'id_area'       => $faker->numberBetween(1, 20),
        'id_material'   => $faker->numberBetween(1, 20),
        'tanggal'       => now(),
        'jumlah'        => $faker->numberBetween(100, 1000),
    ];
});
