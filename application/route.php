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
use think\Route;

// 登录路由
require 'route/loginRoute.php';
require 'route/newsRoute.php';

require 'route/RoleRoute.php';
// 角色路由
require 'route/menuRoute.php';
// 用户
require 'route/userRoute.php';
// 图片上传
require 'route/uploadRoute.php';
// 产品管理
require 'route/productRoute.php';
// 产品分类
require 'route/productClassRoute.php';




Route::miss('Error/index');

//Route::resource(':version/login','api/:version.Login');   //注册一个资源路由，对应restful各个方法


return [
    '__pattern__' => [
        'name' => '\w+',
    ],
];
