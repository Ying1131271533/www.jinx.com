<?php
namespace app\api\exception;

class Success extends BaseException
{
    public $msg    = 'Success';
    public $code   = '200';
    public $status = '10000';
}
