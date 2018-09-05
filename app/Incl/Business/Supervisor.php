<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/9/1
 * Time: 上午9:45
 */

namespace App\Incl\Business;


class Supervisor
{
    const RUN_SUCCESS = 'done';

    public static function trigger($signature){
        $command = 'php artisan '.$signature;
        $result = exec($command);
        if($result==self::RUN_SUCCESS) {
            return true;
        }

        return false;
    }

    public static function reBuildUserNameCache(){
        $result = self::trigger('buildUserNameCache');

        return $result;
    }

    public static function startLoginScript(){
        $result = self::trigger('LoginConsumer');
        var_dump($result);die;
    }




}