<?php

use App\Http\Models\AktivitasGudang;
use Faker\Generator as Faker;

$factory->define(AktivitasGudang::class, function (Faker $faker) {
    return [
        'id_aktivitas'  => $faker->numberBetween(1, 50),
        'id_gudang'     => $faker->numberBetween(1, 20),
        'created_at'    => date('Y-m-d H:i:s'),
    ];
});
