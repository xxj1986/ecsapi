<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class Auth
{
    /**
     * 注册登陆认证参数判断
     */
    public function handle($request, Closure $next)
    {
        $dev = $request->input('device');
        if(!$dev){
            return response()->json(['errcode'=>1001,'message'=>'参数错误！']);
        }

        return $next($request);
    }
}
