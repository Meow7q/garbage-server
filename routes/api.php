<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route as Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace'=>'Api'], function (){
    Route::post('oauth/token', 'TokenController@getToken');
    Route::post('bd_token', 'TokenController@getTokenForBaidu');
});

Route::group(['namespace'=>'Api','middleware' => 'auth:api'] , function () {
    //更新用户信息
    Route::post('user', 'UserController@updateInfo');

    //获取所有分类
    Route::get('category', 'CategoryController@getAllCategory');

    //搜索
    Route::get('dictionary', 'DictionaryController@serach');

    //热门搜索
    Route::get('dictionary/popular_list', 'DictionaryController@getPopularSearch');

    //新增到审核列表
    Route::post('question', 'QuestionController@add');
    Route::get('question', 'QuestionController@getQuestionList');
    Route::get('question/{id}', 'QuestionController@getQuestionbyId');
    Route::post('question/vote', 'QuestionController@vote');
});

