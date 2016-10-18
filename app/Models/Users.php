<?php
/**
 * Created by PhpStorm.
 * User: PC1
 * Date: 2016/10/18
 * Time: 10:41
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table='users';

    //根据手机号查询用户信息
    public function getUserByMobile($mobile){
        return $this->where('mobile_phone',$mobile)->first();
    }

    //注册用户
    public function createUser($params){
        if(!isset($params['mobile_phone']) || !isset($params['password'])){
            return false;
        }
        $data = [
            'email' => '',
            'user_name' => str_random(16), //用户名
            'password' => '', //密码
            'question' => '',
            'answer' => '',
            'last_ip' => '',
            'alias' => '', //昵称
            'mobile_phone' => '', //手机号
            'credit_line' => 800000, // 最大消费
        ];
        $data['mobile_phone'] = $params['mobile_phone'];
        $data['password'] = $params['password'];

        return $this->insert($data);
    }

    //修改默认地址
    public function setDefaultAddressId($user_id,$address_id){
        return $this->where('user_id',$user_id)->update(['address_id'=>$address_id]);
    }

}