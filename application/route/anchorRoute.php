<?php
use think\Route;

Route::resource(':version/anchor','api/:version.anchor');
Route::post(':version/anchor/list','api/:version.anchor/list');
Route::post(':version/anchor/reguser','api/:version.anchor/regUser');
Route::post(':version/anchor/login','api/:version.anchor/login');


Route::get(':version/wechart/get/userinfo','api/:version.wechart/getUserInfo');
Route::post(':version/wechart/update/userinfo','api/:version.wechart/updateUserInfo');
// 收藏
Route::post(':version/wechart/collection/goods','api/:version.wechart/collectionGoods');
//  是否收藏
Route::post(':version/wechart/get/iscollect','api/:version.wechart/isCollGood');
// 收藏列表
Route::post(':version/wechart/get/selectedlist','api/:version.wechart/selectedGoodsList');

// 所有商品
Route::post(':version/wechart/allgoodslist','api/:version.wechart/allGoodsList');
// 获取所有供应商列表

Route::post(':version/wechart/allsupplierlist','api/:version.wechart/allSupplierList');






Route::post(':version/wechartother/get/all/goods','api/:version.wechOther/getAllGoods');
Route::post(':version/wechartother/good/detail','api/:version.wechOther/goodDetail');



