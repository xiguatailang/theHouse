<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Enumeration\App;
use Illuminate\Support\Facades\Redis;
use App\Incl\Business\Player;



class LoginConsumer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'LoginConsumer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'make user cache from list';

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
        while (true){
            $user_id = Redis::RPOP(App::LOGIN_POOL);
            if($user_id){
                $user_data = Redis::get(App::USER_LOGIN_KEY.'_'.$user_id);
                $user_data = json_decode($user_data ,true);
                if($user_data['packages']===null) {
                    $user_packages = Player::getUserPackages($user_id);
                    $user_messages = Player::getUserMessages($user_id);
                    $user_data['packages'] = $user_packages;
                    $user_data['messages'] = $user_messages;
                    Redis::set(App::USER_LOGIN_KEY.'_'.$user_id ,json_encode($user_data));
                }
            }else{
                echo 'no'."\n";
                sleep(10);
            }
        }
    }




}
