<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2019/7/30
 * Time: 11:50
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Model\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * 获取分类信息
     * @param Request $request
     * @return mixed
     */
    public function getAllCategory(Request $request){
        $data =  Category::select('category_id', 'category_name')->get();
        return $this->success($data, 200);
    }
}