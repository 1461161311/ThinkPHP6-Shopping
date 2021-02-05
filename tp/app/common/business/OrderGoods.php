<?php

namespace app\common\business;

use app\common\model\mysql\OrderGoods as OrderGoodsModel;

class OrderGoods extends BaseBusiness
{

    public $model = NULL;

    public function __construct()
    {
        $this->model = new OrderGoodsModel();
    }

    public function saveAll($data)
    {
        try {
            $result = $this->model->saveAll($data);
            return $result->toArray();
        } catch (\Exception $exception) {
            return false;
        }
    }
}