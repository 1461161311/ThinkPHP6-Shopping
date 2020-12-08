<?php

namespace app\controller;

class Error
{
    /**
     * 魔术方法 __call 当找不到控制器时，默认调用该方法
     * 使用 common 公共文件中的 show 方法进行 API 格式输出
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments)
    {
        return show(config("status.controller_not_found"), "找不到该控制器", null, 400);
    }

}