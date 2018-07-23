<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/7/22
 * Time: 上午11:16
 */

namespace App\Help;


class Algorithm
{

    public static function tokenEncrypt($user_id){
        $param1 = rand(100,999);
        $param2 = rand(100,999);

        return base64_encode($param1.$user_id.$param2);
    }

    public static function tokenDecrypt($token){
        $str = base64_decode($token);
        $str = substr($str ,3);
        $str = substr($str ,0 , -3);

        return $str;
    }

}