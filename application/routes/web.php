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

Route::get('/', 'Auth\LoginController@index');
Route::get('/login', 'Auth\LoginController@index')->name('login');
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
Route::post('/authenticate', 'Auth\LoginController@authenticate')->name('authenticate');

Route::get('/home', 'DashboardController@index')->middleware(['eauth', 'revalidate'])->name('home');

Route::get('/dashboard', function () {
    return View::make('dashboard.grid');
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
// Route::get('/master-alat-berat', function () {
//     return view('master.master-alat-berat.grid');
// });
Route::get('/master-kerusakan-alat', function () {
    return view('master.master-kerusakan-alat.grid');
});
Route::get('/master-grup', function () {
    return view('master.master-grup.grid');
});

Route::group(['prefix' => 'master-tenaga-kerja-nonorganik', 'middleware' => ['eauth', 'revalidate']], function () {
    Route::get('/', 'JobDeskController@index');
    Route::put('/save', 'JobDeskController@store');
    Route::post('/json', 'JobDeskController@json');
    Route::get('/show/{id}', 'JobDeskController@show');
    Route::delete('/{id}', 'JobDeskController@destroy');
});

Route::group(['prefix' => 'master-pekerjaan', 'middleware' => ['eauth', 'revalidate']], function () {
    Route::get('/', 'JobDeskController@index');
    Route::put('/save', 'JobDeskController@store');
    Route::post('/json', 'JobDeskController@json');
    Route::get('/show/{id}', 'JobDeskController@show');
    Route::delete('/{id}', 'JobDeskController@destroy');
});

Route::group(['prefix' => 'master-shift-kerja', 'middleware' => ['eauth', 'revalidate']], function () {
    Route::get('/', 'ShiftKerjaController@index');
    Route::put('/', 'ShiftKerjaController@store');
    Route::post('/', 'ShiftKerjaController@json');
    Route::get('/{id}', 'ShiftKerjaController@show');
    Route::delete('/{id}', 'ShiftKerjaController@destroy');
});

Route::group(['prefix' => 'master-kerusakan-alat', 'middleware' => ['eauth', 'revalidate']], function () {
    Route::get('/', 'KerusakanAlatBeratController@index');
    Route::put('/save', 'KerusakanAlatBeratController@store');
    Route::post('/json', 'KerusakanAlatBeratController@json');
    Route::get('/show/{id}', 'KerusakanAlatBeratController@show');
    Route::delete('/{id}', 'KerusakanAlatBeratController@destroy');
});

Route::group(['prefix' => 'master-kategori-alat-berat', 'middleware' => ['eauth', 'revalidate']], function () {
    Route::get('/', 'KategoriAlatBeratController@index');
    Route::put('/', 'KategoriAlatBeratController@store');
    Route::post('/', 'KategoriAlatBeratController@json');
    Route::get('/{id}', 'KategoriAlatBeratController@show');
    Route::delete('/{id}', 'KategoriAlatBeratController@destroy');
});

Route::group(['prefix' => 'list-alat-berat', 'middleware' => ['eauth', 'revalidate']], function () {
    Route::get('/{id}', 'ListAlatBeratController@index');
    Route::put('/{id}', 'ListAlatBeratController@store');
    Route::post('/{id}', 'ListAlatBeratController@json');
    Route::get('/{id}/{id_list}', 'ListAlatBeratController@show');
    Route::delete('/{id}/{id_list}', 'ListAlatBeratController@destroy');
});

Route::group(['prefix' => 'master-jenis-foto', 'middleware' => ['eauth', 'revalidate']], function (){
    Route::get('/', 'JenisFotoController@index');
    Route::put('/save', 'JenisFotoController@store');
    Route::post('/json', 'JenisFotoController@json');
    Route::get('/show/{id}', 'JenisFotoController@show');
    Route::delete('/{id}', 'JenisFotoController@destroy');
});

Route::group(['prefix' => 'master-user', 'middleware' => ['eauth', 'revalidate']], function () {
    Route::get('/', 'UsersController@index');
    Route::put('/', 'UsersController@store');
    Route::post('/', 'UsersController@json');
    Route::get('/{id}', 'UsersController@show');
    Route::patch('/{id}', 'UsersController@changePassword');
    Route::delete('/{id}', 'UsersController@destroy');
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
Route::get('/master-alat-berat/list-alat-berat', function () {
    return view('list-alat-berat.grid');
});
Route::get('/stok-adjustment', function () {
    return view('stok-adjusment.grid');
});
Route::get('/gudang/list-alat-berat', function () {
    return view('list-alat-berat-gudang.grid');
});

Route::get('/list-tenaga-kerja-nonorganik', function () {
    return view('list-tenaga-kerja-nonorganik.grid');
});
Route::get('/list-pallet', function () {
    return view('list-pallet.grid');
});
Route::get('/list-area', function () {
    return view('list-area.grid');
});
Route::get('/anggaran-alat-berat', function () {
    return view('anggaran-alat-berat.grid');
});
Route::get('/anggaran-sdm', function () {
    return view('anggaran-sdm.grid');
});



Route::get('/rencana-harian', function () {
    return view('rencana-harian.grid');
});
Route::get('/add-rencana-harian', function () {
    return view('rencana-harian.add');
});
Route::get('/realisasi', function () {
    return view('rencana-harian.realisasi');
});
Route::get('/aktivitas', function () {
    return view('aktivitas.grid');
});
Route::get('/aktivitas/detail', function () {
    return view('aktivitas.detail');
});
Route::get('/aktivitas/tambah', function () {
    return view('aktivitas.add');
});
