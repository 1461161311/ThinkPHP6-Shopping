<?php
use think\facade\Route;

// 路由设置
Route::rule("smscode","sms/code","POST");
Route::resource('user','User');

