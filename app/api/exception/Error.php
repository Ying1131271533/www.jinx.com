<?php
namespace app\api\exception;

// 好像不需要，程序出错就油由 系统自带的 \Exception 来处理
class Error extends BaseException
{
    public $msg    = 'Error';
    public $code   = '500';
    public $status = '10040';
}
