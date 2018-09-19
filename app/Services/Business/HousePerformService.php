<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/6/28
 * Time: 上午10:28
 */
namespace App\Services\Business;
use App\Help\HouseValid;
use App\Incl\Business\Package;
use App\Incl\Business\Player;
use App\Services\BaseService;
use App\Enumeration\App;
use Illuminate\Support\Facades\Redis;



class HousePerformService extends BaseService{

    private static $self = null;

    public static function instance(){
        if(self::$self==null){
            self::$self = new self();
        }

        return self::$self;
    }


    public function getUserInfo(){
        $data = Player::get();
        unset($data['token']);
        return array('data'=>$data);
    }

    /**
     * 进入前端，查询未读取的信息条数
     * @return int
     */
    public function getNewMessages(){
        $user_id = Player::getUserId();
        $count = 0;
        if(!Player::isUserInitCache($user_id)){
            sleep(App::LOGIN_SYNC_MAKE_USER_CACHE_TIME + 1);
        }
        $user_data = Player::get();
        if($user_data['messages']!==null) {
            $count = $user_data['not_read'];
        }

        return array('data'=>$count);
    }

    /**
     *
     */
    public function writeUserPackage(){
        //对content内容做校验
        //插入package List
        //插入 package pool
        $user_id = Player::getUserId();
        $validContent = HouseValid::validContent($_REQUEST['content']);
        if(!$validContent['result']){
            return array('code'=>App::BUSINESS_EXCEPTION_CODE ,'msg'=>$validContent['msg']);
        }

        if(Package::insertUserPackageList($user_id ,$validContent['content'] ,$_REQUEST['type'])){
            return array('code'=>App::BUSINESS_SUCCESS_CODE ,'msg'=>'Success');
        }

        return array('code'=>App::BUSINESS_EXCEPTION_CODE ,'msg'=>'save failure');
    }

    public function getProperPackage(){
        //获取一个读取次数最少的package
        $data = Player::readProperPackageSort();

        return array('data'=>$data);
    }


    public function getProperPackageId(){

        $data = array();

        return array('data'=>$data);
    }


    public function writeMessage(){
        //写入 user_message_out_box
        //写入 target_user_message_inner_box
        $user_id = Player::getUserId();
        $validContent = HouseValid::validContent($_REQUEST['content']);
        if(!$validContent['result']){
            return array('code'=>App::BUSINESS_EXCEPTION_CODE ,'msg'=>$validContent['msg']);
        }
        $reader = $_REQUEST['target'];
        $offset = $_REQUEST['offset'];
        $data = Package::insertUserMessageOutbox($user_id,$validContent['content'],$reader,$offset ,$reader);
        return array('data'=>$data);
    }

     public function getUserMessage(){
         $user_id = Player::getUserId();
         $user_cache = Redis::get(App::USER_LOGIN_KEY.'_'.$user_id);
         $user_cache = json_decode($user_cache ,1);

//var_dump($user_cache['messages']);die;
        return array('data'=>$user_cache['messages']);
     }

     public function getPackageMessages(){
        $result = array();
        $package_id = isset($_REQUEST['package_id']) ? $_REQUEST['package_id'] : 0;
        $cache_data = Player::get();
        $messages = $cache_data['messages'];
        if(isset($messages[$package_id]) && isset($messages[$package_id]['dialogue'])){
            $result = $messages[$package_id]['dialogue'];
        }

         return array('data'=>$result);
     }


//     public function replyMessage(){
//
//         $user_id = Player::getUserId();
//         $validContent = HouseValid::validContent($_REQUEST['content']);
//         if(!$validContent['result']){
//             return array('code'=>App::BUSINESS_EXCEPTION_CODE ,'msg'=>$validContent['msg']);
//         }
//         $reader = $_REQUEST['reader'];
//         $packageOffset = $_REQUEST['package_offset'];
//         $package_owner = $_REQUEST['package_owner'];
//         $data = Package::insertUserMessageOutbox($user_id,$validContent['content'],$reader,$packageOffset ,$package_owner);
//         return array('data'=>$data);
//
//     }


}