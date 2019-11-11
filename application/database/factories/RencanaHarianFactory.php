<?php

use App\Http\Models\RencanaHarian;
use Faker\Generator as Faker;

$factory->define(RencanaHarian::class, function (Faker $faker) {
    return [
        'id_shift' => $faker->numberBetween(1,3),
        'id_gudang' => $faker->numberBetween(1, 7),
        'tanggal' => now(),
        'start_date' => now(),
        'created_at' => now(),
        'created_by' => 3,
    ];
});
