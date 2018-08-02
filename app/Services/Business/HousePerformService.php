<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/6/28
 * Time: 上午10:28
 */
namespace App\Services\Business;
use App\Help\HouseValid;
use App\Incl\Business\Package;
use App\Incl\Business\Player;
use App\Services\BaseService;
use App\Enumeration\App;
use Illuminate\Support\Facades\Redis;



class HousePerformService extends BaseService{

    private static $self = null;

    public static function instance(){
        if(self::$self==null){
            self::$self = new self();
        }

        return self::$self;
    }


    public function getUserInfo(){
        $data = Player::get();
        unset($data['token']);
        return array('data'=>$data);
    }

    /**
     *
     */
    public function writeUserPackage(){
        //对content内容做校验
        //插入package List
        //插入 package pool
        $user_id = Player::getUserId();
        $validContent = HouseValid::validContent($_REQUEST['content']);
        if(!$validContent['result']){
            return array('code'=>App::BUSINESS_EXCEPTION_CODE ,'msg'=>$validContent['msg']);
        }

        if(Package::insertUserPackageList($user_id ,$validContent['content'] ,$_REQUEST['type'])){
            return array('code'=>App::BUSINESS_SUCCESS_CODE ,'msg'=>'Success');
        }

        return array('code'=>App::BUSINESS_EXCEPTION_CODE ,'msg'=>'save failure');
    }

    public function getProperPackage(){
        //获取一个读取次数最少的package
        $data = Package::getProperUserPackage();
        return array('data'=>$data);
    }


    public function writeMessage(){
        //写入 user_message_out_box
        //写入 target_user_message_inner_box
        $user_id = Player::getUserId();
        $validContent = HouseValid::validContent($_REQUEST['content']);
        if(!$validContent['result']){
            return array('code'=>App::BUSINESS_EXCEPTION_CODE ,'msg'=>$validContent['msg']);
        }
        $reader = $_REQUEST['target'];
        $offset = $_REQUEST['offset'];
        $data = Package::insertUserMessageOutbox($user_id,$validContent['content'],$reader,$offset ,$reader);
        return array('data'=>$data);
    }

     public function getUserMessage(){
        $packages = array();
        $user_id = Player::getUserId();
        $data = Package::getUserMessageInbox($user_id);
        foreach ($data as $datum){
            $datum = json_decode($datum ,true);
            $message = Package::getUserMessageOutbox($datum['writer'] ,$datum['message_offset']);
            $message = json_decode($message,true);

            $package_index = $datum['writer'].'_'.$message['package_offset'];
            if(!isset($packages[$package_index])){
                $package = Package::getUserPackageList($message['package_owner'] ,$message['package_offset']);
                $packages[$package_index]['package'][] = $package;
            }

            $packages[$package_index]['message'][] = $message;
        }


        return array('data'=>$packages);
     }


     public function replyMessage(){

         $user_id = Player::getUserId();
         $validContent = HouseValid::validContent($_REQUEST['content']);
         if(!$validContent['result']){
             return array('code'=>App::BUSINESS_EXCEPTION_CODE ,'msg'=>$validContent['msg']);
         }
         $reader = $_REQUEST['reader'];
         $packageOffset = $_REQUEST['package_offset'];
         $package_owner = $_REQUEST['package_owner'];
         $data = Package::insertUserMessageOutbox($user_id,$validContent['content'],$reader,$packageOffset ,$package_owner);
         return array('data'=>$data);

     }


}