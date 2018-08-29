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
//        $this->getUserPackages();
//        $this->getPackageMessage();
//        $this->getUserCacheData();
//        $this->writeMessage();
        $this->getUserCacheData();
    }

    public function getUserPackages(){
        $_REQUEST['message_time'] = time();
        $data = Player::getUserPackages(self::USER_ID);
        var_dump($data);
    }

    public function getPackageMessage(){
        $_REQUEST['message_time'] = time();
        $data = Player::getPackageMessages(1);
        var_dump($data);
    }

    public function getUserCacheData(){
        $_REQUEST['message_time'] = time();
        $user_data = Player::makeUserCacheData(self::USER_ID);
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