<?php

namespace app\common\lib;

class Str
{
    /**
     * 生成登录所需的 token
     * @param $string
     * @return string
     */
    public static function getLoginToken($string)
    {
        // 生成一个不会重复的字符串
        $str = md5(uniqid(md5(microtime(true)), true));
        // 加密
        $token = sha1($str . $string);
        // 返回
        return $token;
    }
}
