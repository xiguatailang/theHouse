<?php

namespace App\Http\Controllers;

use App\Enumeration\App;
use App\Help\Algorithm;
use App\Help\HouseValid;
use Illuminate\Http\Request;
use App\Services\Business;
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
            try {
                $user = DB::select('select * from user where name = ? and password=?', [$this->params['name'], $this->params['password']]);
            }catch (\Exception $exception){
                $user = array();
            }

            if($user){
                $user_id = $user[0]->user_id;
                $token = Algorithm::tokenEncrypt($user_id);

//                $user_messages = Player::makeUserCacheData($user_id);

                $user_tmp = array(
                    'token'=>$token,
                    'name'=>$user[0]->name,
                    'sex'=>$user[0]->sex,
                    'not_read'=>0,
                    'messages'=>null,
                );


                Redis::set(App::USER_LOGIN_KEY.'_'.$user_id ,json_encode($user_tmp));
                //TODO 同一个key，再次set之后会刷新过期时间。如果不设置过期时间则不会过期
                Redis::Expire(App::USER_LOGIN_KEY.'_'.$user_id ,App::USER_LOGIN_EXPIRE_TIME);

                //将用户id加入登陆缓存池，后面异步load用户package和message数据
                Redis::LPUSH(App::LOGIN_POOL,$user_id);

                return response()->json(array('data'=>'' ,'code'=> App::BUSINESS_SUCCESS_CODE ,'msg'=>'login__en_success') ,200)->header('Access-Control-Expose-Headers','token')->header('token',$token);
            }else{
                return self::restResp('' ,'account and password are incorrect' ,App::BUSINESS_EXCEPTION_CODE);
            }
        }

        return self::restResp('' ,'invalid params' ,App::BUSINESS_EXCEPTION_CODE);
    }

    public function delay(){
        for ($i=0;$i<15;$i++){
            sleep(1);
        }
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
        $result['data'] = isset($result['data']) ? $result['data'] : array();

        return response()->json(array('data'=>$result['data'] ,'code'=>$result['code'] ,'msg'=>$result['msg']) ,$result['status']);
    }


}
