<?php
use think\Route;

Route::resource(':version/routeslist','api/:version.routesList');   //注册一个资源路由，对应restful各个方法