<?php

use App\Http\Models\AreaStok;
use Faker\Generator as Faker;

$factory->define(AreaStok::class, function (Faker $faker) {
    return [
        'id_area'       => $faker->unique()->numberBetween(1, 100),
        'id_material'   => $faker->numberBetween(1, 100),
        'tanggal'       => $faker->dateTimeBetween('-4 days', '+0 days'),
        'jumlah'        => $faker->numberBetween(100, 1000),
    ];
});
