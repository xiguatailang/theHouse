<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/6/28
 * Time: 下午4:52
 */

namespace App\Incl;
use Illuminate\Support\Facades\Redis;



class BaseObject
{
    protected static $auth = 'napple';
    public $fields = null;

    public function __construct()
    {

    }

    public static function getCacheListData($cache_key ,$type=true){

        $length = Redis::LLEN($cache_key);

        if($type) {
            if ($data = Redis::LRANGE($cache_key, 0, $length)) {
                return $data;
            }
        }else{
            return Redis::LINDEX($cache_key ,$length-1);
        }

        return array();
    }


}