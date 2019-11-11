<?php

use App\Http\Models\Material;
use Faker\Generator as Faker;

$factory->define(Material::class, function (Faker $faker) {
    return [
        'id_material_sap' => $faker->numberBetween(1, 100),
        'kategori' => $faker->numberBetween(1, 3),
        'berat' => $faker->numberBetween(50, 100),
        'koefisien_pallet' => $faker->numberBetween(1, 10),
        'nama' => $faker->word(),
    ];
});
