<?php
namespace app\api\exception;

class Fail extends BaseException
{
    public $msg    = 'Fail';
    public $code   = '400';
    public $status = '10020';
}
