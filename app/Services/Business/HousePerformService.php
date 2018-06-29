<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/6/28
 * Time: 上午10:28
 */
namespace App\Services\Business;
use App\Incl\Business\Package;
use App\Services\BaseService;
use Illuminate\Support\Facades\Redis;

class HousePerformService extends BaseService{

    private static $self = null;

    public static function instance(){
        if(self::$self==null){
            self::$self = new self();
        }

        return self::$self;
    }

    public function setUserPackage(){
        //校验
        //写入user package list
        //写如 package asort

        $result = Package::instance()->setUserPackage();
        $name = Redis::smembers('mytest');

    }

    public function writeMessage(){

    }


}