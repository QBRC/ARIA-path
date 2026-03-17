<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;

class Active
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::user()->is_delete == 0){
            return $next($request);
        }else{
            abort(403, 'Unactivated or deactivated account! Please contact Administrator to activate your account.');
        }
    }
}
