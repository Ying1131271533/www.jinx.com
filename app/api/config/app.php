<?php
// +----------------------------------------------------------------------
// | 应用设置
// +----------------------------------------------------------------------

return [
    'page'           => 1,
    'number'         => 10,
    // 'exception_tmpl' => \think\facade\App::getAppPath() . '404.json',
    'exception_tmpl' => app()->getThinkPath() . 'tpl/think_exception.tpl', // tp默认模板
    'sign'           => 'Akali',
];
