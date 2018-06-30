<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Business;

class UserController extends Controller
{
    public function distributor($method){
        $houseService = Business\HouseService::instance();
        if(method_exists($houseService ,$method)){
            $result = $houseService->$method();
        }else{
            return response()->json(array('data'=>'' ,'code'=>200 ,'msg'=>'nothing') ,200);
        }
        $result['status'] = isset($result['status']) ? $result['status'] : 200;
        $result['code'] = isset($result['code']) ? $result['code'] : 200;
        $result['msg'] = isset($result['msg']) ? $result['msg'] : '';
        return response()->json(array('data'=>$result['data'] ,'code'=>$result['code'] ,'msg'=>$result['msg']) ,$result['status']);
    }

    public function postDistributor($method){
        $houseService = Business\HousePerformService::instance();
        if(method_exists($houseService ,$method)){
            $result = $houseService->$method();
        }else{
            return response()->json(array('data'=>'' ,'code'=>200 ,'msg'=>'nothing') ,200);
        }
        $result['status'] = isset($result['status']) ? $result['status'] : 200;
        $result['code'] = isset($result['code']) ? $result['code'] : 200;
        $result['msg'] = isset($result['msg']) ? $result['msg'] : '';
        return response()->json(array('data'=>$result['data'] ,'code'=>$result['code'] ,'msg'=>$result['msg']) ,$result['status']);
    }

}
