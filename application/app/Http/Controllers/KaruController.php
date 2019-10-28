<?php

namespace App\Http\Controllers;

use App\Http\Models\Karu;
use App\Http\Requests\KaruRequest;
use App\Scopes\EndDateScope;
use Illuminate\Http\Request;

class KaruController extends Controller
{
    public function index()
    {
        $data['title'] = 'Master Kepala Regu';
        $data['menu_active'] = 'master';
        $data['sub_menu_active'] = 'kepala regu';
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
            $result = $models->jsonGrid($start, $perpage, $search, false, $sort, $field, $condition);
            $total  = $models->jsonGrid($start, $perpage, $search, true, $sort, $field, $condition);
        } else {
            $result = $models::orderBy($field, $sort)->get();
            $total  = $models::all()->count();
        }
        $this->responseCode = 200;
        $this->responseData = array("sEcho" => $echo, "iTotalRecords" => $total, "iTotalDisplayRecords" => $total, "aaData" => $result);

        return response()->json($this->responseData, $this->responseCode);
    }

    public function store(KaruRequest $req, Karu $karu)
    {
        $req->validated();

        $karu->nama                   = $req->input('nama');
        $karu->nik                    = $req->input('nik');
        $karu->no_hp                  = $req->input('no_hp');
        $karu->start_date             = $req->input('start_date');
        $karu->end_date               = $req->input('end_date');

        $karu->save();

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
            $this->authorize('update', $res);

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
