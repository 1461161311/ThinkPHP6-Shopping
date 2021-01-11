<?php

namespace app\admin\validate;

use think\Validate;

class SpecsValue extends Validate
{
    // 需要验证的字段以及规则
    protected $rule = [
        'id' => 'require|integer',
        'specs_id' => 'require|integer',
        'name' => 'require|chsDash|max:16',
        'status' => 'require|integer',
    ];

    // 错误提示
    protected $message = [
        'id.require' => 'id必须',
        'id.integer' => 'id必须是整数',
        'specs_id.require' => 'specs_id必须',
        'specs_id.integer' => 'specs_id必须是整数',
        'name.require' => '规格名必须',
        'name.chsDash' => '规格名必须是汉字、字母、数字和下划线_及破折号',
        'name.max' => '规格名必须在16个字以内',
        'status.require' => '状态码必须',
        'status.integer' => '状态码必须是整数',
    ];


    // 设置验证场景
    protected $scene = [
        // 添加规格时的场景
        'save' => ['specs_id','name'],
        'status' => ['id'],
    ];

}


