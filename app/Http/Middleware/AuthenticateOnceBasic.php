<?php

namespace App\Http\Middleware;

use Config;
use Auth;
use Closure;

class AuthenticateOnceBasic
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
        Config::set('session.driver', 'array');
        $authenticated = Auth::guard('api')->onceBasic('username');        
        Config::set('session.driver', 'database');        

        if($authenticated === null){
            return $next($request);
        }
    }

}

