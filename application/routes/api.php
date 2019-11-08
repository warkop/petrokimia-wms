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
        Route::get('/get-area', 'API\AktivitasController@getArea');
        Route::get('/get-alat-berat', 'API\AktivitasController@getAlatBerat');
        Route::get('/get-jenis-foto', 'API\AktivitasController@getJenisFoto');
        Route::get('/{aktivitas}', 'API\AktivitasController@show')->where('aktivitas', '[0-9]+');
        Route::put('/', 'API\AktivitasController@store');
    });
    
    Route::group(['prefix' => 'alat-berat'], function () {
        Route::get('/', 'API\AlatBeratController@index');
        Route::post('/', 'API\AlatBeratController@store');
        Route::get('/history', 'API\AlatBeratController@history');
        Route::get('/history/{id}', 'API\AlatBeratController@detailHistory')->where('id', '[0-9]+');
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