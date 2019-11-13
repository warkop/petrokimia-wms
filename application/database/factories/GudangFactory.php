<?php

use App\Http\Models\Gudang;
use Faker\Generator as Faker;

$factory->define(Gudang::class, function (Faker $faker) {
    return [
        'id_karu'   => $faker->unique()->numberBetween(1, 100),
        'id_plant'  => $faker->numberBetween(1, 50),
        'id_sloc'   => $faker->numberBetween(1, 50),
        'nama'      => $faker->unique()->word,
        'tipe_gudang'      => $faker->numberBetween(1, 2),
        'start_date'      => now(),
        'created_at'      => now(),
    ];
});
