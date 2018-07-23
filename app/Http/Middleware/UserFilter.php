<?php

namespace App\Http\Middleware;

use App\Enumeration\App;
use App\Help\Algorithm;
use Closure;
use Illuminate\Support\Facades\Redis;

class UserFilter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //解析token是否存在和过期
        //没有则更新过期时间
        $token = $request->header('token');
        $user_id = Algorithm::tokenDecrypt($token);
        if($data = Redis::get(App::USER_LOGIN_KEY.'_'.$user_id)){
            $data = json_decode($data ,true);
            if($token == $data['token']){
                Redis::Expire(App::USER_LOGIN_KEY.'_'.$user_id ,App::USER_LOGIN_EXPIRE_TIME);
                $data['user_id'] = $user_id;
                $_REQUEST['user'] = $data;
                return $next($request);
            }else{
                return response()->json(array('data'=>false ,'code'=>App::BUSINESS_EXCEPTION_CODE ,'msg'=>'Login state exception!') ,200);
            }
        }
        return response()->json(array('data'=>false ,'code'=>App::BUSINESS_EXCEPTION_CODE ,'msg'=>'Please login!') ,200);
    }



}
