<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/6/28
 * Time: 下午4:52
 */

namespace App\Incl;


class BaseObject
{
    protected static $auth = 'napple';
    public $fields = null;

    public function __construct()
    {
//        var_dump('as',$this);die;
    }


}