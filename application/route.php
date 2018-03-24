<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//官方的默认配置路由写法
//return [
//    '__pattern__' => [
//        'name' => '\w+',
//    ],
//    '[hello]'     => [
//        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
//        ':name' => ['index/hello', ['method' => 'post']],
//    ],
//
//];

//我们最好采用动态注册路由的示例
//use think\Route;
//Route::rule('路由表达式'，‘路由地址’，‘请求类型’，‘路由参数（数组）’，‘变量规则（数组）’)；
//请求类型：
//GET , POST , DELETE , PUT , *（默认会被设置为*，任意类型都支持，但是这种不太好。）
//Route::rule('hello','sample/Test/hello','GET|POST',['https'=>false]);
//或者其他写法有Route::get('hello','sample/Test/hello');
//Route::post();
//Route::any();
//Route::post('hello/:id','sample/Test/hello');

use think\Route;

Route::get('api/:version/banner/:id', 'api/:version.Banner/getBanner');

Route::get('api/:version/theme', 'api/:version.Theme/getSimpleList');

Route::get('api/:version/theme/:id', 'api/:version.Theme/getComplexOne');

Route::get('api/:version/product/recent', 'api/:version.Product/getRecent');
Route::get('api/:version/product/by_category', 'api/:version.Product/getAllInCategory');
Route::get('api/:version/product/:id', 'api/:version.Product/getOne',[], ['id'=>'\d+']);

//对上面换种写法
//Route::group('api/:version/product', function(){
//    Route::get('/by_category','api/:version.Product/getAllInCategory');
//    Route::get('/:id', 'api/:version.Product/getOne',[], ['id'=>'\d+']);
//    Route::get('/recent', 'api/:version.Product/getRecent');
//});

Route::get('api/:version/category/all', 'api/:version.Category/getAllCategories');

// Token
Route::post('api/:version/token/user', 'api/:version.Token/getToken');

//Address
Route::post('api/:version/address', 'api/:version.Address/createOrUpdateAddress');


//Order
Route::post('api/:version/order', 'api/:version.Order/placeOrder');
Route::get('api/:version/order/:id', 'api/:version.Order/getDetail',
    [], ['id'=>'\d+']);
Route::get('api/:version/order/by_user', 'api/:version.Order/getSummaryByUser');


Route::post('api/:version/pay/pre_order', 'api/:version.Pay/getPreOrder');
Route::post('api/:version/pay/notify', 'api/:version.Pay/receiveNotify');
Route::post('api/:version/pay/re_notify', 'api/:version.Pay/redirectNotify');

//Route::get('api/:version/second', 'api/:version.Address/second');
//Route::get('api/:version/third', 'api/:version.Address/third');
