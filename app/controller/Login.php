<?php
declare (strict_types = 1);

namespace app\controller;

use app\validate\UserValidate;
use think\exception\ValidateException;
use think\facade\Session;
use think\Request;
use think\facade\View;
use app\model\User;

class Login
{
    /**
     * 登录页面
     *
     * @return \think\Response
     */
    public function index()
    {
        //
        return View::fetch('index');
    }


    /**
     * 登录
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function login(Request $request)
    {
        try {
            $param = $request->param();
            validate(UserValidate::class)->batch(true)->rule([
                'user_name'=>'require',
                'password'=>'require'
            ])->check($param);
            $user = new User();
            // 触发登录事件执行登录逻辑
            event('UserLogin', $user);

        }catch (ValidateException $e ){
            // 验证失败 输出错误信息
            foreach ($e->getError() as $v){
                return response(102, $v);
            }
        }

    }

    /**
     * 退出登录
     *
     * @return \think\Response
     */
    public function logout()
    {
        //
        Session::delete('user_id');
        response(200, '退出成功');
    }


}
