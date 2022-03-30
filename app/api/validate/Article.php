<?php

namespace app\api\validate;

use app\common\model\Article as A;
use think\Validate;

class Article extends Validate
{
    // 验证规则
    protected $rule = [
        'id|文章id'      => 'require|number',
        'user_id|用户id' => 'require|number',
        'title|文章标题'   => 'require|unique:article|max:50|chsDash',
        'number|每页个数'  => 'number',
        'page|页码'      => 'number',
    ];

    // 验证消息
    protected $message = [
        'title.unique' => '文章标题已被使用',
    ];

    // 验证场景
    protected $scene = [
        'save'   => ['user_id', 'title'],
        'index'  => ['user_id', 'number', 'page'],
        'read'   => ['id'],
        'update' => ['id', 'title'],
        'delete' => ['id'],
    ];
}
