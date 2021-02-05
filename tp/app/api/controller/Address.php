<?php

namespace app\api\controller;

use app\common\business\Address as AddressBus;

class Address extends AuthBase
{

    /**
     * 获取用户收货地址
     * @return \think\response\Json
     */
    public function index()
    {
        $result = (new AddressBus())->getAddressByUserId($this->userId);

        if (!$result) {
            return show(config("status.error"), "获取地址信息失败");
        }

        return show(config("status.success"), "OK", $result);
    }

}