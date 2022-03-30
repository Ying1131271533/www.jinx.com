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

/**
 * 首页 测试用
 *
 */
// rsa 加密解密
Route::post('akali', 'index/akali');
Route::post('jinx', 'index/jinx');
Route::get('crypt/get_key', 'crypt/get_key');
Route::get('excel', 'index/excel');

/**
 * 验证码
 *
 */
// 发送验证码 手机/邮箱
Route::get('get_code/:time/:token/:username/:is_exist', 'code/get_code')->pattern(['username' => '.+']);

/**
 * 用户
 *
 */
// 登录
Route::post('login', 'user/login');
// Route::post('login', 'user/login')->middleware(app\api\middleware\AutoLogin::class);
// 注册
Route::post('register', 'user/register');
// 用户上传头像
Route::post('user/upload_avatar', 'user/upload_avatar');
// 用户修改密码
Route::post('user/change_pwd', 'user/change_pwd');
// 用户找回密码
Route::post('user/find_pwd', 'user/find_pwd');
// 用户绑定手机号
Route::post('user/bind_phone', 'user/bind_phone');
// 用户绑定邮箱
Route::post('user/bind_email', 'user/bind_email');
// 用户绑定邮箱/手机号 这个虽然合并了 但还是不要比较好
Route::post('user/bind_username', 'user/bind_username');
// 用户设定昵称
Route::post('user/nickname', 'user/nickname');

/**
 * 文章
 *
 */
// 资源路由 暂时不要
// Route::resource('article', 'Article');
// 新增新闻
Route::post('article', 'article/save');
// 查看文章列表
Route::get('articles/:time/:token/:user_id/[:number]/[:page]', 'article/index');
// 查看单个文章信息
Route::get('article/:time/:token/:id', 'article/read');
// 修改/保存文章
Route::put('article', 'article/update');
// 删除文章
Route::delete('article/:time/:token/:id', 'article/delete');
