<?php

use App\Http\Models\AktivitasHarian;
use Faker\Generator as Faker;

$factory->define(AktivitasHarian::class, function (Faker $faker) {
    return [
        'id_aktivitas' => $faker->numberBetween(1, 20),
        'id_gudang' => $faker->numberBetween(1, 10),
        'id_karu' => $faker->numberBetween(1, 10),
        'id_shift' => $faker->numberBetween(1, 3),
        'id_area' => $faker->numberBetween(1, 50),
        'id_alat_berat' => $faker->numberBetween(1, 20),
        'ttd' => $faker->numberBetween(0, 1),
        'sistro' => $faker->numberBetween(0, 1),
        'created_by' => 1,
        'created_at' => now(),
    ];
});
