<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/6/28
 * Time: 上午10:28
 */
namespace App\Services\Business;
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

    public function setUserLetter(){
        $name = Redis::smembers('mytest');

        var_dump('set' ,$name);die;
    }


}