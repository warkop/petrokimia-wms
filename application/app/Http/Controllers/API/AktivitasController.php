<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Aktivitas;
use App\Http\Models\Area;
use App\Http\Models\Gudang;
use App\Http\Models\Material;
use App\Http\Resources\AktivitasResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

class AktivitasController extends Controller
{
    public function index(Request $req)
    {
        $search = strip_tags($req->input('search'));

        $obj =  AktivitasResource::collection(Aktivitas::paginate(10))->additional([
            'status' => ['message' => '',
            'code' => Response::HTTP_OK],
        ], Response::HTTP_OK);

        // dump(Aktivitas::search($search)->get());

        return $obj;
    }

    public function getMaterial()
    {
        $resource = Material::produk()->get();
        return (new AktivitasResource($resource))->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK,
            ]
        ], Response::HTTP_OK);
    }
    
    public function getPallet()
    {
        $resource = Material::pallet()->get();
        return (new AktivitasResource($resource))->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK,
            ]
        ], Response::HTTP_OK);
    }

    public function getGudang()
    {
        $resource = Gudang::get();
        return (new AktivitasResource($resource))->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK,
            ]
        ], Response::HTTP_OK);
    }

    public function getArea()
    {
        $resource = Area::get();
        return (new AktivitasResource($resource))->additional([
            'status' => [
                'message' => '',
                'code' => Response::HTTP_OK,
            ]
        ], Response::HTTP_OK);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        try {
            $aktivitas = Aktivitas::findOrFail($id);
            return (new AktivitasResource($aktivitas))->additional([
                'status' => [
                    'message' => '',
                    'code' => Response::HTTP_OK,
                ]
            ], Response::HTTP_OK);
        } catch (ModelNotFoundException $ex) {
            return response()->json([
                'data' => null,
                'status' => [
                    'message' => 'Data tidak ditemukan!',
                    'code' => Response::HTTP_BAD_REQUEST
                ]
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
