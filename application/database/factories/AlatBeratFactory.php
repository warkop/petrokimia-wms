<?php

use App\Http\Models\AlatBerat;
use Faker\Generator as Faker;

$factory->define(AlatBerat::class, function (Faker $faker) {
    return [
        'id_kategori'   => $faker->numberBetween(1, 3),
        'nomor_lambung' => $faker->regexify('[A-Z]+[0-9]+[A-Z]{2,4}'),
        'status'        => $faker->numberBetween(0, 1),
        'created_by'    => 1,
        'created_at'    => now(),
    ];
});
