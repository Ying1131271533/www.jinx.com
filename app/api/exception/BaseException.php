<?php
namespace app\api\exception;

use think\Exception;

abstract class BaseException extends Exception
{
    public $msg    = 'Fail';
    public $code   = 201;
    public $status = 10001;
    public $data   = [];

    public function __construct($params = [])
    {
        if (!is_array($params) || empty($params)) {
            return;
        }

        if (array_key_exists('msg', $params)) {
            $this->msg = $params['msg'];
        }

        if (array_key_exists('code', $params)) {
            $this->code = $params['code'];
        }

        if (array_key_exists('status', $params)) {
            $this->status = $params['status'];
        }

        if (array_key_exists('data', $params)) {
            $this->data = $params['data'];
        }
    }
}
