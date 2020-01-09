<?php

namespace App\Http\Controllers;

use App\Http\Models\Yayasan;
use App\Http\Requests\YayasanRequest;
use Illuminate\Http\Request;

class YayasanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['title'] = 'Master Yayasan';
        $data['menu_active'] = 'master';
        $data['sub_menu_active'] = 'yayasan';
        return view('master.master-yayasan.grid', $data);
    }

    public function json(Request $req)
    {

        $models = new Yayasan();

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
    public function store(YayasanRequest $req, $id = '')
    {
        $req->validated();

        if (!empty($id)) {
            $yayasan = Yayasan::withoutGlobalScopes()->find($id);
        } else {
            $yayasan = new Yayasan;
        }

        $yayasan->fill([
            'nama'          => $req->nama,
            'start_date'    => $req->start_date,
            'end_date'      => $req->end_date,
        ])->save();

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil disimpan';

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Yayasan $models)
    {
        if (!request()->ajax()) {
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
