<?php

namespace App\Http\Controllers;

use App\Http\Models\Users;
use App\Http\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    private $responseCode = 403;
    private $responseStatus = '';
    private $responseMessage = '';
    private $responseData = [];

    public function index()
    {
        $data['role'] = Role::all();
        return view('master.master-user.grid', $data);
    }

    public function create()
    {
        //
    }

    public function json(Request $req)
    {
        $models = new Users();

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

    public function store(Request $req, Users $models)
    {
        $role = $req->input('role_id');
        $id   = $req->input('id');

        $rules = [
            'username'      => [
                'required',
                Rule::unique('users', 'username')->ignore($id, 'id')
            ],
            'email'         => 'email',
            'role_id'       => [
                'required',
                // Rule::exists('role')->where(function ($query) use ($role) {
                //     $query->where('id',  $role);
                // })
            ],
            'start_date'    => 'nullable|date_format:d-m-Y',
            'end_date'      => 'nullable|date_format:d-m-Y|after:start_date',
        ];

        $action = $req->input('action');
        if ($action == 'edit') {
            $rules['id'] = 'required';
        }

        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            $this->responseCode                 = 400;
            $this->responseStatus               = 'Missing Param';
            $this->responseMessage              = 'Silahkan isi form dengan benar terlebih dahulu';
            $this->responseData['error_log']    = $validator->errors();
        } else {
            $username   = $req->input('username');
            $email      = $req->input('email');
            $password   = $req->input('password');

            if (!empty($id)) {
                $models = Users::find($id);
                $models->updated_by = session('userdata')['id_user'];
            } else {
                $models->created_by = session('userdata')['id_user'];
            }

            $start_date  = null;
            if ($req->input('start_date') != '') {
                $start_date  = date('Y-m-d', strtotime($req->input('start_date')));
            }

            $end_date   = null;
            if ($req->input('end_date') != '') {
                $end_date   = date('Y-m-d', strtotime($req->input('end_date')));
            }

            

            $models->role_id        = strip_tags($role);
            $models->username       = strip_tags($username);
            $models->email          = strip_tags($email);
            $models->password       = strip_tags(bcrypt($password));
            $models->start_date     = $start_date;
            $models->end_date       = $end_date;

            $models->save();

            $this->responseCode = 200;
            $this->responseMessage = 'Data berhasil disimpan';
        }

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function show($id, Users $models, Request $request)
    {
        if (!$request->ajax()) {
            return $this->accessForbidden();
        } else {
            $res = $models::find($id);

            if (!empty($res)) {
                $this->responseCode = 200;
                $this->responseMessage = 'Data tersedia.';
                $this->responseData = $res;
            } else {
                $this->responseData = [];
                $this->responseCode = 500;
                $this->responseStatus = 'No Data Available';
                $this->responseMessage = 'Data tidak tersedia';
            }

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        }
    }

    public function edit(Users $users)
    {
        //
    }

    public function update(Request $request, Users $users)
    {
        //
    }

    public function changePassword($id_user)
    {
        $res = Users::find($id_user);
        $res->password = bcrypt('petrokimia123');

        $saved = $res->save();

        if (!$saved) {
            $this->responseData = [];
            $this->responseCode = 500;
            $this->responseStatus = 'No Data Available';
            $this->responseMessage = 'Data tidak tersedia';
        } else {
            $this->responseCode = 200;
            $this->responseMessage = 'Password berhasil direset';
            $this->responseData = $res;
        }


        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function destroy(Users $users)
    {
        //
    }
}
