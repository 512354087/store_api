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


Route::group(['namespace' => 'User'],function(){
    Route::post('/wx_login','UserController@wx_login');
});


Route::group(['namespace' => 'Cate','middleware'=>['token']],function(){
    Route::resource('/cate','CateController');
    Route::get ('/keyword','CateController@keyword');
});

Route::group(['namespace' => 'Product','middleware'=>['token']],function(){
    Route::resource('/product','ProductController');
    Route::get('/discount','ProductController@discount');

});

Route::group(['namespace' => 'User','middleware'=>['token']],function(){
    Route::get('/user/{id}','UserController@show');
});



