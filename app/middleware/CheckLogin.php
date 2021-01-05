<?php
declare (strict_types = 1);

namespace app\middleware;

use think\facade\Session;

class CheckLogin
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        // 判定用户是否登录
        if(!Session::has('user_id')){

            return redirect(request()->domain().'/login/index');
        }
        return $next($request);
    }
}
