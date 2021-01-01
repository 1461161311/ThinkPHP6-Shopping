<?php
/**
 * 存放业务状态码
 */

return [
    // 成功
    "success" => 1,
    // 失败
    "error" => 2,
    // 方法未找到
    "way_not_found" => 3,
    // 控制器未找到
    "controller_not_found" => 4,

    // mysql 相关的状态配置
    "mysql" => [
        "table_normal" => 1,    // 正常
        "table_pedding" => 2,   // 待审
        "table_delete" => 99,   // 删除
    ],

    // 前端登录错误提示
    "code_not" => -1009,
    // 用户未登录
    "not_login" => -1

];
