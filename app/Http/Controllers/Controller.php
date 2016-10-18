<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected $dev;

    public function __construct()
    {
        $this->dev = isset($_GET['device']) ? trim($_GET['device']) : '';
    }
}
