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
        return $next($request);
    }
}
