<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return View::make('login');
});

Route::get('/master-aktivitas', function () {
    return view('master.master-aktivitas.grid');
});

Route::get('/master-aktivitas/tambah', function () {
    return view('master.master-aktivitas.second');
});

Route::get('/master-karu', function () {
    return view('master.master-karu.grid');
});
Route::get('/master-tenaga-kerja-nonorganik', function () {
    return view('master.master-tenaga-kerja-nonorganik.grid');
});
Route::get('/master-pekerjaan', function () {
    return view('master.master-pekerjaan.grid');
});
Route::get('/master-shift-kerja', function () {
    return view('master.master-shift-kerja.grid');
});
Route::get('/master-alat-berat', function () {
    return view('master.master-alat-berat.grid');
});
Route::get('/master-grup', function () {
    return view('master.master-grup.grid');
});
Route::get('/master-user', function () {
    return view('master.master-user.grid');
});

Route::get('/master-material', function () {
    return view('master.master-material.grid');
});

Route::get('/layout', function () {
    return view('menu-layout.grid');
});
Route::get('/gudang', function () {
    return view('gudang.grid');
});
Route::get('/sub-gudang', function () {
    return view('sub-gudang.grid');
});
Route::get('/list-alat-berat', function () {
    return view('list-alat-berat.grid');
});
Route::get('/list-tenaga-kerja-nonorganik', function () {
    return view('list-tenaga-kerja-nonorganik.grid');
});
Route::get('/list-pallet', function () {
    return view('list-pallet.grid');
});
Route::get('/anggaran-alat-berat', function () {
    return view('anggaran-alat-berat.grid');
});


Route::get('/rencana-harian', function () {
    return view('rencana-harian.grid');
});
