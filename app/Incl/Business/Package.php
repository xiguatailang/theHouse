<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/6/28
 * Time: 下午4:44
 */

namespace App\Incl\Business;


use App\Incl\BaseObject;

class Package extends BaseObject
{
    const cache_expire_time = 10000;
    public $fields = ['user_id','authority','read_info','created_at'];

    public function __construct()
    {
        parent::__construct();
    }

    public static function get($user_id){
        return new self();
    }

    public function setCache(){
        echo 'set';die;
    }

}