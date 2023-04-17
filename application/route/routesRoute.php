<?php
use think\Route;

Route::resource(':version/routes','api/:version.routes');   //注册一个资源路由，对应restful各个方法
Route::rule(':version/routesmenu/permissions','api/:version.routes/getAllMenuPermissions'); // 获取所有列表，菜单以及所有权限
//Route::rule(':version/user/nav','api/:version.User/nav'); //restful方法中另外一个方法等。。。