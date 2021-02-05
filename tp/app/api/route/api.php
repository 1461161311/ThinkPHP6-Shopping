<?php
use think\facade\Route;

// 路由设置
Route::rule("smscode","sms/code","POST");
Route::resource('user','User');
Route::rule("lists","mall.lists/index");
Route::rule("subcategory/:id","category/sub");
Route::rule("detail/:id","mall.detail/index");

Route::resource("order","order.index");
