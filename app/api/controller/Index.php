<?php
namespace app\api\controller;

use app\common\model\User as U;
use lib\Crypt;
use lib\Excel;
use think\facade\Cache;

class Index extends Base
{
    public function index()
    {
        return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:) </h1><p> ThinkPHP V' . \think\facade\App::version() . '<br/><span style="font-size:30px;">14载初心不改 - 你值得信赖的PHP框架</span></p><span style="font-size:25px;">[ V6.0 版本由 <a href="https://www.yisu.com/" target="yisu">亿速云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=64890268" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="ee9b1aa918103c4fc"></think>';
    }

    public function hello($name = 'ThinkPHP6')
    {
        return 'hello!,' . $name;
    }

    public function akali()
    {
        // 接收参数
        $params = $this->params;

        // 验签
        $signResult = Crypt::verify(config('app.sign'), $params['sign'], $params['publickey']);
        empty($signResult) and $this->create(400, '验签不通过！');

        // 获取解密数据
        $cryptData = Crypt::decrypt($params);
        empty($cryptData) and $this->create(400, '数据解密失败！');

        // 找出该用户
        $user = U::field('id, nickname, create_time')->where('email|phone', $cryptData['username'])->find();
        empty($user) and $this->create(400, '该用户不存在！');

        // 获取加密数据返回用户端
        $resultData = Crypt::encrypt($user->toArray(), $params['web_privatekey']);
        empty($resultData) and $this->create(400, '数据加密失败！');

        $this->create(200, '成功', $resultData);
    }

    //导出
    public function excel()
    {
        // 设置表格的表头数据
        $header = ["A1" => "编号", "B1" => "姓名", "C1" => "年龄"];
        // 假设下面这个数组从数据库查询出的二维数组
        $data = [
            [1,'张三',18],
            [2,'李四',19],
            [3,'王五',22],
            [4,'赵六',19],
            [5,'李梅',17]
        ];
        //也可从数据库里查值
        // $data=Db::name('数据库名')->select()->toArray();
        // 保存文件的类型
        $type= false;
        // 设置下载文件保存的名称
        $fileName = '信息导出'.time();
        // 调用方法导出excel
        Excel::export($header,$type,$data,$fileName);
    }
}
