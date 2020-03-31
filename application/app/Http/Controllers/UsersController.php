<?php

namespace App\Http\Controllers;

use App\Http\Models\Gudang;
use App\Http\Models\Karu;
use App\Http\Models\Users;
use App\Http\Models\Role;
use App\Http\Models\TenagaKerjaNonOrganik;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    public function index()
    {
        $data['title'] = 'Master Users';
        $data['menu_active'] = 'master';
        $data['sub_menu_active'] = 'users';
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

    public function store(UserRequest $req, Users $models)
    {
        $req->validated($models);
        $role = $req->input('role_id');
        $id   = $req->input('id');

        $nama       = $req->input('nama');
        $username   = $req->input('username');
        $email      = $req->input('email');
        $pilih      = $req->input('pilih');
        $end_date   = $req->input('end_date');

        if (!empty($id)) {
            $models = Users::withoutGlobalScopes()->find($id);
        } else {
            $password               = $req->input('password');
            $models->password       = bcrypt($password);
        }

        if ($role == 1) {
            $models->id_tkbm    = null;
            $models->id_karu    = null;    
        } else if ($role == 5) {
            $models->id_karu    = $pilih;    
            $models->id_tkbm    = null;    
        } else {
            $models->id_tkbm    = $pilih;
            $models->id_karu    = null;    
        }
        
        $models->role_id        = $role;
        $models->name           = $nama;
        $models->username       = $username;
        $models->email          = $email;
        $models->end_date       = $end_date;

        $models->save();

        $this->responseCode = 200;
        $this->responseMessage = 'Data berhasil disimpan';

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function loadPegawai($kategori)
    {
        $peg = '';
        
        switch ($kategori) {
            case 5:
                $peg = Karu::all();
                break;
            case 6:
                $peg = Gudang::gp()->get();
                break;
            default:
                $peg = TenagaKerjaNonOrganik::where('job_desk_id', $kategori)->get();
                break;
        }

        $this->responseCode     = 200;
        $this->responseData     = $peg;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function show($id, Users $models, Request $request)
    {
        if (!$request->ajax()) {
            return $this->accessForbidden();
        } else {
            $res = $models::withoutGlobalScopes()->find($id);

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

    public function resetPassword($id_user)
    {
        $res = Users::withoutGlobalScopes()->find($id_user);
        // dump($res);
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

    public function changePassword(ChangePasswordRequest $req, $id)
    {
        $req->validated();

        $new_password   = $req->new_password;
        $old_password   = $req->old_password;

        $res = Users::withoutGlobalScopes()->find($id);
        if(auth()->user()->id == $id){
            if (Hash::check($old_password, $res->password)) {
                $res->password = Hash::make($new_password);
                $res->save();
            } else {
                $this->responseCode = 403;
                $this->responseMessage = 'Password lama yang Anda masukkan salah!';
                // $this->responseData = $res;

                $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
                return response()->json($response, $this->responseCode);
            }
        } else {
            $this->responseCode = 403;
            $this->responseMessage = 'Aksi yang Anda lakukan dilarang oleh sistem!<br>';
            $this->responseMessage .= 'Silahkan hubungi administrator untuk mengetahui info lebih lanjut!';
            // $this->responseData = $res;

            $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
            return response()->json($response, $this->responseCode);
        }


        $this->responseCode = 200;
        $this->responseMessage = 'Password berhasil diubah';
        $this->responseData = $res;

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
        return response()->json($response, $this->responseCode);
    }

    public function destroy(Users $users)
    {
        //
    }
}
