<?php
namespace app\api\middleware;

use app\BaseController;

class AutoLogin
{
    public function handle($request, \Closure $next)
    {
        // 添加中间件执行代码
        $this->index();
        return $next($request);
    }

    // 自动登录
    protected function index()
    {
        echo '自动登录';return;
    }

}
