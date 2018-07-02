<?php

namespace App\Http\Controllers;

use App\Enumeration\App;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public $params = array(
        'user_id'=>App::THE_NUM_ONE,
        'name'=>'napple',
    );

    public function setParams($key ,$val){
        $this->params[$key] = $val;
    }
}
