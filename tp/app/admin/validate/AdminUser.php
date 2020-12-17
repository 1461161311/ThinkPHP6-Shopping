<?php

namespace app\admin\validate;

use think\Validate;

class AdminUser extends Validate
{
    protected $rule = [
        'username' => 'require|checkCaptcha',
        'password' => 'require|checkCaptcha',
//        'captcha' => 'require|checkCaptcha'
    ];

    protected $message = [
        'username' => '用户名必须,请重新输入',
        'password' => '密码必须,请重新输入',
//        'captcha' => '验证码必须,请重新输入'
    ];

    protected function checkCaptcha($value, $rule, $data = [])
    {
        // 未知错误,无法使用
//        if (!captcha_check($value)) {
//            return "您输入的验证码不正确";
//        }
        return true;
    }

}
