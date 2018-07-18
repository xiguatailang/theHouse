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
    const USER_MESSAGE_OUTBOX = 'user_message_outbox';          //自己写
    const USER_MESSAGE_INBOX = 'user_message_inbox';            //读取
    const USER_MESSAGE_POOL = 'user_message_pool';
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
            $cache_list_key = self::USER_PACKAGE_LIST . '_' . $target[0];
            $content = Redis::LINDEX($cache_list_key, $target[1]);

            return array('user' => $target[0], 'content' => $content);
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
    public static function getUserPackageList($user_id)
    {
        $cache_list_key = self::USER_PACKAGE_LIST . '_' . $user_id;
        return self::getCacheListData($cache_list_key);
    }

    /**
     * list
     * @param $user_id
     * @param string $content
     * @return bool
     */
    public static function insertUserPackageList($user_id, $content = '')
    {

        $cache_list_key = self::USER_PACKAGE_LIST . '_' . $user_id;
        $content = array(
            'content'=>$content,
            'time'=>time(),
        );
        if ($offset = Redis::RPUSH($cache_list_key, json_encode($content))) {
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
    public static function readerUserMessage($reader ,$writer ,$message_offset)
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
    public static function getUserMessageOutbox($user_id)
    {
        $cache_list_key = self::USER_MESSAGE_OUTBOX . '_' . $user_id;
        return self::getCacheListData($cache_list_key);
    }

    /**
     * list
     * @param $write
     * @param $cotent
     * @param $read
     * @param $packageOffset
     * @return bool|mixed
     */
    public static function insertUserMessageOutbox($write, $cotent, $read, $packageOffset)
    {
        $cache_message_key = self::USER_MESSAGE_OUTBOX . '_' . $write;
        $insert = array(
            'content' => $cotent,
            'reader' => $read,
            'package_offset' => $packageOffset,
            'write_time'=>time(),
        );
        if ($message_offset = Redis::RPUSH($cache_message_key, json_encode($insert))) {
            return self::insertUserMessageInbox($write , $read ,$message_offset-1);
        }

        return false;
    }


    public static function readOwnRightPackageBox($user_id){
        
    }

    public static function setOwnRightPackageBox($user_id){

    }


}