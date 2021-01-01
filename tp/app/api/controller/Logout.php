<?php

namespace app\api\controller;

class Logout extends AuthBase
{
    /**
     * 退出登录
     * @return \think\response\Json
     */
    public function index()
    {
        // 清除 redis 中的 token 数据(将数据改为null)
        $result = cache(config("redis.token_pre") . $this->accessToken, NULL);

        if (!$result) {
            return show(config("status.error"), "退出登录失败");
        }
        return show(config("status.success"), "退出登录成功");
    }

}

