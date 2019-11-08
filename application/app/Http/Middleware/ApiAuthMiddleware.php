<?php

namespace App\Http\Middleware;

use App\Http\Models\Users;
use Closure;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $idRole = '')
    {
        $responseCode = 403;
        $responseStatus = '';
        $responseMessage = '';
        $responseData = [];

        $access_token = $request->header('Authorization') ? $request->header('Authorization') : $request->input('Authorization');

        if ($access_token) {
            $auth = (new Users)->getByAccessToken(str_replace('Bearer ', '', $access_token));

            if ($auth) {
                if (!empty($idRole)) {
                    $roles = (strpos($idRole, '&') !== false) ? explode('&', $idRole) : array($idRole);

                    if (in_array($auth->role, $roles)) {
                        $responseCode = 200;
                    } else {
                        $responseCode = 403;
                        $responseMessage = 'Anda tidak dapat mengakses halaman ini, silahkan hubungi Administrator';
                    }
                } else {
                    $responseCode = 200;
                }
            } else {
                $responseCode = 401;
                $responseMessage = 'Token tidak valid';
            }
        } else {
            $responseCode = 403;
            $responseMessage = 'Anda tidak dapat mengakses halaman ini, silahkan hubungi Administrator';
        }

        if ($responseCode == 200) {
            $request->attributes->add(['my_auth' => $auth]);
            return $next($request);
        } else {
            $response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
            return response()->json($response, $responseCode);
        }
    }
}
