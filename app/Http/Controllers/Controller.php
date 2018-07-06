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

    public $params = null;

    public function __construct()
    {
        $this->params = $_REQUEST;
    }

    public static function restResp($data = '' ,$msg = '' ,$code = App::BUSINESS_SUCCESS_CODE ,$status = 200){

        return response()->json(array('data'=>$data ,'code'=>$code ,'msg'=>$msg) ,$status);
    }

}
