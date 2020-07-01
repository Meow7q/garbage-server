<?php

namespace App\Console\Commands;

use App\Service\Dictinonary\PopularSearchService;
use App\Service\Logger;
use Illuminate\Console\Command;

class RefreshPopularSearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refresh:polular_list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'refresh popoluar search every 5 mimutes';

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
        //
        $rs = (New PopularSearchService())->refreshList();
//        Logger::getLogger('popular_search_list')->info(json_encode($rs));
    }


}
