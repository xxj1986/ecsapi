<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class AuthController extends BaseController
{
    protected $dev;

    public function __construct()
    {
        $dev = trim($_GET['device']);
        if(!$dev){
            return response()->json(['errcode'=>1001,'message'=>'参数错误！']);
        }
        $token = Redis::hget('token',$dev);
        if(strlen($token) == 32){
            return response()->json(['errcode'=>2002,'message'=>'您已登录']);
        }
        $this->dev = $dev;
    }

    /*
     * 登录
     */
    public function login(Request $request){
        $user_mobile = $request->input('user_mobile');
        $password = $request->input('password');
        if($user_mobile == 138111 && $password == 123){
            $token = str_random(32);
            $res = Redis::hset('token',$this->dev,$token);
            return response()->json(['errcode'=>0,'message'=>'登录成功','token'=>$token]);
        }else{
            return response()->json(['errcode'=>2002,'message'=>'账号或密码错误！']);
        }
    }

    /*
     * 退出
     */
    public function logout(Request $request){
        //获取设备和签名
        $dev = trim($request->input('device'));
        $sign = trim($request->input('sign'));

        $data = ['errcode'=>0,'message'=>'退出成功'];

        $token = Redis::hget('token',$dev);
        if(!$token){ //没有登录或已超时
            return response()->json($data);
        }
        $checkSign = md5($dev.$token);
        if($checkSign == $sign){ //验证通过
            //Redis::unset('token',$dev);
            return response()->json($data);
        }
        //验证没有通过
        return response()->json(['errcode'=>2003,'message'=>'您是真人吗？']);
    }
}
