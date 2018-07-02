<?php

namespace App\Http\Middleware;

use Closure;

class UserFilter
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
        var_dump('ccvv');die;
        return $next($request);
    }



}
