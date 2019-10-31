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
Route::get('aktivitas', 'API\AktivitasController@index');
Route::get('aktivitas/{aktivitas}', 'API\AktivitasController@show');

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