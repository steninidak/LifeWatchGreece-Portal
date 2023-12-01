<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\GuiUser;

class UpdateUserActivity {
    
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard('web')->check()) {
            // Log current time for last user's activity (we do the same in SessionController)
            $user = GuiUser::where('id',Auth::guard('web')->id())->first();
            $user->last_activity = time();
            $user->save();
        }

        return $next($request);
    }
    
}