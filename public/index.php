<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
namespace think;

require __DIR__ . '/../vendor/autoload.php';

define('HTTP_ERROR_MSG', '错误'); // 接口错误信息
define('HTTP_FAIL_MSG', '失败'); // 接口失败信息
define('HTTP_EMPTY_MSG', '暂无数据~~'); // 接口没有资源信息
define('HTTP_SUCCESS_MSG', '成功'); // 接口成功信息

// 执行HTTP应用并响应
$http = (new App())->http;

$response = $http->run();

$response->send();

$http->end($response);
