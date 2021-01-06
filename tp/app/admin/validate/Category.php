<?php

namespace app\admin\validate;

use think\Validate;

class Category extends Validate
{
    // 需要验证的字段以及规则
    protected $rule = [
        'pid' => 'require|integer',
        'name' => 'require|chsDash|max:16',
        'id' => 'require|integer',
        'listorder' => 'require|integer',
        'status' => 'require|integer',

    ];

    // 错误提示
    protected $message = [
        'pid.require' => '父id必须',
        'pid.integer' => '父id必须是整数',
        'name.require' => '分类名必须',
        'name.chsDash' => '分类名必须是汉字、字母、数字和下划线_及破折号',
        'name.max' => '分类名必须在16个字以内',
        'id.require' => 'id必须',
        'id.integer' => 'id必须是整数',
        'listorder.require' => '排序格式必须',
        'listorder.integer' => '排序格式必须是整数',
        'status.require' => '状态码必须',
        'status.integer' => '状态码必须是整数',
    ];


    // 设置验证场景
    protected $scene = [
        // 新增分类时的场景
        'category' => ['pid', 'name'],
        // 列表修改排序场景
        'listorder' => ['id','listorder'],
        // 修改分类状态场景
        'status' => ['id','status'],
        // 点击编辑显示场景
        'id' => ['id'],
        // 点击编辑修改场景
        'update' => ['id','pid', 'name'],
    ];

}


