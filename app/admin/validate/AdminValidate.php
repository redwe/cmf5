<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\validate;

use think\Validate;

class AdminValidate extends Validate
{
    protected $rule = [
        'username' => 'require|unique:user,username',
        'password'  => 'require',
        'nickname'  => 'require'
        //'user_email' => 'require|email|unique:user,user_email',
    ];
    protected $message = [
        'username.require' => '用户不能为空',
        'nickname.unique'  => '用户名已存在',
        'password.require'  => '密码不能为空',
        //'user_email.require' => '邮箱不能为空',
        //'user_email.email'   => '邮箱不正确',
       // 'user_email.unique'  => '邮箱已经存在',
    ];

    protected $scene = [
        'add'  => ['username', 'password', 'nickname'],
        'edit' => ['username', 'nickname'],
    ];
}