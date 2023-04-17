<?php
use think\Route;

Route::resource(':version/register','api/:version.register');   //注册一个资源路由，对应restful各个方法
//Route::post(':version/admin/register','api/:version.admin/register');