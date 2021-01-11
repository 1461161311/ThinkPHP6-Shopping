<?php

namespace app\admin\validate;

use think\Validate;

class Specs extends Validate
{
    // 需要验证的字段以及规则
    protected $rule = [
        'id' => 'require|integer',
        'name' => 'require|chsDash|max:16',
        'status' => 'require|integer',
    ];

    // 错误提示
    protected $message = [
        'id.require' => 'id必须',
        'id.integer' => 'id必须是整数',
        'name.require' => '规格名必须',
        'name.chsDash' => '规格名必须是汉字、字母、数字和下划线_及破折号',
        'name.max' => '规格名必须在16个字以内',
        'status.require' => '状态码必须',
        'status.integer' => '状态码必须是整数',
    ];


    // 设置验证场景
    protected $scene = [
        // 添加规格时的场景
        'id' => ['id'],
        'save' => ['name'],
        'status' => ['id','status'],
        'updateSave' => ['id','name'],
    ];

}


