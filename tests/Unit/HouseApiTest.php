<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/7/17
 * Time: 下午7:19
 */
namespace Tests\Unit;

use App\Help\Algorithm;
use App\Incl\Business\Package;
use Tests\TestCase;
class HouseApiTest extends TestCase{

    private $user_id = 10000000000;
    private $reader = 10000000001;

    public function testInit(){

        $this->insertUserPackageList();
//        $this->getUserPackageList();
//        $this->getProperUserPackage();
//        $this->insertUserMessageOutbox();
//        $this->getUserMessageOutbox();
//        $this->getUserMessageInbox();
//        $this->readerUserMessage();
//        $this->makeSecret();
//        $this->decodeSecret();
//        $this->tokenEncrypt();
        $this->tokenDecrypt();
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

    public function getUserMessageInbox(){
        $data = Package::getUserMessageInbox($this->reader);
        echo "\n".json_encode($data);
    }

    public function getUserMessageOutbox(){
        $data = Package::getUserMessageOutbox($this->user_id);
        echo "\n".json_encode($data);
    }

    public function insertUserMessageOutbox(){
        $content = 'hi,i am a meesage from writer';
        $data = Package::insertUserMessageOutbox($this->user_id ,$content ,$this->reader ,6);
        echo "\n".json_encode($data);
    }

    public function readerUserMessage(){
        $data = Package::setUserMessagePool($this->user_id ,$this->reader ,0);
        echo "\n".json_encode($data);
    }

    public function makeSecret(){
        $id = 1000000000;
        echo "\n".base64_encode($id);
    }

    public function decodeSecret(){
        $cont = 'MzEzMTAwMDAwMDAwMDA4MDg=';
        echo "\n".base64_decode($cont);
    }

    public function tokenEncrypt(){
        $data = Algorithm::tokenEncrypt($this->user_id);
        echo "\n".$data;
    }

    public function tokenDecrypt(){
        $token = 'MAEzMTAwMDAwMDAwMAADAMNZZM4MD';
        $data = Algorithm::tokenDecrypt($token);
        echo "\n".$data;
    }

}