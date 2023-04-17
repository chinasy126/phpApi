<?php
use think\Route;


Route::post('product/list',
    'api/controllers.ProductController/productList');
Route::post('product/saveOrUpdate',
    'api/controllers.ProductController/saveOrUpdate');
Route::delete('product/delete',
    'api/controllers.ProductController/productDelete');
