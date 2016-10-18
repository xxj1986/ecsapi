<?php


namespace App\Http\Controllers;

use Gregwar\Captcha\CaptchaBuilder;
use \App\Models\Users;

class ExampleController extends Controller
{

    public function test(){
        //header('Content-type: image/jpeg');
        //dd(1);
        $captcha = new CaptchaBuilder();
        $captcha->build();

        return '<img src="'.$captcha->inline().'" />'.$captcha->getPhrase();

    }

    public function md5(){
        dd(md5('ruowano1'));
    }

    public function model(){
        $userModel = new Users();
        $params = [
            'mobile_phone'=>'13811986573',
            'password' => 'ruowano1'
        ];
        $res = $userModel->createUser($params);
        dd($res);
    }
    public function argshow(){

        $str = '<form method="post">';
        $str .= '<input type="text" name="name" value="value">';
        $str .= '<input type="text" name="key" value="val">';
        $str .= '<input type="text" name="id" value="10086">';
        $str .= '<button>提交</button>';
        $str .= '</form>';
        return $str;

    }

    public function redisExp(){
        if(Redis::hget('hello','key')){
            Redis::expire('hello','key',60);
        }else{
            Redis::hset('hello','key','666',60);
        }
        return 'OK';
    }

    //
}
