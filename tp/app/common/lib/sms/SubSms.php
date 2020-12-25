<?php
// 严格模式
declare(strict_types=1);

namespace app\common\lib\sms;

use GuzzleHttp\Exception\ServerException;
use SUBMAIL_PHP_SDK\lib\MESSAGEsend;
use think\facade\Log;

class SubSms implements SmsBase
{
    /**
     * 赛邮云发送短信验证码
     * @param string $phone 手机号
     * @param int $code 验证码
     * @return bool
     */
    public static function sendCode(string $phone, int $code): bool
    {
        // 加载第三方类库自动装载文件
        require_once 'E:\Code\Git-tp6\ThinkPHP6-Shopping_Project\tp\extend\SUBMAIL_PHP_SDK\SUBMAILAutoload.php';

        // 判断手机号或验证码是否为空
        if (empty($phone) || empty($code)) {
            return false;
        }

        // 获取配置文件 api/config/Submail 中的配置
        $message_configs = [
            'server' => config("Submail.server"),
            'appid' => config("Submail.appid"),
            'appkey' => config("Submail.appkey"),
            'sign_type' => config("Submail.sign_type"),
        ];

        try {
            // 初始化 MESSAGEsend 类
            $submail = new MESSAGEsend($message_configs);
            // 设置短信接收的11位手机号码
            $submail->setTo($phone);
            // 设置短信正文,将验证码写入
            $submail->SetContent('【毛越】您的短信验证码：' . $code . '，请在10分钟内输入。——赛邮云');
            // 调用 send 方法发送短信
            $send = $submail->send();
            // 打印服务器返回值
//            dump($send);
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

