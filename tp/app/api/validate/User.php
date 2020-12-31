<?php

namespace app\api\validate;

use think\Validate;

/**
 * 验证手机号与用户名是否为空
 * Class User
 * @package app\api\validate
 */
class User extends Validate
{
    // 需要验证的字段以及规则
    protected $rule = [
        // require 必须的, number 必须是数字, min 长度不能低于, in 类型只能为 1、2
        'username' => 'require',
        'phoneNumber' => 'require',
        'code' => 'require|number|min:4',
        'type' => 'require|in:1,2',
        'sex' => 'require|in:0,1,2'
    ];

    // 错误提示
    protected $message = [
        'username' => '用户名必须',
        'phoneNumber' => '电话号码必须',
        'code.require' => '短信验证码必须',
        'code.number' => '短信验证码必须为数字',
        'code.min' => '短信验证码不能低于4位',
        'type.require' => '类型必须',
        'type.in' => '类型数值错误',
        'sex.require' => '性别必须',
        'sex.in' => '性别数值错误',
    ];


    // 设置验证场景
    protected $scene = [
        // 只验证手机号
        'send_code' => ['phone_number'],
        // 验证手机号，验证码，类型
        'login' => ['phone_number','code','type'],
        // 更新用户个人信息
        'update_user' => ['username','sex'],
    ];

}


