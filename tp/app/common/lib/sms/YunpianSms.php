<?php

declare(strict_types=1);

namespace app\common\lib\sms;

use GuzzleHttp\Exception\ServerException;
use think\facade\Log;
use yunpian_php_sdk\lib\SmsOperator;
use app\common\lib\sms\SmsBase;

class YunpianSms implements SmsBase
{
    public static function sendCode(string $phone, int $code): bool
    {
        // 加载第三方类库自动装载文件
        require_once 'E:\Code\Git-tp6\ThinkPHP6-Shopping_Project\tp\extend\yunpian_php_sdk\YunpianAutoload.php';
        // 判断手机号或验证码是否为空
        if (empty($phone) || empty($code)) {
            return false;
        }

        // 获取配置文件 api/config/Yunpian 中的配置
        $yunpian_config = [
            'APIKEY' => config("Yunpian.appkey"),
            'SMS_HOST' => config("Yunpian.server"),
            'VERSION' => config("Yunpian.VERSION"),
            'RETRY_TIMES' => config("Yunpian.RETRY_TIMES"),
            'API_SECRET' => config("Yunpian.API_SECRET"),
        ];

        try {
            // 配置信息
            $yunpian_config['URI_SEND_SINGLE_SMS'] = $yunpian_config['SMS_HOST'] . $yunpian_config['VERSION'] . "/sms/single_send.json";
            // 初始化 SmsOperator 类
            $smsOperator = new SmsOperator($yunpian_config);
            // 将用户信息存入数组
            $data['mobile'] = $phone;
            $data['text'] = '【1121】您的验证码是' . $code;
            // 调用发送接口
            $send = $smsOperator->single_send($data);
            //print_r($send);
            // 设置日志记录，将访问第三方接口记录在日志中，日志目录 runtime\api\log
            log::info("SubSms-sendCode-{$phone}-result" . json_encode($send));
        } catch (ServerException $exception) {
            // 设置日志记录，将访问第三方接口记录在日志中，日志目录 runtime\api\log
            log::error("SubSms-sendCode-{$phone}-ServerException" . $exception->getMessage());
            return false;
        }
        return true;
    }
}

