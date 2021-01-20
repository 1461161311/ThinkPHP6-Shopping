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

            // 当选择统一规格时
            if ($data['goods_specs_type'] == 1) {
                $data['skus']['0']['propvalnames'] = [
                    "propvalids" => 0,
                    "skuSellPrice" => $data['price'],
                    "skuMarketPrice" => $data['cost_price'],
                    "skuStock" => $data['stock'],
                ];
            }

            // 当选择多规格时
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


    /**
     * 商品列表
     * @param $num
     * @return array
     */
    public function getLists($data,$num)
    {
        // 判断是否传入参数使用搜索功能
        $likeKeys = [];
        if (!empty($data)){
            $likeKeys = array_keys($data);
        }

        // 调用 model 层分页查询方法
        try {
            $list = $this->model->getLists($likeKeys,$data,$num);
        } catch (\Exception $exception) {
            // 当分页方法出现异常时，调用默认返回数据
            return \app\common\lib\Arr::getPaginateDefaultData(3);
        }

        // 转换数据
        $result = $list->toArray();
        // 存放分页信息
        $result['render'] = $list->render();
        return $result;
    }


    /**
     * 修改商品是否首页推荐
     * @param $id
     * @param $data
     * @return bool
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function isIndex($id, $data)
    {
        // 验证 id 是否正确
        $result = $this->getById($id);
        if (!$result) {
            throw new Exception("不存在该条记录");
        }

        // 修改前后不得相同
        if ($result['is_index_recommend'] == $data) {
            throw new Exception("状态修改前和修改后一致");
        }

        // 组装数据
        $data = [
            "is_index_recommend" => intval($data),
        ];

        // 调用 model 层方法修改属性
        try {
            $result = $this->model->updateById($id, $data);
        } catch (\Exception $exception) {
            return false;
        }

        // 返回查询的数据
        return $result;
    }


    /**
     * 保存修改的数据
     * @param $id
     * @param $data
     * @return bool|\think\response\Json
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function saveUpdate($id, $data)
    {
        // 查询 id 是否正确
        $result = $this->getById($id);
        if (!$result) {
            throw new Exception("不存在该条记录");
        }

        // 调用 model 层编辑方法
        try {
            $result = $this->model->updateById($id, $data);
        } catch (\Exception $exception) {
            return show(config("status.error"), $exception->getMessage());
        }

        return $result;
    }

}