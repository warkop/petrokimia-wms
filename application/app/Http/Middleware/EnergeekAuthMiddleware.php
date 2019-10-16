<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;

class EnergeekAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $idRole='')
    {
        if(Auth::check()){
            $session = Auth::user();

            if(!empty($idRole)){
                $roles = (strpos($idRole, '&') !== false)? explode('&', $idRole) : array($idRole);

                if(in_array($session->usr_role, $roles)){
                    return $next($request);
                }

                $alerts[] = array('error', 'Anda tidak dapat mengakses halaman ini, silahkan hubungi Administrator', 'Peringatan!');
                session()->flash('alerts', $alerts);
                return $request->expectsJson() ? response()->json(helpResponse(403, [], 'Anda tidak dapat mengakses halaman ini, silahkan hubungi Administrator'), 403) : redirect()->to(route('/'));
            }else{
                return $next($request);
            }

        }else{
            return $request->expectsJson() ? response()->json(helpResponse(401), 401) : redirect()->to(route('login').'?source='.url()->current());
        }
    }
}
