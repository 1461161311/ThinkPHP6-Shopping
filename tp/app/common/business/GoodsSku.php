<?php

namespace app\common\business;

use app\common\model\mysql\GoodsSku as GoodsSkuModel;

class GoodsSku extends BaseBusiness
{

    public $model = null;

    /**
     * 构造函数，自动 new Model 类对象
     * CategoryBus constructor.
     */
    public function __construct()
    {
        $this->model = new GoodsSkuModel();
    }


    /**
     * 保存 sku 数据
     * @param $data
     * @return array|bool
     */
    public function saveAll($data)
    {
        if (empty($data['skus'])) {
            return false;
        }

        // 循环输出商品的 sku 数据
        foreach ($data['skus'] as $value) {
            $insertData[] = [
                "goods_id" => $data['goods_id'],    // 商品id
                "specs_value_ids" => $value['propvalnames']['propvalids'],  // 规格id
                "price" => $value['propvalnames']['skuSellPrice'],  // 现价
                "cost_price" => $value['propvalnames']['skuMarketPrice'],   // 原价
                "stock" => $value['propvalnames']['skuStock'],  // 库存
            ];
        }

        try {
            $result = $this->model->saveAll($insertData);   // saveAll() 添加数组中所有数据
        } catch (\Exception $exception) {
            return false;
        }

        return $result->toArray();
    }


    /**
     * 保存数据
     * @param $data
     * @return array|bool
     */
    public function save($data)
    {
        if (empty($data['skus'])) {
            return false;
        }

        // 循环输出商品的 sku 数据
        foreach ($data['skus'] as $value) {
            $insertData[] = [
                "goods_id" => $data['goods_id'],    // 商品id
                "specs_value_ids" => $value['propvalnames']['propvalids'],  // 规格id
                "price" => $value['propvalnames']['skuSellPrice'],  // 现价
                "cost_price" => $value['propvalnames']['skuMarketPrice'],   // 原价
                "stock" => $value['propvalnames']['skuStock'],  // 库存
            ];
        }

        try {
            $result = $this->model->saveAll($insertData);   // saveAll() 添加数组中所有数据
        } catch (\Exception $exception) {
            return false;
        }

        return $result->toArray();
    }

}