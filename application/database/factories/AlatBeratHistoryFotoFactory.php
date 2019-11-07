<?php

use App\Http\Models\AlatBeratHistoryFoto;
use Faker\Generator as Faker;

$factory->define(AlatBeratHistoryFoto::class, function (Faker $faker) {
    $id_ab_history = $faker->unique()->numberBetween(1, 50);
    $dir = storage_path('app/public') . '/history/' . $id_ab_history;
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
    $gambar = [
        'cats',
        'animals',
        'transport',
        'technics',
        'city',
    ];

    return [
        'id_ab_history' => $id_ab_history,
        'foto' => $faker->image($dir, $width, $height, 'cats', false),
    ];
});
