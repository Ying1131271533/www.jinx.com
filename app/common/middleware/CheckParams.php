<?php
namespace app\common\middleware;

/**
 * 验证参数，参数过滤
 *
 * @param Request     $request
 * @param Closure     $next
 * @return return           $next($request);
 */
class CheckParams
{
    public function handle($request, \Closure $next)
    {
        //获取当前参数
        $params = $request->filter(['htmlspecialchars'])->all();
        // 验证请求是否超时
        // $this->check_time($params['time']);
        // 验证token（防止篡改数据）
        // $this->check_token($params);
        // 验证参数，参数过滤
        $this->check_param($params, $request->subDomain(), $request->controller(), $request->action());

        return $next($request);
    }

    /**
     * 验证参数，参数过滤
     *
     * @param array      $array         除time和token外的所有参数
     * @param string     $sub_domain    子域名
     * @param string     $controller    控制器
     * @param string     $scene         方法
     * @return return                   合格的参数数组
     */
    public function check_param(array $params, string $sub_domain, string $controller, string $scene)
    {
        // 拼接验证类名，注意路径不要出错
        $validateClassName = 'app\\' . $sub_domain . '\validate\\' . $controller;
        // 判断当前验证类是否存在
        if (class_exists($validateClassName)) {
            $validate = new $validateClassName;
            // 仅当存在验证场景才校验
            if ($validate->hasScene($scene)) {
                // 设置当前验证场景
                $validate->scene($scene);
                if (!$validate->check($params)) {
                    // 校验不通过则直接返回错误信息
                    echo json_encode(['code' => 400, 'msg' => $validate->getError()]);
                    exit;
                }
            }
        }
    }

    /**
     * 验证token（防止篡改数据）
     *
     * @param [array] $array    [全部请求数据]
     * @return [json_encode]           [token验证结果]
     */
    public function check_token(array $params)
    {
        /*****************   api传过来的token   *****************/
        if (!isset($params['token']) || empty($params['token'])) {
            echo json_encode(['code' => 400, 'msg' => 'token不能为空！']);
            exit;
        }

        $app_token = $params['token']; // api传过来的token

        /*****************   服务器即时生成的token   *****************/
        unset($params['token']);
        $service_token = '';
        foreach ($params as $key => $value) {
            $service_token .= md5($value);
        }
        $service_token = md5('Akali_' . $service_token . '_Akali');

        /*****************   对比token，返回结果   *****************/
        if ($app_token !== $service_token) {
            echo json_encode(['code' => 400, 'msg' => 'token值不正确！']);
            exit;
        }
    }

    /**
     * 验证请求是否超时
     *
     * @param [int] $time [时间戳参数]
     * @return [json_encode]    [检测结果]
     */
    public function check_time(int $time)
    {
        if (!isset($time) || intval($time) <= 1) {
            echo json_encode(['code' => 400, 'msg' => '时间戳不正确！']);
            exit;
        }

        if (time() - intval($time) > 60) {
            echo json_encode(['code' => 400, 'msg' => '请求超时！']);
            exit;
        }
    }
}
