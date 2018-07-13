<?php

use Illuminate\Http\Request;

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
// 登录授权放在登录中间件外面不然不能登陆
Route::group(['middleware' => ['cors'], 'namespace' => 'ApiAuth'], function () {
    // 前台
    // 登录
    Route::post('/login', 'AuthenticateController@login');
    // 获取第三方登录信息
    Route::post('/saveLoginInfo', 'AuthenticateController@savaLogin');
    // 注册
    Route::post('/register', 'AuthenticateController@register');
    Route::get('/getArticel', 'ArticleController@index');
    Route::get('/getArticel/{id}', 'ArticleController@show');
    Route::get('/search/{keyWords}', 'SearchController@index');
    // 处理访问信息
    Route::post('/analysisInfo', 'RecordInformationController@analysisInfo');
    Route::post('/analysisIp', 'RecordInformationController@analysisIp');
    // 获取留言数据
    Route::get('/getLeavingMessage', 'LivingMessegeController@getLeavingMessage');
    // 如果已经登录会到这个里面
    Route::group(['middleware' => ['jwt.api', 'jwt.api.refresh']], function () {
        Route::get('/userInfo', 'AuthenticateController@getUserDetails');
        // 留言
        Route::post('/leavingMessage', 'LivingMessegeController@index');
        // 回复留言
        Route::post('/replyMessage', 'LivingMessegeController@replyMessage');
    });
});

Route::group(['middleware' => ['cors'], 'namespace' => 'ApiAuth'], function () {
    // 如果已经登录会到这个里面
    Route::group(['middleware' => ['jwt.api', 'jwt.api.refresh']], function () {
        // 后台文章管理
        Route::resource('/article', 'AdminArticleController');
    });
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
