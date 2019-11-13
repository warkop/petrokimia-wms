<?php

use App\Http\Models\GudangPallet;
use Faker\Generator as Faker;

$factory->define(GudangPallet::class, function (Faker $faker) {
    return [
        'id_gudang' => $faker->numberBetween(1, 50),
        'id_material' => $faker->numberBetween(1, 50),
        'jumlah' => $faker->numberBetween(10, 100),
    ];
});
