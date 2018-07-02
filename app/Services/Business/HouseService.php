<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/6/28
 * Time: 上午10:28
 */
namespace App\Services\Business;
use App\Incl\Business\Package;
use App\Incl\Business\Player;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class HouseService extends BaseService{

    private static $self = null;

    public static function instance(){
        if(self::$self==null){
            self::$self = new self();
        }

        return self::$self;
    }

    public function say(){
        return array('data'=>'hello');
    }

    public function getTheOnePackage(){

    }


    public function getUserMessage($user_id){

    }


}