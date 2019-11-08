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
                    $m_user = Users::find($cek_user['id']);

                    if (empty($cek_user['api_token'])) {
                        $access_token = 'wMs-' . rand_str(10) . date('Y') . rand_str(6) . date('m') . rand_str(6) . date('d') . rand_str(6) . date('H') . rand_str(6) . date('i') . rand_str(6) . date('s');

                        $m_user->api_token = $access_token;
                    } else {
                        $access_token = $cek_user['api_token'];
                    }

                    $m_user->device = $device_id;
                    $m_user->save();

                    $this->responseCode = 200;
                    $this->responseData['access_token'] = $access_token;
                    $this->responseData['role'] = $m_user->role_id;
                    $this->responseData['name'] = $m_user->name;
                    $this->responseData['username'] = $m_user->username;
                    $this->responseData['email'] = $m_user->email;
                    $this->responseData['id_tkbm'] = $m_user->id_tkbm;
                    $this->responseData['id_karu'] = $m_user->id_karu;
                    $this->responseData['gcid'] = $m_user->user_gcid;
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

        $response = helpResponse($this->responseCode, $this->responseData, $this->responseMessage, $this->responseStatus);
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
        $response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
        return response()->json($response, $responseCode);
    }
}
