<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Enumeration\App;
use Illuminate\Support\Facades\Redis;
use App\Incl\Business\Player;
use Illuminate\Support\Facades\DB;


class UpdateUserDataToDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UpdateUserDataToDb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update user cache data to db';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $user_ids = array();

        while (true){
            // 这里没有一次读取完毕批量操作，因为很可能批量读取操作完成后删除缓存。这个过程中如果有缓存写入。会丢失数据。队列操作则不会丢失数据
            $package = Redis::LPOP(Player::USER_PACKAGE_LIST);
            $message = Redis::LPOP(Player::USER_MESSAGE_LIST);

            if($package || $message){
                $suffix = date('Ym' ,time());

                if($package){
                    $package = json_decode($package ,true);

                    $insert = [
                        'user_id'=>$package['writer'],
                        'content'=>$package['content'],
                        'created_at'=>$package['created_at'],
                    ];
                    DB::table('packages_'.$suffix)->insert($insert);

                    $user_ids[] = $package['writer'];
                }

                if($message){
                    $message = json_decode($message ,true);

                    $insert = [
                        'package_id'=>$message['package_id'],
                        'writer'=>$message['writer'],
                        'reader'=>$message['reader'],
                        'content'=>$message['content'],
                        'write_at'=>$message['write_at'],
                    ];

                    DB::table('messages_' . $suffix)->insert($insert);
                    $user_ids[] = $message['writer'];
                    $user_ids[] = $message['reader'];
                }

                echo " done \n";

            }else{
                $user_ids = array_unique($user_ids);
                foreach ($user_ids as $user_id){
                    Player::makeUserCacheData($user_id);
                }

                $user_ids = array();
                echo 'no'."\n";
                sleep(App::MESSAGE_SYNC_WARING_TIME);
            }
        }
    }




}
