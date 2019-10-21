<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Sentry\State\Scope;

class SentryUserContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // if (Auth::check() && app()->bound('sentry')) {
        //     \Sentry\configureScope(function (Scope $scope): void {
        //         $scope->setUser([
        //             'id'    => Auth::user()->id,
        //             'email' => Auth::user()->email,
        //             'name'  => Auth::user()->name,
        //             'username'  => Auth::user()->username,
        //         ]);
        //     });
        // }

        return $next($request);
    }
}
