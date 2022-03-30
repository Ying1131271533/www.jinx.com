<?php
namespace app\admin\controller;

class Admin extends Base
{
    public function index()
    {
        return 'admin';
    }

    public function info()
    {
        return input();
    }

    public function akali()
    {
        return '阿卡丽';
    }

    public function detail()
    {
        return '详细信息';
    }

    public function update()
    {
        return '修改';
    }

    public function delete()
    {
        return '删除';
    }
}
