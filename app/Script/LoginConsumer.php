<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/8/14
 * Time: 下午5:33
 */

require_once '../Enumeration/App.php';
use App\Enumeration\App;

require_once '../../vendor/laravel/framework/src/Illuminate/Support/Facades/Facade.php';
require_once '../../vendor/laravel/framework/src/Illuminate/Support/Facades/Redis.php';
use Illuminate\Support\Facades\Redis;


$processName = 'LoginConsumer.php';

ini_set('memory_limit', '2048M');
$command = 'ps ax | grep ' . $processName . ' | grep -v grep |wc -l';
$result = exec($command);
if ($result > 1)
{
    exit;
}

while (true){
    $count = Redis::get(App::LOGIN_POOL);
    $count = null;
    if($count){
        echo 'has';
    }else{
        echo 'no';
        sleep(10);
    }
}