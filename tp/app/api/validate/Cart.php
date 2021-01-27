<?php

namespace app\api\validate;

use think\Validate;

/**
 * 验证
 * Class User
 * @package app\api\validate
 */
class Cart extends Validate
{
    // 需要验证的字段以及规则
    protected $rule = [
        'id' => 'require',
        'num' => 'require',
    ];

    // 错误提示
    protected $message = [
        'id' => 'id必须',
        'num' => '数量必须',
    ];


    // 设置验证场景
    protected $scene = [
        // 只验证手机号
        'id' => ['id', 'num'],
    ];

}


