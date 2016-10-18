<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected $dev;
    //获取设备ID
    public function __construct()
    {
        $this->dev = isset($_REQUEST['device']) ? trim($_REQUEST['device']) : '0000-0000-0000-0000';
    }
}
