<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/7/2
 * Time: 下午12:43
 */

namespace App\Services\Business;


class HouseDeleteService
{
    private static $self = null;

    public static function instance(){
        if(self::$self==null){
            self::$self = new self();
        }

        return self::$self;
    }

}