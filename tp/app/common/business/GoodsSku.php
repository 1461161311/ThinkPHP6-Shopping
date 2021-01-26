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


    /**
     * 一对一关联查询,根据 sku 的 id,查询 sku 信息以及相关的商品信息
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getNormalSkuAndGoods($id)
    {
        try {
            // with(): 调用关联查询(使用两条sql语句查询)
            $result = $this->model->with("goods")->find($id);
        } catch (\Exception $exception) {
            return [];
        }
        $result = $result->toArray();
        if ($result['status'] != config("status.mysql.table_normal")) {
            return [];
        }
        return $result;
    }


    /**
     * 根据商品 id 查询所属的 sku
     * @param int $goodsId
     * @return array
     */
    public function getSkusByGoodsId($goodsId = 0)
    {
        if (!$goodsId) {
            return [];
        }
        try {
            // 根据商品 id 查询所属的 sku
            $skus = $this->model->getNormalByGoodsId($goodsId);
        } catch (\Exception $exception) {
            return [];
        }

        return $skus->toArray();
    }

}