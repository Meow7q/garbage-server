<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2019/8/2
 * Time: 11:29
 */

namespace App\Service\Dictinonary;


use App\Model\SearchHistory;
use App\Service\Logger;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PopularSearchService
{
    /**
     * @return mixed
     */
    public function refreshList(){
        $time = time()-24*3600;
        $rs = SearchHistory::select([DB::raw('any_value(id) as id'),'keyword',DB::raw('any_value(category_id) as category_id'),DB::raw('any_value(category_name) as category_name'),DB::raw('count(*) as sum')])
            ->groupBy('keyword')
            ->orderBy('sum', 'desc')
            ->whereDate('created_at', '>=', date('Y-m-d', $time))
            ->limit(5)
            ->get();
        $rs = $rs->toArray();
        if($rs){
            //生成假热度
            foreach ($rs as $k=>$v){
                $rs[$k]['sum'] = (5-$k)*mt_rand(4012,5123)+$v['sum'];
            }
            Cache::store('redis')->put('popular_search_list', json_encode($rs), 5);
        }
        return $rs;
    }
}