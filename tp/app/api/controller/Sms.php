<?php
declare(strict_types=1);

namespace app\api\controller;

use app\BaseController;
use app\common\business\Sms as SmsBusiness;

class Sms extends BaseController
{
    /**
     * controller层获取并验证手机号以及调用business层
     * @return object
     */
    public function code(): object
    {
        // 获取手机号码
        $phoneNumber = input('param.phone_number', '', 'trim');

        // 将获取到的手机号码放入数组，交给validate验证
        $data = [
            'phone_number' => $phoneNumber,
        ];

        // 验证数据
        try {
            validate(\app\api\validate\User::class)->scene("send_code")->check($data);
            // 抛出异常
        } catch (\think\Exception\ValidateException $exception) {
            return show(config("status.error"), $exception->getError());
        }

        // 验证手机号是否符合规范
        $g = "/^1[34578]\d{9}$/";
        $g2 = "/^19[89]\d{8}$/";
        $g3 = "/^166\d{8}$/";
        if (preg_match($g, $phoneNumber) || preg_match($g2, $phoneNumber) || preg_match($g3, $phoneNumber)) {
            // 成功验证手机号符合规范
            // 调用business层的数据
            if (SmsBusiness::sendCode($phoneNumber, 4)) {
                return show(config("status.success"), "发送验证码成功");
            }
            return show(config("status.error"), "发送验证码失败");
        }

        // 手机号验证失败
        return show(config("status.error"), "手机号输入错误，请重新输入！");

    }
}

