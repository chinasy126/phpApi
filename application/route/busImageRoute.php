<?php
use think\Route;

Route::resource(':version/busimages','api/:version.busImages');

Route::post(':version/busImages/upload','api/:version.busImages/uploadImg');