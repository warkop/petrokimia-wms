<?php

use App\Http\Models\LaporanKerusakan;
use Faker\Generator as Faker;

$factory->define(LaporanKerusakan::class, function (Faker $faker) {
    return [
        'id_kerusakan'      => $faker->numberBetween(1, 50),
        'id_alat_berat'     => $faker->numberBetween(1, 20),
        'id_shift'          => $faker->numberBetween(1, 3),
        'keterangan'        => $faker->text(200),
        'jenis'             => $faker->numberBetween(1, 2),
        'jam_rusak'         => now(),
        'created_at'        => now(),
        'created_by'        => 1,
    ];
});
