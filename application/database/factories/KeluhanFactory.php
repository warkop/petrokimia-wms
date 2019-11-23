<?php

use App\Http\Models\Keluhan;
use Faker\Generator as Faker;

$factory->define(Keluhan::class, function (Faker $faker) {
    return [
        'nama' => $faker->unique()->word,
        'start_date' => now(),
        'created_by' => 1,
        'created_at' => now(),
    ];
});
