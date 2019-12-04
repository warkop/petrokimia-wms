<?php

namespace App\Http\Controllers;

use App\Http\Models\LogActivity;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $responseCode = 403;
    protected $responseStatus = '';
    protected $responseMessage = '';
    protected $responseData = [];

    protected function writeLog($modul, $action, $message)
    {
        $arr = [
            'modul'         => $modul,
            'action'        => $action,
            'aktivitas'     => $message,
            'created_at'    => now(),
            'created_by'    => \Auth::id(),
        ];
        (new LogActivity)->create($arr);
    }
}
