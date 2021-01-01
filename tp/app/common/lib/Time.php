<?php

namespace app\common\lib;

class Time
{
    /**
     * 根据数据库 type 参数来生成 token 的生效时间
     * @param int $type
     * @return float|int
     */
    public static function userLoginExpiresTime($type = 2)
    {
        // 如果传入的 type 值不在数据库设置的值的范围中，则给出默认值2
        $type = !in_array($type, [1, 2]) ? 2 : $type;
        // type = 1 时，设置7天有效期
        if ($type == 1) {
            $day = $type * 7;
        // type = 2 时，设置30天有效期
        } elseif ($type == 2) {
            $day = $type * 30;
        }
        // 有效期 * 一天的秒数 = token 的有效时间
        return $day * 24 * 3600;
    }
}