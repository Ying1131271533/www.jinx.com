<?php

namespace app\api\validate;

use think\Validate;

class User extends Validate
{
    // 验证规则
    protected $rule = [
        'user_name|用户名'   => 'require',
        'password|密码'     => 'require|length:32',
        'code|验证码'        => 'require|length:6',
        'id|用户id'         => 'require|number',
        'avatar|头像'       => 'require|image|fileSize:2097152|fileExt:jpg,png,bmp,jpeg,gif',
        'newpassword|新密码' => 'require|length:32|different:password',
        'repassword|重复密码' => 'require|confirm:newpassword',
        'phone|手机号'       => ['require', 'mobile'],
        'email|邮箱'        => 'require|email',
        'nickname|昵称'     => 'require|unique:user|chsDash',
    ];

    // 验证消息
    protected $message = [
        'avatar.fileSize'       => '图片内存不能大于2M',
        'newpassword.different' => '原密码和新密码不能一致',
        'repassword.confirm'    => '两次密码不一致',
        'phone.regex'           => '手机号码格式不正确',
        'phone.unique'          => '该手机号已被使用！',
        'email.unique'          => '该邮箱已被使用！',
        'nickname.unique'       => '该昵称已被使用！',
    ];

    // 验证场景
    protected $scene = [
        'login'         => ['user_name', 'password'],
        'register'      => ['user_name', 'password', 'code'],
        'upload_avatar' => ['id', 'avatar'],
        'change_pwd'    => ['user_name', 'password', 'newpassword', 'repassword'],
        // 因为有不需要验证的字段规则，所以需要使用下面的 sceneFind_pwd() 验证场景定义
        // 'find_pwd'      => ['user_name', 'code', 'newpassword', 'repassword'],
        // 'bind_phone'    => ['id', 'code', 'phone'],
        // 'bind_email'    => ['id', 'code', 'email'],
        'bind_username' => ['id', 'code', 'user_name'],
        'nickname'      => ['id', 'nickname'],
    ];

    // find_pwd 验证场景定义
    public function sceneFind_pwd()
    {
        return $this->only(['user_name', 'code', 'newpassword', 'repassword'])
            ->remove('newpassword', 'different');
    }

    // bind_phone 验证场景定义
    public function sceneBind_phone()
    {
        return $this->only(['id', 'code', 'phone'])->append('phone', 'unique:user');
    }

    // bind_email 验证场景定义
    public function sceneBind_email()
    {
        return $this->only(['id', 'code', 'email'])->append('email', 'unique:user');
    }
}
