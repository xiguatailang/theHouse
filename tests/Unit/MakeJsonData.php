<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/6/28
 * Time: 下午5:50
 */

namespace Tests\Unit;


use Tests\TestCase;

class MakeJsonData extends TestCase
{
    public function testRun(){
        $data = array(
            'user_id'=>123456,
            'authority'=>1,
            'read_info'=>[
                [
                    'user_id'=>23344,
                    'time'=>time()
                ]
            ],
            'created_at'=>time(),
        );

        echo json_encode($data);
    }

}