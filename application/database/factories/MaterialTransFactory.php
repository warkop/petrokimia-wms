<?php

use App\Http\Models\MaterialTrans;
use Faker\Generator as Faker;

$factory->define(MaterialTrans::class, function (Faker $faker) {
    return [
        'id_material' => $faker->numberBetween(1, 30),
        'tanggal' => now(),
        'tipe' => $faker->numberBetween(1, 2),
        'jumlah' => $faker->numberBetween(10, 150),
        'id_aktivitas_harian' => $faker->numberBetween(1, 50),
    ];
});
