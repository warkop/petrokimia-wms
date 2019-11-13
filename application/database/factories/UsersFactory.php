<?php

use App\Http\Models\Users;
use Faker\Generator as Faker;

$factory->define(Users::class, function (Faker $faker) {
    $role_id = $faker->numberBetween(2, 5);
    $id_karu = null;
    $id_tkbm = null;
    if ($role_id == 5) {
        $id_karu = $faker->unique()->numberBetween(1, 100);
    } else {
        $id_tkbm = $faker->unique()->numberBetween(1, 100);
    }

    return [
        'role_id'       => $role_id,
        'name'          => $faker->unique()->name(),
        'username'      => $faker->unique()->word,
        'password'      => bcrypt('qwerty123456'),
        'email'         => $faker->unique()->email,
        'start_date'    => now(),
        'created_at'    => now(),
        'id_tkbm'       => $id_tkbm,
        'id_karu'       => $id_karu,
    ];
});
