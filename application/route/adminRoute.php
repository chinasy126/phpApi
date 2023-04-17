<?php
use think\Route;

Route::resource(':version/admin','api/:version.admin');   //注册一个资源路由，对应restful各个方法
Route::post(':version/admin/adminlist','api/:version.admin/adminlist');   //注册一个资源路由，对应restful各个方法

Route::post(':version/admin/deleteadmin','api/:version.admin/deleteAdmin');