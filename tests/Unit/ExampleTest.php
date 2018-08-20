<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Redis;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        for ($i=1;$i<=1000000;$i++){
            $cache_key = 'multy_'.$i;
            $data = $this->makeParams($i);
            if(!Redis::set($cache_key,json_encode($data))){
                echo $data['name'].' faluire'."\n";
            }
        }

        echo 'all done'."\n";
//        $this->assertTrue(true);
    }

    public function makeParams($offset){
        $params = [
            'player_id'=>'1000000000'.$offset,
            'name'=>'test'.$offset,
            'package'=>[
                [
                    'created_at'=>1529979277,
                    'content'=>'I want to thank you for what you have done since purchasing Castle Age - Creating the daily roll for non IOS accounts, opening up storage - so we do not have to constantly search for space to trade runes, creating exciting events to participate in, introducing Legendary Generals with wonderful new skills.',
                    'read_count'=>0,
                ],
                [
                    'created_at'=>1529979277,
                    'content'=>'I want to thank you for what you have done since purchasing Castle Age - Creating the daily roll for non IOS accounts, opening up storage - so we do not have to constantly search for space to trade runes, creating exciting events to participate in, introducing Legendary Generals with wonderful new skills.',
                    'read_count'=>0,
                ],
                [
                    'created_at'=>1529979277,
                    'content'=>'I want to thank you for what you have done since purchasing Castle Age - Creating the daily roll for non IOS accounts, opening up storage - so we do not have to constantly search for space to trade runes, creating exciting events to participate in, introducing Legendary Generals with wonderful new skills.',
                    'read_count'=>0,
                ],
                [
                    'created_at'=>1529979277,
                    'content'=>'I want to thank you for what you have done since purchasing Castle Age - Creating the daily roll for non IOS accounts, opening up storage - so we do not have to constantly search for space to trade runes, creating exciting events to participate in, introducing Legendary Generals with wonderful new skills.',
                    'read_count'=>0,
                ],
                [
                    'created_at'=>1529979277,
                    'content'=>'I want to thank you for what you have done since purchasing Castle Age - Creating the daily roll for non IOS accounts, opening up storage - so we do not have to constantly search for space to trade runes, creating exciting events to participate in, introducing Legendary Generals with wonderful new skills.',
                    'read_count'=>0,
                ],
                [
                    'created_at'=>1529979277,
                    'content'=>'I want to thank you for what you have done since purchasing Castle Age - Creating the daily roll for non IOS accounts, opening up storage - so we do not have to constantly search for space to trade runes, creating exciting events to participate in, introducing Legendary Generals with wonderful new skills.',
                    'read_count'=>0,
                ],
                [
                    'created_at'=>1529979277,
                    'content'=>'I want to thank you for what you have done since purchasing Castle Age - Creating the daily roll for non IOS accounts, opening up storage - so we do not have to constantly search for space to trade runes, creating exciting events to participate in, introducing Legendary Generals with wonderful new skills.',
                    'read_count'=>0,
                ],
                [
                    'created_at'=>1529979277,
                    'content'=>'I want to thank you for what you have done since purchasing Castle Age - Creating the daily roll for non IOS accounts, opening up storage - so we do not have to constantly search for space to trade runes, creating exciting events to participate in, introducing Legendary Generals with wonderful new skills.',
                    'read_count'=>0,
                ],
            ],
            'message'=>[
                'read'=>[
                    [
                        'read_time'=>1529979277,
                        'message_offset'=>1,
                        'writer'=>1000000001,
                    ],
                    [
                        'read_time'=>1529979277,
                        'message_offset'=>2,
                        'writer'=>1000000001,
                    ],
                    [
                        'read_time'=>1529979277,
                        'message_offset'=>1,
                        'writer'=>1000000001,
                    ],
                    [
                        'read_time'=>1529979277,
                        'message_offset'=>2,
                        'writer'=>1000000001,
                    ],
                    [
                        'read_time'=>1529979277,
                        'message_offset'=>1,
                        'writer'=>1000000001,
                    ],
                    [
                        'read_time'=>1529979277,
                        'message_offset'=>2,
                        'writer'=>1000000001,
                    ],
                    [
                        'read_time'=>1529979277,
                        'message_offset'=>1,
                        'writer'=>1000000001,
                    ],
                    [
                        'read_time'=>1529979277,
                        'message_offset'=>2,
                        'writer'=>1000000001,
                    ],
                ],
                'write'=>[
                    [
                        'write_time'=>1529979277,
                        'content'=>'This battle problem started approximately 2 years ago, it has gotten progressively worse. I have been a player since the very beginning, before guilds, before monsters - There was NEVER this problem before - only a rare trip. It was suggested in a forum thread the problem could be a faulty/missing index. I certainly am no programmer, but it is something to look into.',
                        'reader'=>100000000,
                    ],
                    [
                        'write_time'=>1529979277,
                        'content'=>'This battle problem started approximately 2 years ago, it has gotten progressively worse. I have been a player since the very beginning, before guilds, before monsters - There was NEVER this problem before - only a rare trip. It was suggested in a forum thread the problem could be a faulty/missing index. I certainly am no programmer, but it is something to look into.',
                        'reader'=>100000000,
                    ],
                    [
                        'write_time'=>1529979277,
                        'content'=>'This battle problem started approximately 2 years ago, it has gotten progressively worse. I have been a player since the very beginning, before guilds, before monsters - There was NEVER this problem before - only a rare trip. It was suggested in a forum thread the problem could be a faulty/missing index. I certainly am no programmer, but it is something to look into.',
                        'reader'=>100000000,
                    ],
                    [
                        'write_time'=>1529979277,
                        'content'=>'This battle problem started approximately 2 years ago, it has gotten progressively worse. I have been a player since the very beginning, before guilds, before monsters - There was NEVER this problem before - only a rare trip. It was suggested in a forum thread the problem could be a faulty/missing index. I certainly am no programmer, but it is something to look into.',
                        'reader'=>100000000,
                    ],
                    [
                        'write_time'=>1529979277,
                        'content'=>'This battle problem started approximately 2 years ago, it has gotten progressively worse. I have been a player since the very beginning, before guilds, before monsters - There was NEVER this problem before - only a rare trip. It was suggested in a forum thread the problem could be a faulty/missing index. I certainly am no programmer, but it is something to look into.',
                        'reader'=>100000000,
                    ],
                    [
                        'write_time'=>1529979277,
                        'content'=>'This battle problem started approximately 2 years ago, it has gotten progressively worse. I have been a player since the very beginning, before guilds, before monsters - There was NEVER this problem before - only a rare trip. It was suggested in a forum thread the problem could be a faulty/missing index. I certainly am no programmer, but it is something to look into.',
                        'reader'=>100000000,
                    ],
                    [
                        'write_time'=>1529979277,
                        'content'=>'This battle problem started approximately 2 years ago, it has gotten progressively worse. I have been a player since the very beginning, before guilds, before monsters - There was NEVER this problem before - only a rare trip. It was suggested in a forum thread the problem could be a faulty/missing index. I certainly am no programmer, but it is something to look into.',
                        'reader'=>100000000,
                    ],
                    [
                        'write_time'=>1529979277,
                        'content'=>'This battle problem started approximately 2 years ago, it has gotten progressively worse. I have been a player since the very beginning, before guilds, before monsters - There was NEVER this problem before - only a rare trip. It was suggested in a forum thread the problem could be a faulty/missing index. I certainly am no programmer, but it is something to look into.',
                        'reader'=>100000000,
                    ],
                    [
                        'write_time'=>1529979277,
                        'content'=>'This battle problem started approximately 2 years ago, it has gotten progressively worse. I have been a player since the very beginning, before guilds, before monsters - There was NEVER this problem before - only a rare trip. It was suggested in a forum thread the problem could be a faulty/missing index. I certainly am no programmer, but it is something to look into.',
                        'reader'=>100000000,
                    ],

                ],
            ],
        ];


       return $params;

    }

}
