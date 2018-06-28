<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/6/28
 * Time: 下午5:02
 */

namespace Tests\Feature;
use App\Incl\Business\Package;
use Tests\TestCase;


class HouseTest extends TestCase
{
    public function testRun(){
        $this->processLetters();
    }

    public function processLetters(){
        $userPackage = Package::get($user_id='');
        $userPackage->setCache();
    }


}