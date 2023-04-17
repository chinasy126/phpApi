<?php
use think\Route;


Route::post('upload/pictures',
    'api/controllers.Upload/uploadFile');

