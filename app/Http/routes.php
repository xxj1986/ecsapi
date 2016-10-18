<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return $app->version();
});

$app->get('test', 'ExampleController@test');
$app->get('test/md5', 'ExampleController@md5');
$app->get('test/model', 'ExampleController@model');
$app->get('argtest', 'ExampleController@argshow');
//以上为测试

$app->group(['prefix' => 'auth','middleware' => 'auth','namespace' => 'App\Http\Controllers'], function () use ($app) {
    //登录
    $app->post('login', 'AuthController@login');
    $app->get('logout', 'AuthController@logout');
    //图片验证码
    $app->get('createCaptcha', 'AuthController@createCaptcha');
    //注册
    $app->post('register', 'AuthController@register');
    $app->post('confirm', 'AuthController@confirm');
});

$app->group(['middleware' => 'sign','namespace' => 'App\Http\Controllers'], function () use ($app) {
    //登录
    $app->post('argtest', 'ExampleController@argtest');
});

