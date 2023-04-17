<?php
use think\Route;


Route::post('user/login','api/controllers.Login/signIn');
Route::post('user/verificationcode','api/controllers.Login/verificationcode');
Route::get('user/info','api/controllers.Login/userInfo');
Route::post('user/nav','api/controllers.Login/nav');
Route::post('user/logout','api/controllers.Login/logout');
