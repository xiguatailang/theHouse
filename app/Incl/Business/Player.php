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
use Illuminate\Support\Facades\Redis;
use App\Help\HouseValid;
use Illuminate\Support\Facades\DB;



class Player extends BaseObject
{

//    public $fields = ['user_id' ,'name','age','sex','created_at','updated_at'];
    const USER_PACKAGE_LIST = 'package_list';
    const USER_PACKAGE_SORT = 'package_sort';
    const USER_MESSAGE_LIST = 'message_list';
    const USER_PROPER_PACKAGE_HASH = 'user_proper_package_hash';

    public static function get(){
        $user_id = $_REQUEST['user_id'];
        $user_data = Redis::get(App::USER_LOGIN_KEY.'_'.$user_id);
        $user_data = json_decode($user_data ,true);

        return $user_data;
    }

    public static function getUserId(){
        return $_REQUEST['user_id'];
    }

    public static function makeUserCacheData($user_id){
        $_REQUEST['message_time'] = isset($_REQUEST['message_time']) ? $_REQUEST['message_time'] : time();
        $suffix = date('Ym' ,$_REQUEST['message_time']);
        $packages = array();

        $messages_data = self::getUserMessages($user_id);
        $messages = $messages_data['messages'];
        $other_package_ids = array_keys($messages);
        if($other_package_ids){
            $all_packages = DB::table('packages_'.$suffix)->whereIn('id', $other_package_ids)->orWhere('user_id', $user_id)->orderBy('created_at', 'desc')->get();

        }else{
            $all_packages = DB::table('packages_'.$suffix)->Where('user_id', $user_id)->orderBy('created_at', 'desc')->get();
        }

        foreach ($all_packages as $item){
            $packages[$item->id] = [
                'package_id'=>$item->id,
                'created_at'=>date('Y-m-d H:i',$item->created_at),
                'content'=>$item->content,
                'read_count'=>$item->read_count,
                'writer'=>Redis::HGET(App::USER_NAME_POOL ,$item->user_id),
            ];
        }

        if($packages){
            foreach ($messages as $package_id=>$package){
                if(isset($messages[$package_id])){
                    $packages[$package_id]['dialogue'] = $messages[$package_id];
                }
            }
        }
        $packages = array_values($packages);

        $user_data = Redis::get(App::USER_LOGIN_KEY.'_'.$user_id);
        $user_data = json_decode($user_data ,true);
        $user_data['messages'] = $packages;
        $user_data['not_read'] = $messages_data['not_read'];

        
        Redis::set(App::USER_LOGIN_KEY.'_'.$user_id ,json_encode($user_data));
    }


    public static function isUserInitCache($user_id){

        $user_data = Redis::get(App::USER_LOGIN_KEY.'_'.$user_id);
        $user_data = json_decode($user_data ,true);
        if($user_data['messages']!==null) {
            return true;
        }

        return false;
    }


    public static function writePackages($user_id ,$content){
        $content = HouseValid::validContent($content);
        if($content['result']){
            $content = $content['content'];
        }else{
            return false;
        }
        $user_data = Redis::get(App::USER_LOGIN_KEY.'_'.$user_id);
        $user_data = json_decode($user_data ,true);
        //已经初始化过，追加数据到缓存。没有初始化返回false不予操作
        if($user_data['packages']!==null) {
            $tmp = [
                [
                    'created_at'=>time(),
                    'content'=>$content,
                    'read_count'=>0,
                ]
            ];
            $user_data['packages'] = array_merge($tmp ,$user_data['packages']);
        }else{
            return false;
        }
    }

    public static function writeMessages($user_id ,$content ,$write_to ,$package_id){
        $content = HouseValid::validContent($content);
        if($content['result']){
            $content = $content['content'];
        }else{
            return false;
        }
        $user_data = Redis::get(App::USER_LOGIN_KEY.'_'.$user_id);
        $user_data = json_decode($user_data ,true);
        //已经初始化过，追加数据到缓存。没有初始化返回false不予操作
        if($user_data['packages']!==null) {
            $tmp = [
                [
                    'write_time'=>time(),
                    'content'=>$content,
                    'write_to'=>$write_to,
                    'package_id'=>$package_id,
                ]
            ];
            $user_data['messages']['write'] = array_merge($tmp ,$user_data['packages']);

            self::setMessageToReader();
        }else{
            return false;
        }
    }



    public static function getUserPackages($user_id){
        $_REQUEST['message_time'] = isset($_REQUEST['message_time']) ? $_REQUEST['message_time'] : time();
        $result = array();
        $suffix = date('Ym' ,$_REQUEST['message_time']);

        $user = DB::select('select * from packages_'.$suffix.' where user_id = ? ORDER BY created_at desc', [$user_id]);
        if($user){
            foreach ($user as $item){
                $result[$item->id] = [
                    'package_id'=>$item->id,
                    'created_at'=>$item->created_at,
                    'content'=>$item->content,
                    'read_count'=>$item->read_count,
                ];
            }
        }

        return $result;
    }


