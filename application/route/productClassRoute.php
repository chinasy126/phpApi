<?php
use think\Route;


Route::post('productclass/category',
    'api/controllers.ProductClassController/category');
Route::post('productclass/list',
    'api/controllers.ProductClassController/dataList');
Route::post('productclass/modify',
    'api/controllers.ProductClassController/modify');

Route::post('productclass/insert',
    'api/controllers.ProductClassController/insert');

Route::delete('productclass/delete',
    'api/controllers.ProductClassController/delete');
