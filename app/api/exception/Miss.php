<?php
namespace app\api\exception;

class Miss extends BaseException
{
    public $msg    = '未找到数据';
    public $code   = '404';
    public $status = '10024';
}
