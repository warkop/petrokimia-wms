<?php

use App\Http\Models\AktivitasFoto;
use Faker\Generator as Faker;

$factory->define(AktivitasFoto::class, function (Faker $faker) {
    $id_aktivitas_harian = $faker->numberBetween(1, 50);
    $dir = storage_path('app/public') . '/aktivitas_harian/' . $id_aktivitas_harian;
    if (!file_exists(storage_path('app/public') . '/aktivitas_harian/')) {
        mkdir(storage_path('app/public') . '/aktivitas_harian/', 755);
        if (!file_exists($dir)) {
            mkdir($dir, 755);
        }
    } else {
        if (!file_exists($dir)) {
            mkdir($dir, 755);
        }
    }

    $width = 640;
    $height = 480;

    $file_ori = $faker->image($dir, $width, $height, 'cats', false);

    $size = filesize($dir . '/' . $file_ori);

    return [
        'id_aktivitas_harian'   => $id_aktivitas_harian,
        'id_foto_jenis'         => $faker->numberBetween(1, 4),
        'foto'                  => $file_ori,
        'size'                  => $size,
        'lat'                   => $faker->latitude(-7, -6),
        'lng'                   => $faker->longitude(112, 113),
    ];
});
