<?php
declare(strict_types=1);

namespace app\common\business;

use app\common\lib\sms\SubSms;
use app\common\lib\Num;

class Sms
{
    /**
     * business层调用生成验证码以及验证发送验证码
     * @param string $phoneNumber 手机号
     * @param int $len 验证码个数
     * @param string $type 调用哪个第三方接口，默认Sub
     * @return bool
     */
    public static function sendCode(string $phoneNumber, int $len, $type = 'sub'): bool
    {
        // 静态调用lib下面的类库生成随机短信验证码
        $code = Num::getCode($len);

        // 调用lib层的发送验证码功能，并将手机号和生成的验证码传过去
//        $sms = SubmailSms::sendCode($phoneNumber, $code);

        // 工厂模式
        // ucfirst() 将首字母转为大写
        $type = ucfirst($type);
        // 拼接路径，sms\\ 第二个斜杆是转义
        $class = "app\common\lib\sms\\".$type."Sms";
        // 调用方法
        $sms = $class::sendCode($phoneNumber, $code);

        // 判断是否发送成功
        if ($sms) {
            // 发送成功需要把短信验证码记录到redis中，并且给出生效时间，如：该验证码一分钟失效
            cache(config("redis.code_pre") . $phoneNumber, $code, config("redis.code_expire"));
        }

        // 发送失败
        return $sms;
    }


}

