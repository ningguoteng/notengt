<?php
declare (strict_types = 1);

namespace app\listener;

use think\Request;
use think\facade\Session;

class UserLogin
{
    /**
     * 事件监听处理
     * @return mixed
     */
    public function handle($user,Request $request)
    {
        $param = $request->param();
        $is_add =  $user::where([['user_name', '=', $param['user_name']],['status','=',1]])->findOrEmpty();
        if ($is_add->isEmpty()){
            return response(101, '账号不存在，请先注册');
        }else{
            $password = build_password($param['password']);
            if ($password == $is_add['password']){
                Session::set('user_id',$is_add['id']);
                return response(200, '登录成功');
            }else{
                return response(103, '用户名或密码不正确');
            }
        }
    }
}
