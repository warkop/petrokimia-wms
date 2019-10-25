<?php

use App\Http\Models\Aktivitas;
use Faker\Generator as Faker;

$factory->define(Aktivitas::class, function (Faker $faker) {
    return [
        'nama' => $faker->unique()->name,
        'created_at' => now(),
    ];
});
