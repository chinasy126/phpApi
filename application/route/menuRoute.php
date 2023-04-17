<?php
use think\Route;

Route::post('menu/multiclassclassification','api/controllers.MenuController/multiclassclassification');
Route::post('menu/menulist','api/controllers.MenuController/menulist');
Route::post('menu/category','api/controllers.MenuController/category');
Route::post('menu/getSecMenuList','api/controllers.MenuController/getSecMenuList');
Route::post('menubutton/indertBtn','api/controllers.MenuController/insertBtn');
Route::post('menubutton/delete','api/controllers.MenuController/deleteBtn');
Route::post('menu/saveOrUpdate','api/controllers.MenuController/saveOrUpdate');
Route::delete('menu/delete','api/controllers.MenuController/menudelete');
Route::delete('menu/batchdelete','api/controllers.MenuController/batchdelete');
Route::post('menu/batch','api/controllers.MenuController/batchInsertMenu');
