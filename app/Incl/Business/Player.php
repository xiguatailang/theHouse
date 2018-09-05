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

        $messages_data = self::getUserMessages($user_id);
        $messages = $messages_data['messages'];
        $other_package_ids = array_keys($messages);
        if($other_package_ids){
            $all_packages = DB::table('packages_'.$suffix)->whereIn('id', $other_package_ids)->orWhere('user_id', $user_id)->orderBy('created_at', 'desc')->get();
            foreach ($all_packages as $item){
                $packages[$item->id] = [
                    'package_id'=>$item->id,
                    'created_at'=>date('Y-m-d',$item->created_at),
                    'content'=>$item->content,
                    'read_count'=>$item->read_count,
                    'writer'=>Redis::HGET(App::USER_NAME_POOL ,$item->user_id),
                ];
            }
        }

        if($packages){
            foreach ($messages as $package_id=>$package){
                if(isset($messages[$package_id])){
                    $packages[$package_id]['dialogue'] = $messages[$package_id];
                }
            }
        }

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


    public static function isUserCacheExsit(){


    }

//    public static function initUserCache($user_id){
//
//        $user_data = Redis::get(App::USER_LOGIN_KEY.'_'.$user_id);
//        $user_data = json_decode($user_data ,true);
//        if($user_data['packages']===null) {
//            $user_packages = Player::getUserPackages($user_id);
//            $user_messages = Player::getUserMessages($user_id);
//
//            $user_data['not_read'] = $user_messages['not_read'];
//            $user_messages = $user_messages['messages'];
//            $user_data['packages'] = $user_packages;
//            $user_data['messages'] = $user_messages;
//            Redis::set(App::USER_LOGIN_KEY.'_'.$user_id ,json_encode($user_data));
//        }
//
//        return true;
//    }


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

    public static function setMessageToReader($user_id ){

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

                $result[$item->package_id][] = [
                    'message_id'=>$item->id,
                    'package_id'=>$item->package_id,
                    'write_at'=>date('Y-m-d',$item->write_at),
                    'content'=>$item->content,
                    'reader'=>$item->reader,
                    'writer'=>$item->writer,
                ];
            }
        }

        return array('messages'=>$result ,'not_read'=>$not_read);
    }


}