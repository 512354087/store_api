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


/**
 * 用户模块
 */


/**
 * 测试队列
 */

Route::get('/test','Order\OrderController@test');


Route::group(['namespace' => 'User'],function(){
    Route::post('/wx_login','UserController@wx_login');
});
Route::group(['namespace' => 'User','middleware'=>['token']],function(){
    Route::resource('/user_address','UserAddressController');  //收货地址相关
    Route::get('/user/{id}','UserController@show');   //用户信息相关
    Route::put('/user/{id}','UserController@edit');
    Route::resource('/user_point','UserPointController');    //积分模块相关
    Route::resource('/user_star','UserStarController');    //用户收藏相关
    Route::delete('/un_star/{product_id}/{user_id}','UserStarController@unStar');   //取消收藏
});

/**
 * 分类模块
 */
Route::group(['namespace' => 'Cate','middleware'=>['token']],function(){
    Route::resource('/cate','CateController');
    Route::get ('/keyword','CateController@keyword');
});

/**
 *  订单模块
 */
Route::group(['namespace' => 'Order','middleware'=>['token']],function(){
    Route::resource('/order','OrderController');
});



/**
 *   积分模块
 */
Route::group(['namespace' => 'Product','middleware'=>['token']],function(){
    Route::get('/discount','ProductController@discount');

});


/**
 * 通用的接口
 */
Route::group(['namespace' => 'Address'],function(){
    Route::resource('/address','AddressController');
});





/**
 * Fnb 分类
 */

Route::group(['namespace' => 'Fnb'],function(){
    Route::resource('/fnb/category','CategoryController');

});




/**
 * 购物车模块
 */

Route::group(['namespace' => 'ShoppingCart','middleware'=>['token']],function(){
    Route::resource('/shopping_cart','ShoppingCartController');  //购车相关
});
