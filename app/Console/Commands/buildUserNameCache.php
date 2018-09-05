<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/9/1
 * Time: 上午11:25
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Enumeration\App;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use App\Incl\Business\Supervisor;

class buildUserNameCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'buildUserNameCache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'make a hash user cache';


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
        $users = DB::select('select user_id,name from user ');
        if($users){
            foreach ($users as $user){
                Redis::HMSET(App::USER_NAME_POOL ,$user->user_id ,$user->name);
            }
        }

        var_dump('done');
    }

}