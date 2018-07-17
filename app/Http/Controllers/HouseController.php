<?php

namespace App\Http\Controllers;

use App\Enumeration\App;
use App\Help\HouseValid;
use Illuminate\Http\Request;
use App\Services\Business;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;


class HouseController extends Controller
{

    //登陆和注册是大问题，后续需要好好完善
    public function register(){
        if(HouseValid::validPassWord($this->params)){

        }

        return self::restResp('' ,'invalid password' ,App::BUSINESS_EXCEPTION_CODE);
    }


    public function login(Request $request){
        //校验用户名密码是否在库里
        if(isset($this->params['password']) && isset($this->params['name'])){
            $passWord = HouseValid::resolvePassWord($this->params['password']);

            if(!HouseValid::validPassWord($passWord)){
                return self::restResp('' ,'invalid password' ,App::BUSINESS_EXCEPTION_CODE);
            }

            if(!HouseValid::validUserName($this->params['name'])){
                return self::restResp('' ,'invalid user name' ,App::BUSINESS_EXCEPTION_CODE);
            }

            var_dump($request->header('tokenhh'));die;

            $user = DB::select('select * from user where name = ? and password=?', [$this->params['name'], $this->params['password']]);

            if($user){
                //TODO  生成cookie，cookie可以包含user_id      将cookie值存入缓存
                $user_id = $user[0]->user_id;
//                $user_cookie = Cookie::make('user_login',10000,10);

                return response()->json(array('data'=>'' ,'code'=> App::BUSINESS_SUCCESS_CODE ,'msg'=>'login__en_success') ,200)->header('Access-Control-Expose-Headers','token')->header('token',1002);
            }else{
                return self::restResp('' ,'account and password are incorrect' ,App::BUSINESS_EXCEPTION_CODE);
            }
        }

        return self::restResp('' ,'invalid params' ,App::BUSINESS_EXCEPTION_CODE);
    }


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
            return self::restResp('' ,'nothing' ,App::BUSINESS_EXCEPTION_CODE);
        }
        $result['status'] = isset($result['status']) ? $result['status'] : 200;
        $result['code'] = isset($result['code']) ? $result['code'] : App::BUSINESS_SUCCESS_CODE;
        $result['msg'] = isset($result['msg']) ? $result['msg'] : '';

        return response()->json(array('data'=>$result['data'] ,'code'=>$result['code'] ,'msg'=>$result['msg']) ,$result['status']);
    }


}