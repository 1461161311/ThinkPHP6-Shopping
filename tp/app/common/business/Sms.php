<?php
declare(strict_types=1);

namespace app\common\business;

use app\common\lib\sms\YunpianSms;
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
    public static function sendCode(string $phoneNumber, int $len): bool
    {
        // 定义用于存放调用接口名称的变量
        $type = null;
        // 定义用于存放配置文件的数组
        $roll = [];
        $va = [];

        // 静态调用lib下面的类库生成随机短信验证码
        $code = Num::getCode($len);
        // 调用lib层的发送验证码功能，并将手机号和生成的验证码传过去
//        $sms = YunpianSms::sendCode($phoneNumber, $code);
//
//        // 工厂模式
//        // ucfirst() 将首字母转为大写
//        $type = ucfirst($type);
//        // 拼接路径，sms\\ 第二个斜杆是转义
//        $class = "app\common\lib\sms\\" . $type . "Sms";
//        // 调用方法
//        $sms = $class::sendCode($phoneNumber, $code);
//
//        // 判断是否发送成功
//        if ($sms) {
//            // 发送成功需要把短信验证码记录到redis中，并且给出生效时间，如：该验证码一分钟失效
//            cache(config("redis.code_pre") . $phoneNumber, $code, config("redis.code_expire"));
//        }

        // 将配置文件数组取出来
        $arr = config("sms");
        // 遍历
        foreach ($arr as $key => $value) {
            if ($key == 'roll') {
                $roll = $value;
            } else if ($key == 'value') {
                $va = $value;
            }
        }

        // 设置循环次数
        $count = count($va);

        // 循环调用接口，成功则结束循环
        for ($i = 0; $i <= $count; $i++) {
            // 按照规定概率随机生成一个数组
            $arr = self::Choose($arr['value'], $arr['roll'], 1);
            // 获取生成的数组中的字符串
            $type = $arr['0'];

            // 工厂模式,调用第三方短信接口
            // ucfirst() 将首字母转为大写
            $type = ucfirst($type);
            // 拼接路径，sms\\ 第二个斜杆是转义
            $class = "app\common\lib\sms\\" . $type . "Sms";
            // 调用方法
            $sms = $class::sendCode($phoneNumber, $code);


            // 判断是否发送成功
            if ($sms) {
                // 发送成功需要把短信验证码记录到redis中，并且给出生效时间，如：该验证码一分钟失效
                cache(config("redis.code_pre") . $phoneNumber, $code, config("redis.code_expire"));
                // 结束循环
                break;
            }

            // 如果发送失败,则删除数组中的值,防止重复发送发送失败的接口
            // 根据值获取键
            $key = array_search($arr['0'], $va);

            // 根据key删除value
            unset($roll[$key]);
            unset($va[$key]);

            $roll = array_values($roll);
            $va = array_values($va);
        }

        // 如果全部接口都发送失败则返回发送失败
        return $sms;
    }

    /**
     * 获取浮点随机数
     * @param int $min
     * @param int $max
     * @return int
     */
    public static function randomFloat($min = 0, $max = 1)
    {

        return $min + mt_rand() / mt_getrandmax() * ($max - $min);

    }

    /**
     * 根据概率提取数组中元素
     * @param array $seq 待取数组
     * @param array $prob 对应每个元素概率（浮点数，和等于1）
     * @param int $k 重复次数
     * @return array
     */
    public static function Choose($seq, $prob, $k = 1)
    {
        $list = [];
        for ($i = 0; $i < $k; $i++) {
            $q = self::randomFloat();
            for ($j = 0; $j <= count($seq); $j++) {
                if (array_sum(array_slice($prob, 0, $j)) < $q && $q <= array_sum(array_slice($prob, 0, $j + 1))) {
                    $list[$i] = $seq[$j];
                    break;
                }
            }
        }
        return $list;
    }
}

