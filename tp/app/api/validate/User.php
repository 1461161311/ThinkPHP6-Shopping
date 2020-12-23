<?php
namespace app\api\validate;

use think\Validate;

/**
 * 验证手机号与用户名是否为空
 * Class User
 * @package app\api\validate
 */
class User extends Validate{
    protected $rule = [
        'username' => 'require',
        'phoneNumber' => 'require',
    ];

    protected $message = [
        'username' => '用户名必须',
        'phoneNumber' => '电话号码必须',
    ];

    protected $scene = [
        'send_code' => ['phone_number'],
    ];

}


