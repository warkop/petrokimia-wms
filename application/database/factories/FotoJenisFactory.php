<?php

use App\Http\Models\JenisFoto;
use Faker\Generator as Faker;

$factory->define(JenisFoto::class, function (Faker $faker) {
    return [
        'nama' => $faker->unique()->word,
        'start_date' => now(),
        'created_at' => now(),
    ];
});
