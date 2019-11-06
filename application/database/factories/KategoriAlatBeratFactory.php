<?php

use App\Http\Models\KategoriAlatBerat;
use Faker\Generator as Faker;

$factory->define(KategoriAlatBerat::class, function (Faker $faker) {
    return [
        'nama' => $faker->word,
        'start_date' => now(),
        'created_by' => 1,
        'created_at' => now(),
    ];
});
