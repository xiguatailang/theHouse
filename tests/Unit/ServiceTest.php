<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/8/15
 * Time: 下午4:21
 */

namespace Tests\Unit;
use Tests\TestCase;
use App\Enumeration\App;
use Illuminate\Support\Facades\Redis;
use App\Incl\Business\Player;

class ServiceTest extends TestCase
{
    const USER_ID = 10000000000;

    public function testInit(){
        Player::getUserPackages(self::USER_ID);
//        $this->getUserCacheData();
//        $this->writeMessage();
    }

    public function getUserCacheData(){
        $user_data = Redis::get(App::USER_LOGIN_KEY.'_'.self::USER_ID);
        echo "\n".$user_data."\n";
    }

    public function writePackage(){
        $content = 'hello world,i came from 10000000000';

    }

    public function getProperPackage(){

    }

    public function writeMessage(){
        $a = [
            ['a1'],
            ['a2'],
        ];

        $b = [
            ['b1']
        ];

        var_dump(array_merge($b,$a));die;
    }

    public function getAllMessages(){

    }


}