<?php
declare(strict_types=1);

namespace app\common\business;

use app\common\lib\sms\SubmailSms;
use app\common\lib\Num;

class Sms
{
    public static function sendCode(string $phoneNumber, int $len): bool
    {
        // 静态调用lib下面的类库生成随机短信验证码
        $code = Num::getCode($len);

        // 调用lib层的发送验证码功能，并将手机号和生成的验证码传过去。返回值为bool
        $sms = SubmailSms::sendCode($phoneNumber, $code);

        // 判断是否发送成功
        if ($sms) {
            // 发送成功需要把短信验证码记录到redis中，并且给出生效时间，如：该验证码一分钟失效
            cache(config("redis.code_pre") . $phoneNumber, $code, config("redis.code_expire"));
        }

        // 发送失败
        return $sms;
    }


}

