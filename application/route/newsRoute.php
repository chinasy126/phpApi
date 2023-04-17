<?php
use think\Route;


Route::post('news/list','api/controllers.NewsController/getListOfData');
Route::post('news/saveOrUpdate','api/controllers.NewsController/saveOrUpdate');
Route::delete('news/delete','api/controllers.NewsController/deleteNews');
Route::post('news/export','api/controllers.NewsController/exportNews');
Route::post('news/import','api/controllers.NewsController/importNews');

Route::delete('news/batchDelete','api/controllers.NewsController/newsBatchDelete');
