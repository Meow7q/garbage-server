<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return view('welcome');
});

/**
 * 数据库表信息
 */
Route::match(['get', 'post'], '/database/{action}',function($action){
    $ctrl = \App::make(\App\Http\Controllers\Database\DatabaseController::class);
    return \App::call([$ctrl, $action]);
});

Route::get('/home', 'HomeController@index')->name('home');
