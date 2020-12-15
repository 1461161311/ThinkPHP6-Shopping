<?php
namespace app\admin\controller;

use think\captcha\facade\Captcha;

/**
 * Class Verify
 * @package app\admin\controller
 * 自定义验证码
 */
class Verify{
    public function index()
    {
        return Captcha::create();
    }

}
