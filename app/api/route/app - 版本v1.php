<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;

Route::get('think', function () {
    return 'hello,ThinkPHP6!';
});

/**
 * 首页
 *
 * @return think\facade\Route;
 */
Route::rule('v1/', 'v1.index/index'); // 首页
Route::rule('v1/hello', 'v1.index/hello'); // hello

Route::rule('', 'index/index'); // 首页
Route::rule('hello', 'index/hello'); // hello

/**
 * 用户
 *
 * @return think\facade\Route;
 */
Route::get('v1/user', 'v1.user/index'); // 首页
Route::get('v1/user/:id', 'v1.user/info'); // 用户信息
Route::post('v1/user', 'v1.user/add'); // 添加用户
Route::put('v1/user', 'v1.user/update'); // 更新用户
Route::delete('v1/user/:id', 'v1.user/delete'); // 删除用户

// Route::get('test/[:id]/[:name]', 'user/test'); // 测试
/*Route::get('test-<id>-?<name?>', 'user/test'); // 测试*/
Route::get('test-[:id]-[:name]', 'user/test'); // 测试
// Route::get('test/[:id]-[:name]', 'user/test'); // 测试
Route::get('user', 'user/index'); // 首页
Route::get('user/:id', 'user/info'); // 用户信息
Route::post('user', 'user/add'); // 添加用户
Route::put('user', 'user/update'); // 更新用户
Route::delete('user/:id', 'user/delete'); // 删除用户
