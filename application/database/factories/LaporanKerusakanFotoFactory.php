<?php

use App\Http\Models\LaporanKerusakanFoto;
use Faker\Generator as Faker;

$factory->define(LaporanKerusakanFoto::class, function (Faker $faker) {
    $id_laporan = $faker->unique()->numberBetween(1, 50);
    $dir = storage_path('app/public') . '/history/' . $id_laporan;
    if (!file_exists(storage_path('app/public') . '/history/')) {
        mkdir(storage_path('app/public') . '/history/', 755);
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
    $ext = explode('.', $file_ori);

    $enc = md5($ext[0]) . '.' . $ext[1];

    // rename($dir.'\\'.$file_ori, $dir . '\\' . $enc);

    // $size = filesize($dir.'\\'.$enc);
    $size = '';

    return [
        'id_laporan'    => $id_laporan,
        'file_ori'      => $file_ori,
        'size'          => $size,
        'ekstensi'      => $ext[1],
        'file_enc'      => $file_ori,
    ];
});
