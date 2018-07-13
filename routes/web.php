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
    return response()->json(['error'=>'没有访问权限', 'code'=>'401000'], 401, ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
});
Route::get('/axios', function () {
    return view('axios');
});
//Route::get('/loginApi', 'AuthenticateController@login');