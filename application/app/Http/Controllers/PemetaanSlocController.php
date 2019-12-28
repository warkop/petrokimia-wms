<?php

namespace App\Http\Controllers;

use App\Http\Models\Gudang;
use App\Http\Models\PemetaanSloc;
use App\Http\Requests\PemetaanSlocRequest;
use Illuminate\Http\Request;

class PemetaanSlocController extends Controller
{
    public function index()
    {
        return view('master.master-pemetaan-sloc.grid');
    }

    public function json(Request $req)
    {
        $models = new PemetaanSloc();

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

    public function store(PemetaanSlocRequest $req,PemetaanSloc $pemetaanSloc)
    {
        $req->validated();

        $pemetaanSloc->nama = $req->nama;
        $pemetaanSloc->nama = $req->nama;
        $pemetaanSloc->nama = $req->nama;
    }

    public function loadSloc()
    {
        $data = Gudang::distinct()->whereNotNull('id_sloc')->orderBy('id_sloc', 'asc')->get();

        return response()->json($data, 200);
    }
}
