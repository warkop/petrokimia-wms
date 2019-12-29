<?php

namespace App\Http\Controllers\API;

use App\Http\Models\Users;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Gudang;
use App\Http\Models\Realisasi;
use App\Http\Models\RencanaHarian;
use App\Http\Models\RencanaTkbm;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function clean($user)
    {
        $user->user_gcid = null;
        $user->save();
    }

    public function username()
    {
        return 'username';
    }

    public function authenticate(Request $request)
    {
        $rules['username'] = 'required';
        $rules['password'] = 'required';

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $this->responseCode = 400;
            $this->responseStatus = 'Missing Param';
            $this->responseMessage = 'Silahkan isi form dengan benar terlebih dahulu';
            $this->responseData['error_log'] = $validator->errors();
        } else {
            $username   = $request->input('username');
            $password   = $request->input('password');
            $device_id  = $request->input('device');
            $user_gcid  = $request->input('user_gcid');

            $cek_user = Users::where('username', $username)->endDate()->first();

            if ($cek_user) {
                if (Hash::check($password, $cek_user['password'])) {
                    if ($cek_user['role_id'] == 3) {
                        $rencanaTkbm = RencanaTkbm::where('id_tkbm', $cek_user['id_tkbm'])->get();
                        if (!$rencanaTkbm->isEmpty()) {
                            $m_user = Users::withoutGlobalScopes()->find($cek_user['id']);

                            if (empty($cek_user['api_token'])) {
                                $access_token = 'wMs-' . rand_str(10) . date('Y') . rand_str(6) . date('m') . rand_str(6) . date('d') . rand_str(6) . date('H') . rand_str(6) . date('i') . rand_str(6) . date('s');

                                $m_user->api_token = $access_token;
                            } else {
                                $access_token = $cek_user['api_token'];
                            }

                            $m_user->device = $device_id;
                            $m_user->user_gcid = $user_gcid;
                            $m_user->save();

                            $arr = [
                                'access_token'  => $access_token,
                                'role'          => $m_user->role_id,
                                'name'          => $m_user->name,
                                'username'      => $m_user->username,
                                'email'         => $m_user->email,
                                'id_tkbm'       => $m_user->id_tkbm,
                                'id_karu'       => $m_user->id_karu,
                                'user_gcid'     => $m_user->user_gcid,
                            ];
                            if ($m_user->id_tkbm != null) {
                                $rencanaTkbm        = RencanaTkbm::where('id_tkbm', $m_user->id_tkbm)->orderBy('id_rencana', 'desc')->first();
                                $rencanaHarian      = RencanaHarian::findOrFail($rencanaTkbm->id_rencana);

                                $realisasi = Realisasi::where('id_rencana', $rencanaHarian->id)->orderBy('id', 'desc')->first();
                                if (!empty($realisasi)) {
                                    $this->clean($m_user);

                                    $this->responseCode = 403;
                                    $this->responseMessage = 'Rencana harian dari checker ini sudah ter-realisasi!';

                                    $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
                                    return response()->json($response, $this->responseCode);
                                }

                                if (date('Y-m-d H:i:s', strtotime($rencanaHarian->end_date)) < date('Y-m-d H:i:s')) {
                                    $this->clean($m_user);

                                    $this->responseCode = 403;
                                    $this->responseMessage = 'Rencana harian dari checker ini sudah kadaluarsa! Silahkan buat Rencana Harian lagi untuk checker ini!';
                                    
                                    $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
                                    return response()->json($response, $this->responseCode);
                                }
                                $gudang             = Gudang::findOrFail($rencanaHarian->id_gudang);
                                
                                $arr['id_gudang'] = $gudang->id;
                                $arr['nama_gudang'] = $gudang->nama;
                            }

                            $this->responseCode = 200;
                            $this->responseData = $arr;
                            $this->responseMessage = 'Anda berhasil login';
                        } else {
                            $this->clean($m_user);
                            $this->responseCode = 403;
                            $this->responseMessage = 'Checker tidak didaftarkan pada rencana harian!';

                        }
                    } else {
                        $m_user = Users::withoutGlobalScopes()->find($cek_user['id']);

                        if (empty($cek_user['api_token'])) {
                            $access_token = 'wMs-' . rand_str(10) . date('Y') . rand_str(6) . date('m') . rand_str(6) . date('d') . rand_str(6) . date('H') . rand_str(6) . date('i') . rand_str(6) . date('s');

                            $m_user->api_token = $access_token;
                        } else {
                            $access_token = $cek_user['api_token'];
                        }

                        $m_user->device = $device_id;
                        $m_user->user_gcid = $user_gcid;
                        $m_user->save();
                        

                        $arr = [
                            'access_token'  => $access_token,
                            'role'          => $m_user->role_id,
                            'name'          => $m_user->name,
                            'username'      => $m_user->username,
                            'email'         => $m_user->email,
                            'id_tkbm'       => $m_user->id_tkbm,
                            'id_karu'       => $m_user->id_karu,
                            'gcid'          => $m_user->user_gcid,
                        ];

                        $arr['nama_gudang'] = '';
                        if ($m_user->id_karu != null) {
                            $gudang = Gudang::where('id_karu', $m_user->id_karu)->first();
                            $arr['nama_gudang'] = $gudang->nama;
                        }

                        $this->responseCode = 200;
                        $this->responseData = $arr;
                        $this->responseMessage = 'Anda berhasil login';
                    }
                } else {
                    $this->responseCode = 401;
                    $this->responseMessage = 'Username atau Password Anda salah';
                }
            } else {
                $this->responseCode = 400;
                $this->responseMessage = 'Username tidak ditemukan!';
            }
        }

        $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
        return response()->json($response, $this->responseCode);
    }

    public function logout(Request $req)
    {
        $this->validate($req, [
            'token' => 'required',
        ]);

        $user = new Users;
        $data = $user->where('api_token', $req->input('token'))->first();
        if (is_null($data)) {
            $this->responseCode = 400;
            $this->responseMessage = 'User tidak ditemukan.';
        } else {
            $user->where('id', $data['id'])->update(['api_token' => null, 'user_gcid' => null]);
            $this->responseCode = 200;
            $this->responseMessage = 'Berhasil melakukan logout.';
        }
        $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
        return response()->json($response, $this->responseCode);
    }
}
