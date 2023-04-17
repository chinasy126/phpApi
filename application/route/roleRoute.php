<?php

use think\Route;



Route::post('role/list', 'api/controllers.RoleController/getListOfData');
Route::post('role/modify', 'api/controllers.RoleController/modify');
Route::post('role/insert', 'api/controllers.RoleController/insert');
Route::delete('role/delete', 'api/controllers.RoleController/delete');

Route::post('role/rolelist', 'api/controllers.RoleController/rolelist');
