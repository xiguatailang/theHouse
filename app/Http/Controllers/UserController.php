<?php

namespace App\Http\Controllers;

use App\Enumeration\App;
use Illuminate\Http\Request;
use App\Services\Business;

class UserController extends Controller
{
    public function distributor($method ,Request $request){
        $http_method = $request->method();
        switch ($http_method){
            case App::HTTP_POST_METHOD:
                $service = Business\HousePerformService::instance();
                break;
            case App::HTTP_PUT_METHOD:
                $service = Business\HousePutService::instance();
                break;
            case App::HTTP_DELETE_METHOD:
                $service = Business\HouseDeleteService::instance();
                break;
            default:
                $service = Business\HouseService::instance();
                break;
        }
        if(method_exists($service ,$method)){
            $result = $service->$method();
        }else{
            return response()->json(array('data'=>'' ,'code'=>200 ,'msg'=>'nothing') ,200);
        }
        $result['status'] = isset($result['status']) ? $result['status'] : 200;
        $result['code'] = isset($result['code']) ? $result['code'] : 200;
        $result['msg'] = isset($result['msg']) ? $result['msg'] : '';
        return response()->json(array('data'=>$result['data'] ,'code'=>$result['code'] ,'msg'=>$result['msg']) ,$result['status']);
    }


}
