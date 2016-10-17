<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected $token;

    public function __construct()
    {
        $dev = isset($_GET['device']) ? trim($_GET['device']) : '';
        $sign = isset($_GET['sign']) ? trim($_GET['sign']) : '';
        if(!$dev || $sign){
            return response()->json(['errcode'=>1001,'message'=>'参数错误！']);
        }
        $token = Redis::hget('token',$dev);
        if(!$token || strlen($token) != 32){
            return response()->json(['errcode'=>2001,'message'=>'请重新登录']);
        }
        Redis::hset('token',$dev,$token,20*60);
        $this->token = $token;
    }
}
