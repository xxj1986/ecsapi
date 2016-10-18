<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class Auth
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
        dd($request);
        $dev = $request->input('device');
        if(!$dev){
            return response()->json(['errcode'=>1001,'message'=>'参数错误！']);
        }

        return $next($request);
    }
}
