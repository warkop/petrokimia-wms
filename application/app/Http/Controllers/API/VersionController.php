<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Models\Version;

class VersionController extends Controller
{
    public function index()
    {
        $this->responseData = Version::orderBy('id', 'desc')->first();

        $this->responseCode = 200;
        $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
        return response()->json($response, $this->responseCode);
    }

    public function store(Request $request)
    {
        $version_code = $request->input('version_code');

        $version = new Version;
        $version->version_code = $version_code;
        $version->save();

        $this->responseData = $version_code;
        $this->responseCode = 200;
        $this->responseMessage = 'Versi berhasil ditambahkan!';

        $response = ['data' => $this->responseData, 'status' => ['message' => $this->responseMessage, 'code' => $this->responseCode]];
        return response()->json($response, $this->responseCode);
    }
}
