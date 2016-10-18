<?php
/**
 * Created by PhpStorm.
 * User: PC1
 * Date: 2016/10/18
 * Time: 10:41
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminUser extends Model
{
    protected $table='admin_user';

    //根据手机号查询用户信息
    public function getUserByName($name){
        return $this->where('user_name',$name)->first();
    }

    public function createUser($params){
        $data = [];
        $data['user_name'] = isset($params['user'])? $params['user']:'';
    }

}