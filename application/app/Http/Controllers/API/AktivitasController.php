<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Aktivitas;
use App\Http\Resources\AktivitasResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

class AktivitasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return AktivitasResource::collection(Aktivitas::paginate(10))->additional([
            'status' => ['message' => '',
            'code' => Response::HTTP_OK],
        ], Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
