<?php

use App\Http\Models\TenagaKerjaNonOrganik;
use Faker\Generator as Faker;

$factory->define(TenagaKerjaNonOrganik::class, function (Faker $faker) {
    return [
        'job_desk_id' => $faker->numberBetween(1, 4),
        'nama' => $faker->unique()->name('male'),
        'nik' => $faker->creditCardNumber(),
    ];
});
