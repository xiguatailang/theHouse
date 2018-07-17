<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/7/17
 * Time: 下午7:19
 */
namespace Tests\Unit;

use App\Incl\Business\Package;
use Tests\TestCase;
class HouseApiTest extends TestCase{

    private $user_id = 10000000000;

    public function testInit(){

//        $this->insertUserPackageList();
//        $this->getUserPackageList();
        $this->getProperUserPackage();

    }


    public function getProperUserPackage(){

        $data = Package::getProperUserPackage();
        echo "\n".json_encode($data);
    }


    public function getUserPackageList(){

        $data = Package::getUserPackageList($this->user_id);
        echo "\n".json_encode($data);
    }

    public function insertUserPackageList(){
        $content = 'hello world,i came from 10000000000';
        $data = Package::insertUserPackageList($this->user_id ,$content);
        echo "\n".json_encode($data);
    }

}