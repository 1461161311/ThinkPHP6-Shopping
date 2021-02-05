<?php

namespace app\api\controller\order;

use app\common\business\Order;
use app\common\business\Order as OrderBus;
use app\api\controller\AuthBase;

class Index extends AuthBase
{

    /**
     * 创建订单
     * @return \think\response\Json
     */
    public function save()
    {
        // 用户地址
        $addressId = input("param.address_id", 0, "intval");
        // 商品 sku_id
        $ids = input("param.ids", "", "trim");

        if (!$addressId || !$ids) {
            return show(config("status.error"), "参数错误");
        }

        $data = [
            "ids" => $ids,
            "address_id" => $addressId,
            "user_id" => $this->userId,    // 用户 id
        ];

        try {
            $result = (new OrderBus())->save($data);
        } catch (\Exception $exception) {
            return show(config("status.error"));
        }
        if (!$result) {
            return show(config("status.error"));
        }
        return show(config("status.success"), "OK", $result);
    }


    /**
     * 获取订单信息
     * @return \think\response\Json
     */
    public function read()
    {
        $id = input("param.id", 0, "intval");   // 订单 id

        if (empty($id)) {
            return show(config("status.error"), "参数错误");
        }

        $data = [
            "user_id" => $this->userId, // 用户 id
            "order_id" => $id,  // 订单 id
        ];

        $result = (new Order())->detail($data);

        if (!$result) {
            return show(config("status.error"), "获取订单信息失败");
        }

        return show(config("status.success"), "OK", $result);

    }

}