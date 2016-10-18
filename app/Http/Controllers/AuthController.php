<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Http\Request;
use App\Models\Users;

class AuthController extends Controller
{
    /*
     * 登录
     */
    public function login(Request $request){
        dd($this->dev);
        $token = Redis::hget('token',$this->dev);
        if(strlen($token) == 32){
            return response()->json(['errcode'=>2002,'message'=>'您已登录']);
        }
        //获取用户名和密码
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
        //获取签名
        $sign = trim($request->input('sign'));

        $data = ['errcode'=>0,'message'=>'退出成功'];

        $token = Redis::hget('token',$this->dev);

        if(!$token){ // 没有登录或已超时
            return response()->json($data);
        }
        $checkSign = md5($this->dev.$token);
        if($checkSign == $sign){ //验证通过
            //Redis::unset('token',$dev);
            return response()->json($data);
        }
        // 验证没有通过
        return response()->json(['errcode'=>2003,'message'=>'您是真人吗？']);
    }

    /*
     * 注册
     */
    public function register(Request $request){

        $mobile_phone = $request->input('mobile_phone');
        $password = $request->input('password');
        $captcha = $request->input('captcha');
        //检查验证码
        if(!$captcha){
            return response()->json(['errcode'=>2008,'message'=>'请输入验证码']);
        }
        $cap = Redis::hget('captcha',$this->dev);
        if($captcha != $cap){
            return response()->json(['errcode'=>2009,'message'=>'验证码错误']);
        }
        //检查手机号
        if(!$mobile_phone){
            return response()->json(['errcode'=>2004,'message'=>'请输入手机号']);
        }
        $match = '/^((13[0-9])|(15[^4,/d])|(18[0,5-9]))[0-9]{8}$/';
        if(!preg_match($match, $mobile_phone)){
            return response()->json(['errcode'=>2005,'message'=>'手机号不正确']);
        }
        $userModel = new Users();
        $userInfo = $userModel->getUserByMobile($mobile_phone);
        if($userInfo){
            return response()->json(['errcode'=>2006,'message'=>'该手机号已注册']);
        }
        //检查密码
        if(!$password){
            return response()->json(['errcode'=>2007,'message'=>'请输入密码']);
        }
        Redis::hset('regMobile',$this->dev,$mobile_phone);
        Redis::hset('regPass',$this->dev,$password);

        //发送短信验证码
        return response()->json(['errcode'=>0,'message'=>'提交信息成功']);
    }

    /*
     * 短信验证码确认
     */
    public function confirm(Request $request){
        $code = $request->input('code');
        if(!$code){
            return response()->json(['errcode'=>2008,'message'=>'请输入短信验证码']);
        }
        //将临时数据写入数据库
        $userModel = new Users();
        $data = [
            'mobile_phone' => Redis::hget('regMobile',$this->dev),
            'password' => Redis::hget('regPass',$this->dev),
        ];
        $res = $userModel->createUser($data);
        if(!$res){
            return response()->json(['errcode'=>2009,'message'=>'注册失败']);
        }
        return response()->json(['errcode'=>0,'message'=>'注册成功']);
    }

    /*
     * 创建验证码
     */
    public function createCaptcha(){

        $captcha = new CaptchaBuilder();
        $captcha->build();
        Redis::hset('captcha',$this->dev,$captcha->getPhrase());
        return '<img src="'.$captcha->inline().'" />';
    }

    /*
     * 检测手机号
     */
    public function checkMobile($mobile){
        //考虑到安全，该接口暂时不开放。
    }

}
