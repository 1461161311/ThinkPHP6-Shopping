<?php
declare(strict_types=1);

namespace app\common\business;

use app\common\lib\sms\SubmailSms;

class Sms
{
    public static function sendCode(string $phoneNumber): bool
    {
        // 随口生成短信验证码 rand() 随机在两个值之间生成一个值
        $code = rand(0000, 9999);

        $sms = SubmailSms::sendCode($phoneNumber, $code);

        if ($sms) {
            // 需要把短信验证码记录到redis中，并且给出生效时间，如：该验证码一分钟失效
            cache(config("redis.code_pre") . $phoneNumber, $code, config("redis.code_expire"));
        }
        return $sms;
    }


}

