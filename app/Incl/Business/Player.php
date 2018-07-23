<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/6/29
 * Time: 下午4:05
 */

namespace App\Incl\Business;


use App\Enumeration\App;
use App\Incl\BaseObject;

class Player extends BaseObject
{

//    public $fields = ['user_id' ,'name','age','sex','created_at','updated_at'];

    public static function get(){
        return $_REQUEST['user'];
    }

    public static function getUserId(){
        return $_REQUEST['user']['user_id'];
    }



}