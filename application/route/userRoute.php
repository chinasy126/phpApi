<?php
use think\Route;

Route::rule(':version/user/info','api/:version.User/info'); //restful方法中另外一个方法等。。。
Route::rule(':version/user/nav','api/:version.User/nav'); //restful方法中另外一个方法等。。。
Route::post('user/logout','api/controllers.Login/logout');
Route::post('user/list', 'api/controllers.UserController/userList');
Route::post('user/insert', 'api/controllers.UserController/userInsert');
Route::delete('user/delete', 'api/controllers.UserController/userDelete');

Route::post('user/batchdelete', 'api/controllers.UserController/userBatchDelete');
Route::post('user/modify', 'api/controllers.UserController/userModify');

Route::post('user/compasssame', 'api/controllers.UserController/comPassSame');

Route::post('user/updatepassword', 'api/controllers.UserController/updatePassword');

