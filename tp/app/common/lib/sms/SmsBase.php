<?php
declare(strict_types=1);

namespace app\common\lib\sms;
/**
 * 工厂模式，当使用多个第三方验证码发送平台时使用
 * Interface SmsBase
 * @package app\api\common\lib\sms
 */
interface SmsBase
{
    public static function sendCode(string $phone, int $code);
}