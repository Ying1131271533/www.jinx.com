<?php
// 应用公共文件

/**
 * 返回api接口数据
 *
 * @param  string    $smg       描述信息
 * @param  int       $code      http状态码
 * @param  int       $status    程序状态码
 * @param  notype    $data      返回的数据
 * @return json                 api返回的json数据
 */
function show(string $msg, int $code = 200, int $status = 10000, $data = [])
{
    // 组装数据
    $resultData = [
        'status' => $status,
        'msg'    => $msg,
        'data'   => $data,
    ];
    // 返回数据
    return json($resultData, $code);
}

if (!function_exists('html_dcode')) {
    function html_dcode($str)
    {
        return htmlspecialchars_decode($str);
    }
}

// 递归找子分类
function get_child($data = [], $pid = 0)
{
    $temp = [];
    foreach ($data as $key => $value) {
        if ($value['pid'] == $pid) {
            $child                             = get_child($data, $value['id']);
            !empty($child) and $value['child'] = $child;
            $temp[]                            = $value;
        }
    }

    return $temp;
}

// 加密解密函数
if (!function_exists('encrypt')) {
    function encrypt($string, $operation, $key = '')
    {
        $key           = md5($key);
        $key_length    = strlen($key);
        $string        = $operation == 'D' ? base64_decode($string) : substr(md5($string . $key), 0, 8) . $string;
        $string_length = strlen($string);
        $rndkey        = $box        = array();
        $result        = '';
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($key[$i % $key_length]);
            $box[$i]    = $i;
        }
        for ($j = $i = 0; $i < 256; $i++) {
            $j       = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp     = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a       = ($a + 1) % 256;
            $j       = ($j + $box[$a]) % 256;
            $tmp     = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if ($operation == 'D') {
            if (substr($result, 0, 8) == substr(md5(substr($result, 8) . $key), 0, 8)) {
                return substr($result, 8);
            } else {
                return '';
            }
        } else {
            return str_replace('=', '', base64_encode($result));
        }
    }

}

function return_msg(int $code, string $msg, $data = [])
{
    return json(['code' => $code, 'msg' => $msg, 'data' => $data]);
}
