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
        //获取用户名和密码
        $user_mobile = $request->input('user_mobile');
        $password = $request->input('password');
        //获取
        $userModel = new Users();
        $user = $userModel->getUserByMobile($user_mobile);
        if( $user && md5($password) == $user->password ){
            $token = str_random(32);
            //绑定user_id和device
            Redis::hset('device', $user->user_id, $this->dev);
            //保存token,设置超时时间20秒
            Redis::set('token'.$this->dev, $token);
            Redis::expire('token'.$this->dev, 20*60);
            //更新最后登陆时间
            $userModel->where('user_id',$user->user_id)->update(['last_login'=>time()]);
            //返回user_id,token
            return response()->json(['errcode'=>0,'message'=>'登录成功','user_id'=>$user->user_id,'token'=>$token]);
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

        Redis::unset('token'.$this->dev);

        return response()->json(['errcode'=>0,'message'=>'退出成功']);
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
        $cap = Redis::get('captcha'.$this->dev);
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
        // 存redis并设置过期时间
        Redis::set('regMobile'.$this->dev, $mobile_phone);
        Redis::set('regPass'.$this->dev, md5($password));
        Redis::expire('regMobile'.$this->dev, 10*60);
        Redis::expire('regPass'.$this->dev, 10*60);

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
        $data = [
            'mobile_phone' => Redis::get('regMobile'.$this->dev),
            'password' => Redis::get('regPass'.$this->dev),
        ];
        //验证是否超时
        if(!$data['mobile_phone']){
            return response()->json(['errcode'=>2,'message'=>'验证超时,请重新注册']);
        }
        //将信息保存到数据库
        $userModel = new Users();
        $res = $userModel->createUser($data);
        if(!$res){
            return response()->json(['errcode'=>2,'message'=>'注册失败']);
        }
        return response()->json(['errcode'=>0,'message'=>'注册成功']);
    }

    /*
     * 创建验证码
     */
    public function createCaptcha(){
        //创建验证码
        $captcha = new CaptchaBuilder();
        $captcha->build();
        //将验证码保存到缓存
        Redis::set('captcha'.$this->dev, $captcha->getPhrase());
        Redis::expire('captcha'.$this->dev, 10*60);
        //输出验证码图片
        return '<img src="'.$captcha->inline().'" />';
    }

    /*
     * 检测手机号
     */
    public function checkMobile($mobile){
        //考虑到安全，该接口暂时不开放。
        // 非法用户可能根据这个接口来探测注册的手机号。
    }

}
