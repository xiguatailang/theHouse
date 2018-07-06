<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/6/29
 * Time: 下午6:21
 */

namespace App\Help;


class HouseValid
{

    public static function validPassWord($passWord){

        return true;
    }

    public static function resolvePassWord($passWord){


        return $passWord;
    }

    public static function validUserName($name){

        return true;
    }

}