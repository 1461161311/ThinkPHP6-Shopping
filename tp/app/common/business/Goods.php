<?php

namespace app\common\business;

use app\common\model\mysql\Goods as GoodsModel;
use app\common\business\GoodsSku as GoodsSkuBus;
use think\Exception;

class Goods extends BaseBusiness
{
    public $model = null;

    /**
     * 构造函数，自动 new Model 类对象
     * CategoryBus constructor.
     */
    public function __construct()
    {
        $this->model = new GoodsModel();
    }


    /**
     * 添加商品并添加 sku
     * @param $data
     * @return bool|mixed
     */
    public function insertData($data)
    {
        // 开启事务
        $this->model->startTrans();

        try {
            // 添加商品
            $goodsId = $this->add($data);
            if (!$goodsId) {
                return $goodsId;
            }

            if ($data['goods_specs_type'] == 1){
                $data['skus']['0']['propvalnames'] = [
                    "propvalids" => 0,
                    "skuSellPrice" => $data['price'],
                    "skuMarketPrice" => $data['cost_price'],
                    "skuStock" => $data['stock'],
                ];
            }

            // 获取商品 id , 作为 sku 表的 goods_id
            $data['goods_id'] = $goodsId;
            $res = (new GoodsSkuBus())->saveAll($data);

            if (!empty($res)) {
                // 获取所有 sku 表中的库存数据,相加得出该商品共有库存
                $stock = array_sum(array_column($res, "stock"));

                // 将 sku 表中的第一条数据作为默认数据回写至商品表
                $goodsUpdateData = [
                    "price" => $res['0']['price'],
                    "cost_price" => $res['0']['cost_price'],
                    "stock" => $stock,
                    "sku_id" => $res['0']['id'],
                ];
                // 回写商品表
                $goodsRes = $this->model->updateById($goodsId, $goodsUpdateData);
                if (!$goodsRes) {
                    throw new Exception("goods表更新失败");
                }

            } else {
                throw new Exception("sku表新增失败");
            }

            // 事务提交
            $this->model->commit();
            return true;
        } catch (\Exception $exception) {
            // 事务回滚
            $this->model->rollback();
            show(config("status.error"), $exception->getMessage());
            return false;
        }

    }

}