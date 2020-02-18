<?php
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use Illuminate\Support\Facades\Route;

Route::post('/login', 'API\AuthController@authenticate');
Route::post('/logout', 'API\AuthController@logout');

Route::group(['middleware' => 'api.auth'], function () {
    Route::group(['prefix' => 'aktivitas'], function () {
        Route::get('/', 'API\AktivitasController@index');
        Route::get('/get-data-sistro', 'API\AktivitasController@getDataSistro');
        Route::get('/get-sistro', 'API\AktivitasController@getSistro');
        Route::get('/get-alat', 'API\AktivitasController@getAlat');
        Route::get('/get-kategori-alat', 'API\AktivitasController@kategoriAlat');
        Route::get('/get-gudang', 'API\AktivitasController@getGudang');
        Route::get('/get-produk', 'API\AktivitasController@getMaterial');
        Route::get('/get-pallet', 'API\AktivitasController@getPallet');
        Route::get('/get-alat-berat/{id_aktivitas}', 'API\AktivitasController@getAlatBerat')->where('id_aktivitas', '[0-9]+');
        Route::get('/get-jenis-foto', 'API\AktivitasController@getJenisFoto');
        Route::get('/get-kelayakan-foto', 'API\AktivitasController@getKelayakanFoto');
        Route::get('/get-pindah-area', 'API\AktivitasController@pindahArea');
        Route::get('/history', 'API\AktivitasController@history');
        Route::get('/get-aktivitas', 'API\AktivitasController@getAktivitas');
        Route::get('/get-yayasan', 'API\AktivitasController@getYayasan');
        Route::get('/get-tkbm', 'API\AktivitasController@getTkbm');
        
        Route::get('/list-notifikasi', 'API\AktivitasController@listNotifikasi');
        Route::get('/test-notif/{aktivitasHarian}', 'API\AktivitasController@testNotif');
        Route::get('/all-notif', 'API\AktivitasController@allNotif');
        Route::get('/read-notif', 'API\AktivitasController@readNotif');
        Route::get('/unread-notif', 'API\AktivitasController@unreadNotif');
        Route::post('/mark-as-read', 'API\AktivitasController@markAsRead');

        Route::get('/load-penerimaan/{id}', 'API\AktivitasController@loadPenerimaan')->where('id', '[0-9]+');
        Route::get('/get-area-from-pengirim/{id}', 'API\AktivitasController@getAreaFromPengirim')->where('id', '[0-9]+');
        Route::get('/get-area-from-penerima', 'API\AktivitasController@getAreaFromPenerima');
        Route::get('/get-list-tanggal/{id}', 'API\AktivitasController@listTanggalFromAreaStok')->where('id', '[0-9]+');
        Route::patch('/', 'API\AktivitasController@storePenerimaan');
        
        Route::get('/get-area/{id_aktivitas}/{id_material}/{pindah?}', 'API\AktivitasController@getArea')->where('id_aktivitas', '[0-9]+');
        Route::get('/get-area-stok/{id_aktivitas}/{id_material}/{id_area}', 'API\AktivitasController@getAreaStok')->where(['id_area' => '[0-9]+', 'id_material' => '[0-9]+','id_aktivitas' => '[0-9]+']);
        Route::get('/history/{id}', 'API\AktivitasController@detailHistory')->where('id', '[0-9]+');
        Route::get('/history/{id}/{id_material}', 'API\AktivitasController@historyMaterialArea')->where(['id' => '[0-9]+', 'id_material' => '[0-9]+']);
        Route::get('/{aktivitas}', 'API\AktivitasController@show')->where('aktivitas', '[0-9]+');
        Route::put('/{draft?}/{id?}', 'API\AktivitasController@store')->where('id', '[0-9]+')->where('draft', '[0-1]+');
        Route::post('/{aktivitas?}', 'API\AktivitasController@storePhotos')->where('aktivitas', '[0-9]+');
        Route::post('/kelayakan', 'API\AktivitasController@storeKelayakanPhotos');
        Route::put('/save-pengembalian', 'API\AktivitasController@storePengembalian');
        Route::put('/cancel/{id}', 'API\AktivitasController@cancelAktivitas')->where('id', '[0-9]+');
        
        Route::post('/test-save/{kategoriAlatBerat}', 'API\AktivitasController@testSave');
        Route::get('/isi-stok/{hapus?}', 'API\AktivitasController@isiStok');
    });
    
    Route::group(['prefix' => 'alat-berat'], function () {
        Route::get('/', 'API\AlatBeratController@index');
        Route::post('/', 'API\AlatBeratController@store')->where('laporan', '[0-9]+');
        Route::get('/get-kerusakan', 'API\AlatBeratController@getKerusakan');
        Route::get('/get-shift', 'API\AlatBeratController@getShift');
        Route::get('/history', 'API\AlatBeratController@history');
        Route::get('/history/{id}', 'API\AlatBeratController@detailHistory')->where('id', '[0-9]+');
    });

    Route::group(['prefix' => 'keluhan-operator'], function () {
        Route::get('/', 'API\KeluhanOperatorController@index');
        Route::get('/{keluhanOperator}', 'API\KeluhanOperatorController@show')->where('keluhanOperator', '[0-9]+');
        Route::put('/{keluhanOperator?}', 'API\KeluhanOperatorController@store')->where('keluhanOperator', '[0-9]+');
        Route::get('/get-operator', 'API\KeluhanOperatorController@getOperator');
        Route::get('/get-keluhan', 'API\KeluhanOperatorController@getKeluhan');
    });

    Route::group(['prefix' => 'layout'], function () {
        Route::get('/', 'API\LayoutController@index');
        Route::get('/{id_area}', 'API\LayoutController@detail')->where('id_area', '[0-9]+');
    });

    Route::group(['prefix' => 'rencana-kerja'], function () {
        Route::get('/', 'API\RencanaKerjaController@index');
        Route::get('/{id}', 'API\RencanaKerjaController@show')->where('id', '[0-9]+');
        Route::get('/get-alat-berat', 'API\RencanaKerjaController@getAlatBerat');
        Route::get('/get-shift', 'API\RencanaKerjaController@getShift');
        Route::get('/get-tkbm/{id}', 'API\RencanaKerjaController@getTkbm')->where('id', '[0-9]+');
        Route::get('/get-area/{id?}', 'API\RencanaKerjaController@getArea')->where('id', '[0-9]+');
        Route::put('/{draft?}', 'API\RencanaKerjaController@store');
        Route::patch('/{draft?}/{rencanaHarian}', 'API\RencanaKerjaController@store');
    });

    Route::group(['prefix' => 'realisasi'], function () {
        Route::get('/', 'API\RealisasiController@index');
        Route::get('/{id}', 'API\RealisasiController@show')->where('id', '[0-9]+');
        Route::get('/get-material', 'API\RealisasiController@getMaterial');
        Route::get('/get-area/{id_gudang?}', 'API\RealisasiController@getArea')->where('id_gudang', '[0-9]+');
        Route::get('/get-housekeeper/{id_rencana}', 'API\RealisasiController@getHousekeeper')->where('id_rencana', '[0-9]+');
        Route::post('/', 'API\RealisasiController@store');
    });

    Route::group(['prefix' => 'realisasi-material'], function () {
        Route::get('/', 'API\RealisasiController@getRealisasiMaterial');
        Route::get('/{realisasiMaterial}', 'API\RealisasiController@getShowRealisasiMaterial')->where('realisasiMaterial', '[0-9]+');
        Route::get('/get-material', 'API\RealisasiController@getMaterial');
        Route::put('/', 'API\RealisasiController@storeMaterial');
    });
});
