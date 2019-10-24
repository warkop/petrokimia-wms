<?php

use App\Http\Models\Aktivitas;
use App\Http\Resources\AktivitasResource;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::get('aktivitas', function () {
//     return (new AktivitasResource(Aktivitas::paginate()))->response()->setStatusCode(200);
// });
Route::get('aktivitas', 'API\AktivitasController@index');

// Route::get('aktivitas/{id}', function ($id) {
//     $obj = Aktivitas::find($id);
//     if (!empty($obj)) {
//         return (new AktivitasResource(Aktivitas::find($id)))->response()->setStatusCode(200);
//     } else {
//         return (new AktivitasResource(new Aktivitas))->response()->setStatusCode(400);
//     }
// });
Route::get('aktivitas/{aktivitas}', 'API\AktivitasController@show');