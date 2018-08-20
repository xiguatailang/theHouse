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
        return $_REQUEST['user'];
    }

    public static function getUserId(){
        return $_REQUEST['user']['user_id'];
    }

    public static function getUserDbData($user_id){
        return __FUNCTION__;
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

        $result = $packages_ids = array();
        $user = DB::select('select * from packages where user_id = ? limit 10', [$user_id]);
        if($user){
            foreach ($user as $item){
                $result[$item->id] = [
                    'package_id'=>$item->id,
                    'created_at'=>$item->created_at,
                    'content'=>$item->content,
                    'read_count'=>$item->read_count,
                    'messages'=>null,
                ];

                $packages_ids[] = $item->id;
            }
        }

        return array('packages'=>$result ,'packages_ids'=>$packages_ids);
    }


    public static function getUserMessages($user_id){

        $result = array();
        $user = DB::select('select * from messages where  = ? limit 10', [$user_id]);
        if($user){
            foreach ($user as $item){
                $result[$item->id] = [
                    'package_id'=>$item->id,
                    'created_at'=>$item->created_at,
                    'content'=>$item->content,
                    'read_count'=>$item->read_count,
                    'messages'=>null,
                ];
            }
        }

        return $result;
    }


}