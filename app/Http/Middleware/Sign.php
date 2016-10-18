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

        /*
         * App登录互斥
         * 说明：如果传输了user_id,一个账户只能在一个设备登录
         * 如果不传user_id，那么可以多个设备同时登录账户
         */
        $user_id = intval($request->input('user_id'));
        if($user_id){
            $onlineDev = Redis::hget('device',$user_id);
            if($dev !== $onlineDev){
                return response()->json(['errcode'=>1,'message'=>'您已在其他设备登录']);
            }
        }

        if(!$dev || !$sign){
            return response()->json(['errcode'=>1001,'message'=>'参数错误！']);
        }
        //判断是否已经登陆或超时退出
        $token = Redis::hget('token',$dev);
        if(!$token || strlen($token) != 32){
            return response()->json(['errcode'=>2001,'message'=>'请重新登录']);
        }
        //计算签名
        $data = $request->except('sign'); // 获取数据，排除签名
        $data['token'] = $token; // 加入token
        ksort($data); // 按照key升序排序
        $correctSign = md5(json_encode($data)); // json序列化，并md5哈希
        //判断签名
        if($sign != $correctSign){
            return response()->json(['errcode'=>1002,'message'=>'数字签名错误！']);
        }
        //更新过期时间
        Redis::expire('token'.$dev, 20*60);
        //下一步
        return $next($request);
    }
}
