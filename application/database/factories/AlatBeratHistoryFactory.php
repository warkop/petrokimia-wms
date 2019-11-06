<?php

use App\Http\Models\AlatBeratHistory;
use Faker\Generator as Faker;

$factory->define(AlatBeratHistory::class, function (Faker $faker) {
    return [
        'id_alat_berat_kerusakan' => $faker->numberBetween(1, 10),
        'keterangan' => $faker->text(200),
        'waktu' => date('Y-m-d H:i:s'),
    ];
});