    public static function getPackageMessages($package_id){
        $_REQUEST['message_time'] = isset($_REQUEST['message_time']) ? $_REQUEST['message_time'] : time();
        $result = array();
        $suffix = date('Ym' ,$_REQUEST['message_time']);
        $user = DB::select('select * from messages_'.$suffix.' where package_id  = ? ORDER BY write_at desc', [$package_id]);

        if($user){
            foreach ($user as $item){
                $result[$item->package_id][] = [
                    'package_id'=>$item->package_id,
                    'message_id'=>$item->id,
                    'write_at'=>$item->write_at,
                    'content'=>$item->content,
                    'read_at'=>$item->read_at,
                ];
            }
        }

        return $result;
    }


    public static function getUserMessages($user_id){
        $result = array();
        $not_read = 0;
        $suffix = date('Ym' ,$_REQUEST['message_time']);
        $messages = DB::select('select * from messages_'.$suffix.' where reader = ? or writer = ? ORDER BY write_at desc', [$user_id ,$user_id]);
        if($messages){
            foreach ($messages as $item){
                if($item->reader==$user_id && !$item->read_at){
                    $not_read++;
                }

                $result[$item->package_id][$item->id] = [
                    'message_id'=>$item->id,
                    'package_id'=>$item->package_id,
                    'write_at'=>date('Y-m-d H:i',$item->write_at),
                    'content'=>$item->content,
                    'reader'=>$item->reader,
                    'writer'=>$item->writer,
                ];
            }
        }

        return array('messages'=>$result ,'not_read'=>$not_read);
    }


    public static function insertProperPaclageSort($writer ,$content ,$created_at){
        $cache_data = [
            'writer_id'=>$writer,
            'content'=>$content,
            'created_at'=>$created_at,
        ];

        if (Redis::ZADD(self::USER_PACKAGE_SORT, 0, json_encode($cache_data))) {
            return true;
        }
        return false;
    }


    public static function readProperPackageSort(){
        $user_id = self::getUserId();

        $content = '';
        $score = 0;
        //将合适的package存入 用户读取合适package的hash列表里。优先读取列表。没有再从有序集合里取数据
        if( $proper = Redis::HGET(self::USER_PROPER_PACKAGE_HASH, $user_id) ){

            $proper = json_decode($proper ,true);
            if(!$proper['reply']){
                $proper['writer_name'] = Redis::HGET(App::USER_NAME_POOL, $proper['writer_id']);
                $proper['created_at_f'] = date('Y.m.d', $proper['created_at']);
                return $proper;
            }
        }

        if ($data = Redis::ZREVRANGEBYSCORE(self::USER_PACKAGE_SORT, INF, -INF ,'WITHSCORES')) {

            while(true){
                $target = array_slice($data ,-1);
                array_pop($data);
                $content = key($target);
                $content = json_decode($content ,true);
                if($content['writer_id'] != $user_id){
                    $score = array_pop($target);
                    break;
                }
            }

            if($content) {
                $score++;
                Redis::ZINCRBY(self::USER_PACKAGE_SORT, $score, json_encode($content));
                //写入user_proper_package_hash
                $content['reply'] = 0;
                Redis::HSET(self::USER_PROPER_PACKAGE_HASH, $user_id, json_encode($content));


                $content['writer_name'] = Redis::HGET(App::USER_NAME_POOL, $content['writer_id']);
                $content['created_at_f'] = date('Y.m.d', $content['created_at']);

                return $content;
            }
        }

        return false;
    }

    public static function getProperPackageId($user_id ,$created_at){

        $_REQUEST['message_time'] = isset($_REQUEST['message_time']) ? $_REQUEST['message_time'] : time();
        $result = array();
        $suffix = date('Ym' ,$_REQUEST['message_time']);

        $package = DB::select('select * from packages_'.$suffix.' where user_id = ? and created_at= ?', [$user_id ,$created_at]);
        if($package){
            $package = array_pop($package);
            return $package->id;
        }

        return false;
    }


    public static function insertPackageList($writer ,$content ,$created_at){

        $data = [
            'writer'=>$writer,
            'content'=>$content,
            'created_at'=>$created_at,
        ];
        if(Redis::RPUSH(self::USER_PACKAGE_LIST, json_encode($data))){
            return true;
        }

        return false;
    }

    public static function insertMessageList($writer ,$reader ,$package_id ,$content){

        $data = [
            'writer'=>$writer,
            'reader'=>$reader,
            'package_id'=>$package_id,
            'content'=>$content,
            'write_at'=>time(),
        ];
        if(Redis::RPUSH(self::USER_MESSAGE_LIST, json_encode($data))){
            return true;
        }

        return false;
    }


    public function updateUserPackageCache(){

    }


    public function updateUserDialogueCache(){

    }


}