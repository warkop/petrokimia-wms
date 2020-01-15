<?php

namespace App\Http\Controllers;

use App\Http\Models\Gudang;
use App\Http\Models\Karu;
use App\Http\Requests\KaruRequest;
use App\Scopes\EndDateScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KaruController extends Controller
{
    public function index()
    {
        $data['title'] = 'Master Kepala Regu';
        $data['menu_active'] = 'master';
        $data['sub_menu_active'] = 'kepala regu';
        $data['gudang'] = Gudang::internal()->get();
        return view('master.master-karu.grid', $data);
    }

    public function json(Request $req)
    {
        $models = new Karu();

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
            $temp = $models->jsonGrid($start, $perpage, $search, $sort, $field, $condition);
            $result = $temp['result'];
            $total  = $temp['count'];
        } else {
            $temp = $models::orderBy($field, $sort)->get();
            $result = $temp;
            $total  = $temp->count();
        }
        $this->responseCode = 200;
        $this->responseData = array("sEcho" => $echo, "iTotalRecords" => $total, "iTotalDisplayRecords" => $total, "aaData" => $result);

        return response()->json($this->responseData, $this->responseCode);
    }

    public function store(KaruRequest $req, $id='')
    {
        $req->validated();

        if (!empty($id)) {
            $karu = Karu::withoutGlobalScopes()->find($id);
        } else {
            $karu = new Karu;
        }

        $karu->fill([
            'nama'      => $req->nama,
            'nik'       => $req->nik,
            'no_hp'     => $req->no_hp,
            'id_gudang' => $req->gudang,
            'start_date'=> $req->start_date,
            'end_date'  => $req->end_date,
        ])->save();

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil disimpan';

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Http\Models\Karu  $karu
     * @return \Illuminate\Http\Response
     */
    public function show($id, Karu $models, Request $request)
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

    public function getGudang(Request $req)
    {
        $search = $req->input('term');
        $pattern = '/[^a-zA-Z0-9 !@#$%^&*\/\.\,\(\)-_:;?\+=]/u';
        $search = preg_replace($pattern, '', $search);

        $res = Gudang::internal()
        ->where(DB::raw('LOWER(nama)'), 'LIKE', "%" . strtolower($search) . "%")
        ->get();

        if (!empty($res)) {
            $this->responseCode = 200;
            $this->responseMessage = 'Data tersedia.';
            $this->responseData = $res;
        } else {
            $this->responseData = [];
            $this->responseStatus = 'No Data Available';
            $this->responseMessage = 'Data Karu tidak tersedia';
        }

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Http\Models\Karu  $karu
     * @return \Illuminate\Http\Response
     */
    public function edit(Karu $karu)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Models\Karu  $karu
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Karu $karu)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Http\Models\Karu  $karu
     * @return \Illuminate\Http\Response
     */
    public function destroy(Karu $karu)
    {
        //
    }
}
