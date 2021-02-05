<?php

namespace app\common\business;

use  app\common\model\mysql\Address as AddressModel;

class Address extends BaseBusiness
{
    public $model = null;

    /**
     * 构造函数，自动 new Model 类对象
     * CategoryBus constructor.
     */
    public function __construct()
    {
        $this->model = new AddressModel();
    }


    /**
     * 根据用户 id 获取用户收货地址
     * @param $userId   // 用户 id
     * @return array
     */
    public function getAddressByUserId($userId)
    {
        $where = [
            "user_id" => $userId,
        ];

        try {
            $res = $this->model->getByCondition($where);
        } catch (\Exception $exception) {
            return [];
        }

        if (!$res) {
            return [];
        }

        $res = $res->toArray();

        $mark = [];
        $result = [];
        foreach ($res as $key => $value) {
            $mark[] = $value['is_default'];
            $result[$key]['id'] = $value['id'];
            $result[$key]['consignee_info'] = $value['consignee_info'] . "-" . $value['consignee_name'] . "收" . "-" . $value['phone'];
            $result[$key]['is_default'] = $value['is_default'];
        }
        // 重新排序收货信息,将默认收货地址放在第一位
        array_multisort($mark, SORT_DESC, $result);

        return $result;
    }
}
