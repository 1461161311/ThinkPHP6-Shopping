<?php

namespace app\api\controller;

use app\BaseController;

class Login extends BaseController
{

    public function index()
    {
        // 验证请求方式
        if (!$this->request->isPost()) {
            return show(config("status.error"), "请求方式错误，非post请求");
        }

        // 验证参数
        // 从请求中获取参数
        $phoneNumber = input("param.phone_number", "" . "trim");
        $code = input("param.code", 0, "intval");
        $type = input("param.type", 0, "intval");

        // 参数校验
        $data = [
            'phone_number' => $phoneNumber,
            'code' => $code,
            'type' => $type,
        ];

        // 调用校验类
        $validate = new \app\api\validate\User();
        // 使用 login 场景校验
        if (!$validate->scene('login')->check($data)) {
            return show(config("status.error"), $validate->getError());
        }

        // 调用 business 层登录逻辑
        try {
            $result = (new \app\common\business\User())->login($data);
        } catch (\Exception $exception) {
            return show($exception->getCode(), $exception->getMessage());
        }

        // 判断 business 层返回的数据
        if ($result) {
            return show(config("status.success"), "登陆成功", $result);
        }
        return show(config("status.error"), "登陆失败");


    }

}
