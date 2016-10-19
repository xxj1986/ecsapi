<?php


namespace App\Http\Controllers;

use Gregwar\Captcha\CaptchaBuilder;
use \App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

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
    public function argshow(Request $request){
        $device = $request->input('device');
        $token = Redis::get('token'.$this->dev);
        $data = [
            'name' => 'value',
            'key'  => 'val',
            'id'   => '10086',
            'token'=> $token,
            'device' => $device
        ];
        $str = '<form method="post">';
        foreach($data as $k=>$v){
            $str .= "<div>$k:<input type='text' name='$k' value='$v' size='40'></div>";
        }
        ksort($data);
        $sign = md5(json_encode($data));
        $str .= "<div>sign:<input type='text' name='sign' value='$sign' size='40'></div>";
        $str .= '<div><button>提交</button></div>';
        $str .= '</form>';
        return $str;

    }

    public function argtest(){
        return 'test successful';
    }

    public function redisExp(){
        if(Redis::hget('hello','key')){
            Redis::expire('hello','key',60);
        }else{
            Redis::hset('hello','key','666',60);
        }
        return 'OK';
    }

    public function serial(){
        $data = [
            'name' => 'value',
            'key'  => 'val',
            'id'   => '10086',
            'token'=> '12ed31e12',
            'device' => '1q2w3e'
        ];
        foreach($data as $k=>$v) $data[$k] = $k.'='.$v;
        ksort($data); // 按照key升序排序
        $str = implode('&',$data); // 序列化
        $correctSign = md5($str); // md5哈希
        dd($str);
    }

    //
}
