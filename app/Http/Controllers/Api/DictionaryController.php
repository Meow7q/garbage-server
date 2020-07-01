<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2019/7/23
 * Time: 11:57
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Model\Dictionary;
use App\Model\Garbage;
use App\Model\SearchHistory;
use App\Service\Dictinonary\PopularSearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DictionaryController extends Controller
{
    /**
     * 查询
     * @param Request $request
     * @return mixed
     */
    public function serach(Request $request){
        $keyword = $request->input('keyword');
        if(!$keyword){
            return $this->message('搜索内容不能为空！', 0);
        }
//        $search_rs = Garbage::where('name', 'like', '%'.$keyword.'%')
//            ->get();
        //使用ngram全文搜索
        $search_rs = DB::select("SELECT * FROM garbage WHERE MATCH (name) AGAINST ('".$keyword."' IN NATURAL LANGUAGE MODE) limit 20");

        //如果搜素结果存在的话加入搜索历史
        if($search_rs){
            SearchHistory::create([
                'keyword' => $search_rs[0]->name,
                'category_id' => $search_rs[0]->category_id,
                'category_name' => $search_rs[0]->category_name,
            ]);
        }
        return $this->success($search_rs,200);
    }

    /**
     * 热门搜索
     * @param Request $request
     * @return mixed
     */
    public function getPopularSearch(Request $request){
        $popularSearchList = Cache::store('redis')->get('popular_search_list');
        if($popularSearchList){
            return $this->success(json_decode($popularSearchList), 200);
        }
        $new_data = (New PopularSearchService())->refreshList();
        return $this->success($new_data,200);
    }
}