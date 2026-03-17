<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;

class Permissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  $type: 1 for image id from slide.show, 2 for image id from annotation
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $type)
    {
        if (Auth::user()->role == 1 || Auth::user()->role == 2){
            return $next($request);
        }elseif (Auth::user()->role == 3){
            $id='';
            if ($type == 1){
                $id=$request->route('image');
            }elseif ($type == 2){
                $id=$request->input('id');
            }

            if (in_array($id, Auth::user()->imagesID())){
                return $next($request);
            }else{
                abort(403, 'Unauthorized access! Please contact Administrator to grant you access permission.');
            }

        }else{
            abort(403, 'Unauthorized action! Please contact Administrator to grant you access permission.');
        }



    }
}
