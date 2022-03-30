<?php
namespace app\api\controller\v1;

use think\Db;
use think\Request;

class User extends Base
{
    public function index(Request $request)
    {
        $resultData = [
            'id'   => 1,
            'name' => '阿卡丽',
        ];

        $code = 200;
        $msg  = '获取资源成功';
        // return xml(['code' => $code, 'msg' => $msg, 'data' => $resultData]);
        return json(['code' => $code, 'msg' => $msg, 'data' => $resultData, 'md5' => md5(md5(md5('akali')))]);
    }

    public function info(Request $request)
    {
        return '用户信息';
    }

    public function add()
    {
        return json(input());
    }

    public function update()
    {
        return '修改';
    }

    public function delete($id)
    {
        return json($id);
    }
}
