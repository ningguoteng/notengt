<?php
declare (strict_types = 1);

namespace app\validate;

use think\Validate;

class UserValidate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'user_name'  => 'require|max:50',
        'password'  => 'require|confirm',
        'email'  => 'email',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [
        'user_name.require' => '用户名必填',
        'user_name.max' => '用户名不能超过50个字符',
        'password.require' => '密码必填',
        'password.confirm' => '两次密码输入不一致',
        'email'        => '邮箱格式错误',
    ];

}
