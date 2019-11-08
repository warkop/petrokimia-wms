<?php

namespace App\Http\Controllers\API;

use App\Http\Models\Users;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
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
            $username = $request->input('username');
            $password = $request->input('password');
            $device_id = $request->input('device');

            $cek_user = Users::where('username', $username)->endDate()->first();

            if ($cek_user) {
                if (Hash::check($password, $cek_user['password'])) {
                    
                    // if () {

                    // } else {

                    // }
                    $m_user = Users::find($cek_user['id']);

                    if (empty($cek_user['api_token'])) {
                        $access_token = 'wMs-' . rand_str(10) . date('Y') . rand_str(6) . date('m') . rand_str(6) . date('d') . rand_str(6) . date('H') . rand_str(6) . date('i') . rand_str(6) . date('s');

                        $m_user->api_token = $access_token;
                    } else {
                        $access_token = $cek_user['api_token'];
                    }

                    $m_user->device = $device_id;
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

                    $this->responseCode = 200;
                    $this->responseData = $arr;                    
                    $this->responseMessage = 'Anda berhasil login';
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
        $responseCode = 403;
        $responseStatus = '';
        $responseMessage = '';
        $responseData = [];

        $this->validate($req, [
            'token' => 'required',
        ]);

        $user = new Users;
        $data = $user->where([['api_token', '=', $req->get('token')]])->first();
        if (is_null($data)) {
            $responseCode = 400;
            $responseMessage = 'User tidak ditemukan.';
        } else {
            $user->where('id', $data['id'])->update(['api_token' => null]);
            $responseCode = 200;
            $responseMessage = 'Berhasil melakukan logout.';
        }
        $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
        return response()->json($response, $responseCode);
    }
}
