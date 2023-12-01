<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IsVisitor {
    
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard('web')->check()) {
            return redirect('/home');
        }

        return $next($request);
    }
    
}
