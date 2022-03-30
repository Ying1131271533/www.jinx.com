<?php
namespace app\api\exception;

class Unauthorized extends BaseException
{
    public $msg    = '未经授权';
    public $code   = '401';
    public $status = '10031';
}
