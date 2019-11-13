<?php

use App\Http\Models\AktivitasMasterFoto;
use Faker\Generator as Faker;

$factory->define(AktivitasMasterFoto::class, function (Faker $faker) {
    return [
        'id_aktivitas' => $faker->numberBetween(1, 50),
        'id_foto_jenis' => $faker->numberBetween(1, 50),
    ];
});
