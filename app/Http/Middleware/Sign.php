<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class Sign
{
    public function handle($request, Closure $next)
    {
        $dev = $request->input('device');
        $sign = $request->input('sign');
        if(!$dev || !$sign){
            return response()->json(['errcode'=>1001,'message'=>'参数错误！']);
        }
        $token = Redis::hget('token',$dev);
        if(!$token || strlen($token) != 32){
            return response()->json(['errcode'=>2001,'message'=>'请重新登录']);
        }
        //计算签名
        $data = $request->except('sign'); // 获取数据，排除签名
        $data['token'] = $token; // 加入token
        ksort($data); // 按照key排序
        $correctSign = md5(json_encode($data)); // json序列化并加密
        //判断签名
        if($sign != $correctSign){
            return response()->json(['errcode'=>1002,'message'=>'数字签名错误！']);
        }
        Redis::hget('token',$dev,$token,20*60);

        return $next($request);
    }
}
