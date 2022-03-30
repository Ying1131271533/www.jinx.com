<?php
declare (strict_types = 1);
namespace app\api\controller;

use app\common\model\User as U;
use lib\Crypt;
use lib\Jwttoken;

class User extends Base
{
    /**
     * 用户注册
     *
     * @return json    api返回的json数据
     */
    public function register()
    {
        /*****************   接收参数   *****************/
        $data = $this->params;

        /*****************   检查验证码   *****************/
        $this->check_code($data['user_name'], $data['code']);

        /*****************   检测用户名   *****************/
        $user_type_name = $this->check_username($data['user_name']);
        $this->check_exist($data['user_name'], $user_type_name, 0);

        /*****************   将用户信息写入数据库   *****************/
        unset($data['user_name']);
        $id = U::create($data)->getData('id');
        if (!$id) {
            $this->return_msg(400, '用户注册失败！');
        } else {
            $this->return_msg(200, '用户注册成功！', $id);
        }
    }

    /**
     * 用户登录
     *
     * @return json    api返回的json数据
     */
    public function login()
    {
        /*****************   接收参数   *****************/
        $data = $this->params;

        /*****************   检测用户名   *****************/
        $user_name_type = $this->check_username($data['user_name']);
        $this->check_exist($data['user_name'], $user_name_type, 1);

        /*****************   查询数据库   *****************/
        $user = U::where('email|phone', $data['user_name'])
            ->where('password', $data['password'])
            ->find();
        empty($user) and $this->return_msg(400, '用户名或者密码不正确');

        /*****************   获取密钥   *****************/
        $key = Crypt::key();

        /*****************   生成token   *****************/
        $token = Jwttoken::createJwt([
            'id'        => $user['id'],
            'user_name' => $user[$user_name_type],
        ], $key['privatekey'], 14400);

        /*****************   返回api接口数据   *****************/
        $resultData = [
            'id'        => $user['id'],
            'user_name' => $data['user_name'],
            'publickey' => $key['publickey'],
            'token'     => $token,
        ];
        $this->return_msg(200, '登录成功！', $resultData);
    }

    /**
     * 用户上传头像
     *
     * @return json    api返回的json数据
     */
    public function upload_avatar()
    {
        /*****************   接收参数   *****************/
        $data = $this->params;

        /*****************   上传文件获取路径   *****************/
        $avatar_path = $this->upload_file($data['avatar'], 'avatar');

        /*****************   存入数据库   *****************/
        $result = U::update(['id' => $data['id'], 'avatar' => $avatar_path]);
        if ($result) {
            $this->return_msg(200, '头像上传成功!', $avatar_path);
        } else {
            $this->return_msg(400, '头像上传失败!');
        }
    }

    /**
     * 用户修改密码
     *
     * @return json     api返回的json数据
     */
    public function change_pwd()
    {
        // 接收参数
        $data = $this->params;

        // 检查用户名并取出数据库中的用户密码
        $user_name_type = $this->check_username($data['user_name']);
        switch ($user_name_type) {
            case 'phone':
                $this->check_exist($data['user_name'], $user_name_type, 1);
                $where['phone'] = $data['user_name'];
                break;
            case 'email':
                $this->check_exist($data['user_name'], $user_name_type, 1);
                $where['email'] = $data['user_name'];
                break;
        }

        // 判断原始密码是否正确
        $password = U::where($where)->value('password');
        if ($password !== $data['password']) {
            $this->return_msg(400, '原密码错误！');
        }

        // 把新的密码存入数据库
        $result = U::where($where)->update(['password' => $data['newpassword']]);
        if ($result !== false) {
            $this->return_msg(200, '密码修改成功！');
        } else {
            $this->return_msg(400, '密码修改失败！');
        }
    }

    /**
     * 用户修改密码
     *
     * @return json     api返回的json数据
     */
    public function find_pwd()
    {
        // 接收参数
        $data = $this->params;

        // 检测验证码
        $this->check_code($data['user_name'], $data['code']);

        // 检测用户名
        $user_name_type = $this->check_username($data['user_name']);
        switch ($user_name_type) {
            case 'phone':
                $this->check_exist($data['user_name'], 'phone', 1);
                $where['phone'] = $data['user_name'];
                break;
            case 'email':
                $this->check_exist($data['user_name'], 'email', 1);
                $where['email'] = $data['user_name'];
                break;
        }

        // 修改数据库用户密码
        $result = U::where($where)->update(['password' => $data['newpassword']]);
        if ($result !== false) {
            $this->return_msg(200, '密码修改成功！');
        } else {
            $this->return_msg(400, '密码修改失败！');
        }
    }

    /**
     * 用户绑定手机号
     *
     * @return json     api返回的json数据
     */
    public function bind_phone()
    {
        /*****************   接收参数   *****************/
        $data = $this->params;

        /*****************   检查验证码   *****************/
        $this->check_code($data['phone'], $data['code']);

        /*****************   修改数据库   *****************/
        $result = U::where('id', $data['id'])->update(['phone' => $data['phone']]);
        if ($result !== false) {
            $this->return_msg(200, '手机号绑定成功！');
        } else {
            $this->return_msg(400, '手机号绑定失败！');
        }
    }

    /**
     * 用户绑定邮箱
     *
     * @return json     api返回的json数据
     */
    public function bind_email()
    {
        /*****************   接收参数   *****************/
        $data = $this->params;

        /*****************   检查验证码   *****************/
        $this->check_code($data['email'], $data['code']);

        /*****************   修改数据库   *****************/
        $result = U::where('id', $data['id'])->update(['email' => $data['email']]);
        if ($result !== false) {
            $this->return_msg(200, '邮箱绑定成功！');
        } else {
            $this->return_msg(400, '邮箱绑定失败！');
        }
    }

    /**
     * 用户绑定邮箱/手机号 这个虽然合并了 但还是不要比较好
     *
     * @return json     api返回的json数据
     */
    public function bind_username()
    {
        /*****************   接收参数   *****************/
        $data = $this->params;

        /*****************   检查验证码   *****************/
        $this->check_code($data['user_name'], $data['code']);

        /*****************   获取用户名类型   *****************/
        $user_name_type = $this->check_username($data['user_name']);
        switch ($user_name_type) {
            case 'phone':
                $type_text   = '手机号';
                $update_data = ['phone' => $data['user_name']];
                break;
            case 'email':
                $type_text   = '邮箱';
                $update_data = ['email' => $data['user_name']];
                break;
        }

        /*****************   修改数据库   *****************/
        $result = U::where('id', $data['id'])->update($update_data);
        if ($result !== false) {
            $this->return_msg(200, $type_text . '绑定成功！');
        } else {
            $this->return_msg(400, $type_text . '绑定失败！');
        }
    }

    /**
     * 用户设定昵称
     *
     * @return json     api返回的json数据
     */
    public function nickname()
    {
        /*****************   接收参数   *****************/
        $data = $this->params;

        /*****************   修改数据库   *****************/
        $result = U::where('id', $data['id'])->update(['nickname' => $data['nickname']]);
        if ($result !== false) {
            $this->return_msg(200, '昵称修改成功！');
        } else {
            $this->return_msg(400, '昵称修改失败！');
        }
    }

}
