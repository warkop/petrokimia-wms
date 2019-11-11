<?php

use App\Http\Models\Area;
use Faker\Generator as Faker;

$factory->define(Area::class, function (Faker $faker) {
    return [
        'id_gudang' => $faker->numberBetween(1, 100),
        'nama'      => $faker->word,
        'kapasitas' => $faker->numberBetween(100, 1000),
        'tipe'      => $faker->numberBetween(1, 2),
    ];
});
