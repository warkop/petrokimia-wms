<?php

use App\Http\Models\Aktivitas;
use App\Http\Resources\AktivitasResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

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

Route::post('/login', 'API\AuthController@authenticate');
Route::post('/logout', 'API\AuthController@logout');

Route::group(['middleware' => 'api.auth'], function () {
    Route::group(['prefix' => 'aktivitas'], function () {
        Route::get('/', 'API\AktivitasController@index');
        Route::get('/get-gudang', 'API\AktivitasController@getGudang');
        Route::get('/get-produk', 'API\AktivitasController@getMaterial');
        Route::get('/get-pallet', 'API\AktivitasController@getPallet');
        Route::get('/get-alat-berat', 'API\AktivitasController@getAlatBerat');
        Route::get('/get-jenis-foto', 'API\AktivitasController@getJenisFoto');
        Route::get('/get-area-stok', 'API\AktivitasController@areaStok');
        Route::get('/history', 'API\AktivitasController@history');
        Route::get('/get-area/{id_aktivitas}', 'API\AktivitasController@getArea')->where('id_aktivitas', '[0-9]+');
        Route::get('/get-area-stok/{id_aktivitas}/{id_area}', 'API\AktivitasController@getAreaStok')->where(['id_area' => '[0-9]+', 'id_aktivitas' => '[0-9]+']);
        Route::get('/history/{id}', 'API\AktivitasController@detailHistory')->where('id', '[0-9]+');
        Route::get('/history/{id}/{id_material}', 'API\AktivitasController@historyMaterialArea')->where(['id' => '[0-9]+', 'id_material' => '[0-9]+']);
        Route::get('/{aktivitas}', 'API\AktivitasController@show')->where('aktivitas', '[0-9]+');
        Route::post('/{aktivitas?}', 'API\AktivitasController@store')->where('aktivitas', '[0-9]+');
        Route::patch('/{aktivitas}', 'API\AktivitasController@approve')->where('aktivitas', '[0-9]+');
    });
    
    Route::group(['prefix' => 'alat-berat'], function () {
        Route::get('/', 'API\AlatBeratController@index');
        Route::post('/{laporan?}', 'API\AlatBeratController@store')->where('laporan', '[0-9]+');
        Route::get('/get-kerusakan', 'API\AlatBeratController@getKerusakan');
        Route::get('/get-shift', 'API\AlatBeratController@getShift');
        Route::get('/history', 'API\AlatBeratController@history');
        Route::get('/history/{id}', 'API\AlatBeratController@detailHistory')->where('id', '[0-9]+');
    });

    Route::group(['prefix' => 'layout'], function () {
        Route::get('/', 'API\LayoutController@index');
        Route::get('/{id_area}', 'API\LayoutController@detail')->where('id_area', '[0-9]+');
    });
});


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::get('aktivitas', function () {
//     return (new AktivitasResource(Aktivitas::paginate()))->response()->getStatusCode();
// });

// Route::get('aktivitas/{id}', function ($id) {
//     // $obj = Aktivitas::find($id);
//     // if (!empty($obj)) {
//         // return new AktivitasResource($aktivitas);
//     // } else {
//     //     return (new AktivitasResource(new Aktivitas))->response()->getStatusCode();
//     // }
// });