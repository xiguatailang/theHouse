<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/6/28
 * Time: 下午4:44
 */

namespace App\Incl\Business;


use App\Incl\BaseObject;

class Package extends BaseObject
{
    const cache_expire_time = 10000;
//    public $fields = ['user_id','read_count','read_info', 'content','created_at' ,'updated_at'];
    public static $self = null;
    public $name = null;


    public function getName($name){
        $this->name = $name;
        return array('data'=>$this->name);
    }


    public static function getProperUserPackage(){

    }

    public static function insertPackagePool(){

    }

    public static function getUserPackageList($user_id){

    }

    public static function insertUserPackageList($user_id){

    }

    public static function getMessagePool(){

    }

    public static function insertMessagePool(){

    }

    public static function getUserMessageInbox($user_id){

    }

    public static function insertUserMessageInbox($user_id){

    }

    public static function getUserMessageOutbox($user_id){

    }

    public static function insertUserMessageOutbox($user_id){

    }

    public static function readOwnRightPackageBox($user_id){

    }

    public static function setOwnRightPackageBox($user_id){

    }


}