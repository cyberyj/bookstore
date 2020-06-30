<?php
/**
 * Created by PhpStorm.
 * UserValidate: yingjun
 * Date: 2019-01-27
 * Time: 12:41
 */

namespace app\user\validate;

use think\Validate;

class UserValidate extends Validate
{
    protected $rule = [
        'name' => 'require|max:25',
        'password' => 'require|max:16|min:6|confirm',
        'phone' => 'require|mobile',
        'email' => 'require|email',
        'address' => 'require'
    ];
    protected $message = [
        'name.require' => 0,//用户名不能为空
        'name.max' => 1,//用户名不能超过25个字符
        'password.require' => 2,//密码不能为空
        'password.max' => 3,//密码最多16位
        'password.min' => 4,//密码最少6位
        'password.confirm' => 5,//两次密码不一致
        'phone' => 6,//联系方式格式错误
        'email' => 7,//邮箱格式错误
        'address' => 8,//地址不能为空
    ];
}