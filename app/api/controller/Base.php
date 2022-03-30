<?php
declare (strict_types = 1);

namespace app\api\controller;

use app\common\model\User as U;
use think\exception\HttpResponseException;
use think\facade\Cache;
use think\facade\Validate;
use think\Request;
use think\Response;

abstract class Base
{
    protected $request; // 请求信息对象
    protected $validate; // 验证器对象
    protected $params; // 过滤后符合要求的参数

    /*****************   初始化   *****************/
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->params  = $this->request->filter(['htmlspecialchars'])->all();
    }

    /**
     * 检测用户名并返回用户名类别
     *
     * @param  string    $username   用户名，可能是邮箱，也可能是手机号
     * @return string                检测结果
     */
    public function check_username(string $username)
    {
        /*****************   判断是否为邮箱   *****************/
        $is_email = Validate::is($username, 'email') ? 1 : 0;

        /*****************   判断是否为手机号   *****************/
        $is_phone = Validate::is($username, 'mobile') ? 4 : 2;
		
        /*****************   最终结果   *****************/
        $flag = $is_email + $is_phone;
        switch ($flag) {
            /*****************   not email not phone  *****************/
            case 2:
                $this->return_msg(400, '邮箱或手机号格式不正确！');
                break;
            /*****************   is email  not phone  *****************/
            case 3:
                return 'email';
                break;
            /*****************   not email  is phone  *****************/
            case 4:
                return 'phone';
                break;
        }
    }

    /**
     * 检测手机号/邮箱是否应该存在于数据库中
     *
     * @param  string    $value      手机号/邮箱的值
     * @param  string    $type       phone/email
     * @param  int       $exist      手机号/邮箱是否应该存在于数据库中：0 否 1 是
     * @return json                  返回检测结果，通过时不处理，不通过时返回信息
     */
    public function check_exist(string $value, string $type, $exist)
    {
        $type_num = $type == 'phone' ? 2 : 4;
        $flag     = $type_num + $exist;
        $result   = U::where($type, $value)->find();
        switch ($flag) {
            /*****************   2+0 phone need no exist   *****************/
            case 2:
                if ($result) {
                    $this->return_msg(400, '此手机已被占用！');
                }
                break;
            /*****************   2+1 phone need exist   *****************/
            case 3:
                if (!$result) {
                    $this->return_msg(400, '此手机未注册！');
                }
                break;
            /*****************   4+0 email need no exist   *****************/
            case 4:
                if ($result) {
                    $this->create(400, '此邮箱有已被占用！');
                }
                break;
            /*****************   4+1 email need exist   *****************/
            case 5:
                if (!$result) {
                    $this->return_msg(400, '此邮箱未注册！');
                }
        }
    }

    /**
     * 检测验证码
     *
     * @param  srting   $username   用户名
     * @param  int      $code       验证码
     * @return josn                 检测结果
     */
    public function check_code($username, $code)
    {
        /*****************   获取缓存类型   *****************/
        $cache = Cache::store('code');

        /*****************   检测是否超时   *****************/
        $last_time = $cache->get($username . '_last_send_time');
        if ($last_time - time() > 300) {
            $this->return_msg(400, '验证超时请在五分钟内验证！');
        }

        /*****************   检测验证码是否正确   *****************/
        $md5_code = md5($username) . '_' . md5($code);
        if ($cache->get($username . '_code') != $md5_code) {
            $this->return_msg(400, '验证码不正确！');
        }

        /*****************   不管正确与否，每个验证码只验证一次   *****************/
        $cache->delete($username . '_code');
    }

    /**
     * 上传文件
     *
     * @param  file         $file          文件对象
     * @param  srting       $type          上传文件类型
     * @return string                      返回文件路径
     */
    public function upload_file($file, $type = '')
    {
        // 上传到本地服务器
        $save_name = \think\facade\Filesystem::disk('uploads')->putFile($type, $file);
        if ($save_name) {
            $path = '/uploads/' . $save_name;
            // 裁剪图片
            if (!empty($type)) {
                // 暂时不做缩略图
                // $this->image_edit($path, $type);
            }
            // 返回文件路径
            return str_replace('\\', '/', $path);
        } else {
            $this->return_msg(400, $file->getError());
        }
    }

    /**
     * 修改图片尺寸 200x200
     *
     * @param  srting       $path          文件路径
     * @param  srting       $type          上传文件类型
     * @return string                      返回处理后的图片路径
     */
    public function image_edit($path, $type)
    {
        $image_path = app()->getRootPath() . 'public' . $path;
        $image      = Image::open($image_path);
        switch ($type) {
            case 'avatar':
                $image->thumb(200, 200, Image::THUMB_CENTER)->save($image_path);
                break;
        }
    }

    /**
     * api 数据返回
     *
     * @param [int]         $code   [状态码 200：正常/4**：数据问题/5**：服务器问题]
     * @param [srting]      $msg    [接口返回的提示信息]
     * @param [无类型]       $data   [接口返回的数据]
     * @return [json]               [最终的json数据]
     */
    public function return_msg(int $code, $msg = '', $data = [])
    {
        /*****************   组合数据   *****************/
        $result = [
            // 状态码
            'code' => $code,
            // 消息
            'msg'  => $msg,
            // 返回数据
            'data' => $data,
        ];

        /*****************   返回接口数据，并中止脚本   *****************/
        echo json_encode($result);
        exit;
    }

    protected function create(int $code = 200, string $msg, $data = [], string $type = 'json'): Response
    {
        $result = [
            // 状态码
            'code' => $code,
            // 消息
            'msg'  => $msg,
            // 返回数据
            'data' => $data,
        ];

        // 返回api接口
        $response = Response::create($result, $type);
        throw new HttpResponseException($response);
    }
}
