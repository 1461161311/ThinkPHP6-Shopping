<?php

namespace app\common\model\mysql;

class Order extends BaseModel
{

    /**
     * 根据订单 id 更新数据
     * @param $orderId
     * @param $data
     * @return bool
     */
    public function updateByOrderId($orderId, $data)
    {
        $data['update_time'] = time();
        return $this->where($orderId)->save($data);
    }


}