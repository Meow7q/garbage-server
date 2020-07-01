<?php

namespace App\Console\Commands;

use App\Service\Logger;
use Illuminate\Console\Command;

//运行 php artisan make:command start_test生成的此文件
class start_test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'start:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * 这里放要执行的东西
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
//        Logger::getLogger('Start-test')->info(time());
    }
}
