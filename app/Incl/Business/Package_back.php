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

    const PUBLIC = 0;
    const PRIVATE = 1;

    const USER_PACKAGE_LIST = 'package_list';
    const USER_PACKAGE_POOL = 'package_pool';
    const USER_MESSAGE_OUTBOX = 'user_message_outbox';          //自己写
    const USER_MESSAGE_INBOX = 'user_message_inbox';            //读取
    const USER_MESSAGE_POOL = 'user_message_pool';
    const USER_PROPER_PACKAGE = 'user_proper_package';
    const PROPER_PACKAGE_POOL = 'proper_package_pool';
//    public $fields = ['user_id','read_count','read_info', 'content','created_at' ,'updated_at'];
    public static $self = null;
    public $name = null;


    public function getName($name)
    {
        $this->name = $name;
        return array('data' => $this->name);
    }


    /**
     * list
     * @return array
     */
    public static function getProperUserPackage()
    {

        if ($data = Redis::ZREVRANGEBYSCORE(self::USER_PACKAGE_POOL, INF, -INF)) {
            $target = array_pop($data);
            $target = explode('_', $target);
            if($target[0] != Player::getUserId()) {
                $cache_list_key = self::USER_PACKAGE_LIST . '_' . $target[0];
                $content = Redis::LINDEX($cache_list_key, $target[1]);
                return array('user' => $target[0], 'content' => $content ,'offset'=>$target[1]);
            }elseif ($data){
                $target = array_pop($data);
                $target = explode('_', $target);
                $cache_list_key = self::USER_PACKAGE_LIST . '_' . $target[0];
                $content = Redis::LINDEX($cache_list_key, $target[1]);
                return array('user' => $target[0], 'content' => $content ,'offset'=>$target[1]);
            }

        }

        return array();
    }

    /**
     * sorted set
     * @param $write    user_write
     * @param $offset   in user_package_list offset
     * @return bool
     */
    public static function insertPackagePool($write, $offset)
    {

        $cache_value = $write . '_' . $offset;
        if (Redis::ZADD(self::USER_PACKAGE_POOL, 0, $cache_value)) {
            return true;
        }
        return false;
    }

    /**
     * list
     * @param $user_id
     * @return array
     */
    public static function getUserPackageList($user_id ,$offset=true)
    {
        $cache_list_key = self::USER_PACKAGE_LIST . '_' . $user_id;
        return self::getCacheListData($cache_list_key ,$offset);
    }

    /**
     * list
     * @param $user_id
     * @param string $content
     * @return bool
     */
    public static function insertUserPackageList($user_id, $content = '' ,$type)
    {

        $cache_list_key = self::USER_PACKAGE_LIST . '_' . $user_id;
        $content = array(
            'content'=>$content,
            'time'=>time(),
            'type'=>$type,
        );

        if ($type==self::PUBLIC && $offset = Redis::RPUSH($cache_list_key, json_encode($content))) {
            return self::insertPackagePool($user_id, $offset - 1);
        }

        return false;
    }


    /**
     * hash
     * @param $reader
     * @param $writer
     * @param $message_offset
     * @return mixed
     */
    public static function setUserMessagePool($reader ,$writer ,$message_offset)
    {
        $index = $writer % 10;
        $cache_key = self::USER_MESSAGE_POOL.'_'.$index;
        $data = array(
            'read_time'=>time(),
            'reader'=>$reader,
        );
        return Redis::HMSET($cache_key ,$writer.'_'.$message_offset ,json_encode($data));
    }

    /**
     * list
     * @param $user_id
     * @return array
     */
    public static function getUserMessageInbox($user_id)
    {
        $cache_list_key = self::USER_MESSAGE_INBOX . '_' . $user_id;
        return self::getCacheListData($cache_list_key);
    }

    /**
     * list
     * @param $writer
     * @param $reader
     * @param $message_offset
     * @return mixed
     */
    public static function insertUserMessageInbox($writer ,$reader, $message_offset)
    {
        $cache_message_key = self::USER_MESSAGE_INBOX . '_' . $reader;
        $data = array(
            'writer'=>$writer,
            'message_offset'=>$message_offset,
        );

        return Redis::RPUSH($cache_message_key, json_encode($data));
    }

    /**
     * list
     * @param $user_id
     * @return array
     */
    public static function getUserMessageOutbox($user_id ,$offset=true)
    {
        $cache_list_key = self::USER_MESSAGE_OUTBOX . '_' . $user_id;
        return self::getCacheListData($cache_list_key ,$offset);
    }

    /**
     * list
     * @param $write
     * @param $cotent
     * @param $read
     * @param $packageOffset
     * @return bool|mixed
     */
    public static function insertUserMessageOutbox($write, $cotent, $read, $packageOffset ,$package_owner)
    {
        $cache_message_key = self::USER_MESSAGE_OUTBOX . '_' . $write;
        $insert = array(
            'content' => $cotent,
            'reader' => $read,
            'package_offset' => $packageOffset,
            'package_owner' => $package_owner,
            'write_time'=>time(),
        );
        if ($message_offset = Redis::RPUSH($cache_message_key, json_encode($insert))) {
            return self::insertUserMessageInbox($write , $read ,$message_offset-1);
        }

        return false;
    }


//    public static function getUserProperPackage($user_id){
//        $cache_message_key = self::USER_PROPER_PACKAGE . '_' . $user_id;
//        return self::getCacheListData($cache_message_key ,false);
//    }

    public static function insertUserProperPackage($writer ,$reader ,$packageOffset){
        $cache_message_key = self::USER_PROPER_PACKAGE . '_' . $reader;

        $lastIndex = Redis::LLEN($cache_message_key) - 1;
        if(!Redis::HEXISTS(self::PROPER_PACKAGE_POOL ,$reader.'_'.$lastIndex)){
            return false;
        }
        $insert = array(
            'writer'=>$writer,
            'package_offset'=>$packageOffset,
        );
        if ($message_offset = Redis::RPUSH($cache_message_key, json_encode($insert))) {
            return self::insertProperPackagePool($reader ,$message_offset-1);
        }

        return false;
    }

    public static function getProperPackagePool($owner_id ,$offset){
        if(Redis::HEXISTS(self::PROPER_PACKAGE_POOL ,$owner_id.'_'.$offset)){
            return Redis::HGET(self::PROPER_PACKAGE_POOL ,$owner_id.'_'.$offset);
        }

        return '';
    }

    public static function insertProperPackagePool($owner ,$offset){
        $data = array(
            'reply'=>true,
            'time'=>time(),
        );
        return Redis::HMSET(self::PROPER_PACKAGE_POOL ,$owner.'_'.$offset ,json_encode($data));
    }


}