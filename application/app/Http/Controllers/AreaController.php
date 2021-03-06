<?php

namespace App\Http\Controllers;

use App\Http\Models\Area;
use App\Http\Models\Gudang;
use App\Http\Requests\AreaRequest;
use App\Scopes\EndDateScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AreaController extends Controller
{
    public function index($id_gudang)
    {
        $data['title'] = 'Area';
        $data['id_gudang'] = $id_gudang;
        $models = Gudang::find($id_gudang);
        $data['nama_gudang'] = $models->nama;
        return view('list-area.grid', $data);
    }

    public function json(Request $req, $id_gudang)
    {
        $models = new Area();

        $numbcol = $req->get('order');
        $columns = $req->get('columns');

        $echo    = $req->get('draw');
        $start   = $req->get('start');
        $perpage = $req->get('length');

        $search  = $req->get('search');
        $search  = $search['value'];
        $pattern = '/[^a-zA-Z0-9 !@#$%^&*\/\.\,\(\)-_:;?\+=]/u';
        $search  = preg_replace($pattern, '', $search);

        $sort = $numbcol[0]['dir'];
        $field = $columns[$numbcol[0]['column']]['data'];

        $condition = '';

        $page = ($start / $perpage) + 1;

        if ($page >= 0) {
            $result = $models->jsonGrid($start, $perpage, $search, false, $sort, $field, $condition, $id_gudang);
            $total  = $models->jsonGrid($start, $perpage, $search, true, $sort, $field, $condition, $id_gudang);
        } else {
            $result = $models::orderBy($field, $sort)->get();
            $total  = $models::all()->count();
        }
        $this->responseCode = 200;
        $this->responseData = array("sEcho" => $echo, "iTotalRecords" => $total, "iTotalDisplayRecords" => $total, "aaData" => $result);

        return response()->json($this->responseData, $this->responseCode);
    }

    public function store(AreaRequest $req, Area $models, $id_gudang)
    {
        $req->validated();

        $action = $req->input('action');
        if ($action == 'edit') {
            $rules['id'] = 'required';
        }

        $id = $req->input('id');

        if (!empty($id)) {
            $models = Area::find($id);
            $models->updated_by = auth()->id();
        } else {
            $models->created_by = auth()->id();
        }

        $models->id_gudang      = $id_gudang;
        $models->nama           = strip_tags($req->input('nama'));
        $models->kapasitas      = strip_tags($req->input('kapasitas'));
        $models->tipe           = strip_tags($req->input('tipe'));
        if ($req->input('tipe') == 2) {
            $models->range          = strip_tags($req->input('range'));
        }
        $models->start_date     = date('Y-m-d');

        $saved = $models->save();
        if (!$saved) {
            $this->responseCode     = 502;
            $this->responseMessage  = 'Data gagal disimpan!';
        } else {
            $this->responseCode = 200;
            $this->responseMessage = 'Data berhasil disimpan';
        }

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function show($id_gudang, $id, Area $models, Request $request)
    {
        if (!$request->ajax()) {
            return $this->accessForbidden();
        } else {
            $res = $models::withoutGlobalScope(EndDateScope::class)->find($id);

            if (!empty($res)) {
                $this->responseCode = 200;
                $this->responseMessage = 'Data tersedia.';
                $this->responseData = $res;
            } else {
                $this->responseData = [];
                $this->responseStatus = 'No Data Available';
                $this->responseMessage = 'Data tidak tersedia';
            }

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function edit(Area $area)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Area $area)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_gudang,$id)
    {
        // Area::destroy($area->id);
        $area = Area::find($id);
        $area->end_date = date('Y-m-d');
        $area->save();
        $res = Area::where('id', $area->id)->where('end_date', null)->first();
        if (!empty($res)) {
            $this->responseCode = 500;
            $this->responseMessage = 'Data gagal dihapus';
            $this->responseData = [];
        } else {
            $this->responseData = [];
            $this->responseCode = 200;
            $this->responseStatus = 'No Data Available';
            $this->responseMessage = 'Data berhasil dihapus';
        }

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }
}
