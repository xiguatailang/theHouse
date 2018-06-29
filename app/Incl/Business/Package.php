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
//    public $fields = ['user_id','read_count','read_info', 'content','created_at' ,'updated_at'];
    public static $self = null;
    public $name = null;



    public static function instance(){
        if(self::$self==null){
            self::$self = new self();
        }

        return self::$self;
    }

    public function getName($name){
        $this->name = $name;
        return array('data'=>$this->name);
    }


}