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

Route::get('/master-grup', function () {
    return view('master.master-grup.grid');
});
Route::get('/master-alat-berat', function () {
    return view('master.master-alat-berat.grid');
});
Route::get('/master-user', function () {
    return view('master.master-user.grid');
});
