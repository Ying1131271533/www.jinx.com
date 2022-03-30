<?php

namespace app\api\validate;

use think\Validate;

class Code extends Validate
{
    // 验证规则
    protected $rule = [
        'username|用户名' => 'require',
        'is_exist|密码'  => 'require|number|length:1',
    ];

    // 验证场景
    protected $scene = [
        'get_code' => ['username', 'is_exist'],
    ];

}
