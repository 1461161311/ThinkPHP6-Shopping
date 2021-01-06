<?php

namespace app\common\lib;

class Status
{
    /**
     * 获取配置文件中的 status 值
     * @return mixed
     */
    public static function getTableStatus()
    {
        $status = config("status.mysql");
        // array_values() 返回指定数组的 value 值
        return array_values($status);
    }
}