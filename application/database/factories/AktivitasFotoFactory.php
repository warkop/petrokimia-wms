<?php

use App\Http\Models\AktivitasFoto;
use Faker\Generator as Faker;

$factory->define(AktivitasFoto::class, function (Faker $faker) {
    $id_aktivitas_harian = $faker->numberBetween(1, 50);
    $dir = storage_path('app\\public') . '\\aktivitas_harian\\' . $id_aktivitas_harian;
    if (!file_exists(storage_path('app\\public') . '\\aktivitas_harian\\')) {
        mkdir(storage_path('app\\public') . '\\aktivitas_harian\\', 777);
        if (!file_exists($dir)) {
            mkdir($dir, 777);
        }
    } else {
        if (!file_exists($dir)) {
            mkdir($dir, 777);
        }
    }

    $width = 640;
    $height = 480;

    $file_ori = $faker->image($dir, $width, $height, 'cats', false);
    $ext = explode('.', $file_ori);

    // $enc = md5($ext[0]) . '.' . $ext[1];

    // rename($dir . '\\' . $file_ori, $dir . '\\' . $enc);

    $size = filesize($dir . '\\' . $file_ori);
    // $size = '';

    return [
        'id_aktivitas_harian'   => $id_aktivitas_harian,
        'id_foto_jenis'         => $faker->numberBetween(1, 4),
        'foto'                  => $file_ori,
        'size'                  => $size,
        'lat'                   => $faker->latitude(-7, -6),
        'lng'                   => $faker->longitude(112, 117),
    ];
});
