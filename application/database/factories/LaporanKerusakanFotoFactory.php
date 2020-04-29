<?php

use App\Http\Models\LaporanKerusakanFoto;
use Faker\Generator as Faker;

$factory->define(LaporanKerusakanFoto::class, function (Faker $faker) {
    $id_laporan = $faker->unique()->numberBetween(1, 50);
    $public = 'app/public';
    $history = '/history/';

    $dir = storage_path($public) . $history . $id_laporan;
    if (!file_exists(storage_path($public) . $history)) {
        mkdir(storage_path($public) . $history, 755);
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

    $nama_file = $ext[0];
    $extension = $ext[1];
    $enc = md5_file($nama_file).'.' . $extension;

    try {
        rename($dir.'/'.$file_ori, $dir . '/' . $enc);
    } catch (\Throwable $th) {
        return 0;
    }

    $size = filesize($dir.'/'.$enc);

    return [
        'id_laporan'    => $id_laporan,
        'file_ori'      => $file_ori,
        'size'          => $size,
        'ekstensi'      => $ext[1],
        'file_enc'      => $enc,
    ];
});
