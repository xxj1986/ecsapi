<?php


namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use Gregwar\Captcha\CaptchaBuilder;

class ExampleController extends Controller
{

    public function test(){
        //header('Content-type: image/jpeg');
        //dd(1);
        $captcha = new CaptchaBuilder();
        $captcha->build();

        return '<img src="'.$captcha->inline().'" />'.$captcha->getPhrase();

    }

    //
}
