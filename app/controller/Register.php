<?php
declare (strict_types = 1);

namespace app\controller;

use app\model\Type;
use think\Request;
use app\validate\UserValidate;
use app\model\User;
use think\exception\ValidateException;

class Register
{
    /**
     *注册页面
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function index(Request $request){
        try {
            validate(UserValidate::class)->batch(true)->check($request->param());
            $user = new User();
            $param = $request->param();
            $is_add =  $user::where([['user_name', '=', $param['user_name']],['status','=',1]])->findOrEmpty();
            if ($is_add->isEmpty()){
                $param['password'] = build_password($param['password']);
                $user->save($param);
                //新建第一个文件夹
                $Type = new Type();
                $Type->saveAll([
                    [
                        'title'=>'我的笔记',
                        'parent_id'=>0,
                        'user_id'=>$user->id,
                    ],
                    [
                        'title'=>'回收站',
                        'parent_id'=>0,
                        'user_id'=>$user->id,
                    ]
                ]);
                response(200, '恭喜您，注册成功');
            }else{
                response(101, '账号已存在，请直接登录');
            }

        } catch (ValidateException $e) {
            // 验证失败 输出错误信息
            foreach ($e->getError() as $v){
                response(102, $v);
            }

        }


    }




}