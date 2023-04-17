<?php
use think\Route;

Route::resource(':version/good','api/:version.good');
Route::post(':version/good/list','api/:version.good/goodList');
Route::get(':version/good/store/info','api/:version.good/storeInfo');
Route::post(':version/good/store/save','api/:version.good/saveStroeInfo');
Route::post(':version/good/allgoods','api/:version.good/allgoods');
Route::post(':version/good/head/choose','api/:version.good/headChoose');
Route::get(':version/good/anchor/list','api/:version.good/anchorList');
Route::post(':version/good/choose/list','api/:version.good/chooseList');
Route::post(':version/good/choose/delete','api/:version.good/chooseDelete');
Route::post(':version/good/selected','api/:version.good/selected');

Route::post(':version/good/mail/list','api/:version.good/mailList');
Route::post(':version/good/save/mailgood','api/:version.good/saveMailGood');
Route::post(':version/good/delete/mailgood','api/:version.good/deleteMailGood');
