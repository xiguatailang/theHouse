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
        $user_id = $_REQUEST['user']['user_id'];
        return Redis::get(App::USER_LOGIN_KEY.'_'.$user_id);
    }

    public static function getUserId(){
        return $_REQUEST['user']['user_id'];
    }

    public static function makeUserCacheData($user_id){
        $packages = self::getUserPackages($user_id);
        $messages = self::getPackageMessages($user_id);
        var_dump($packages,$messages);die;
    }


    public static function isUserInitCache($user_id){

        $user_data = Redis::get(App::USER_LOGIN_KEY.'_'.$user_id);
        $user_data = json_decode($user_data ,true);
        if($user_data['packages']!==null) {
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

    public static function setMessageToReader($user_id ){

    }


    public static function getUserPackages($user_id){

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


}