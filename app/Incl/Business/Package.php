<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/6/28
 * Time: 下午4:44
 */

namespace App\Incl\Business;


use App\Incl\BaseObject;
use Illuminate\Support\Facades\Redis;


class Package extends BaseObject
{
    const cache_expire_time = 10000;

    const USER_PACKAGE_LIST = 'package_list';
    const USER_PACKAGE_POOL = 'package_pool';
//    public $fields = ['user_id','read_count','read_info', 'content','created_at' ,'updated_at'];
    public static $self = null;
    public $name = null;


    public function getName($name){
        $this->name = $name;
        return array('data'=>$this->name);
    }


    public static function getProperUserPackage(){

        if($data = Redis::ZREVRANGEBYSCORE(self::USER_PACKAGE_POOL ,INF ,-INF)){
            $target = array_pop($data);
            $target = explode('_',$target);
            $cache_list_key = self::USER_PACKAGE_LIST.'_'.$target[0];
            $content = Redis::LINDEX($cache_list_key ,$target[1] -1);

            return array('user'=>$target[0] ,'content'=>$content);
        }

        return array();
    }

    /**
     * @param $write    user_write
     * @param $offset   in user_package_list offset
     * @return bool
     */
    public static function insertPackagePool($write,$offset){

        $cache_value = $write.'_'.$offset;
        if(Redis::ZADD(self::USER_PACKAGE_POOL ,0 ,$cache_value)){
            return true;
        }
        return false;
    }

    /**
     * @param $user_id
     * @return array
     */
    public static function getUserPackageList($user_id){
        $cache_list_key = self::USER_PACKAGE_LIST.'_'.$user_id;
        $length = Redis::LLEN($cache_list_key);
        if($data = Redis::LRANGE($cache_list_key ,0 ,$length)){
            return $data;
        }

        return array();
    }

    /**
     * @param $user_id
     * @param string $content
     * @return bool
     */
    public static function insertUserPackageList($user_id ,$content=''){

        $cache_list_key = self::USER_PACKAGE_LIST.'_'.$user_id;
        if($offset = Redis::RPUSH($cache_list_key ,$content)){
            return self::insertPackagePool($user_id,$offset);
        }

        return false;
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