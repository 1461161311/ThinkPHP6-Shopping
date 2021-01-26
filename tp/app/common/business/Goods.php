<?php

namespace app\common\business;

use app\common\model\mysql\Goods as GoodsModel;
use app\common\business\GoodsSku as GoodsSkuBus;
use app\common\business\SpecsValue as SpecsValueBus;
use app\common\model\mysql\Category as CategoryModel;
use think\Exception;
use think\facade\Cache;

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
     * @param $data // 添加商品的信息
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
            // 如果提交失败,事务回滚
            $this->model->rollback();
            show(config("status.error"), $exception->getMessage());
            return false;
        }

    }


    /**
     * 商品列表
     * @param $data // 模糊查询条件
     * @param $num // 分页显示数量
     * @param $field // 查询字段
     * @param $order // 排序方式
     * @return array    // 返回数组
     */
    public function getLists($data, $order, $num, $field)
    {
        // 判断是否传入参数使用搜索功能
        $likeKeys = [];
        if (!empty($data)) {
            $likeKeys = array_keys($data);
        }

        // 判断是否传入查询字段参数
        $fieldModel = null;
        if (!empty($field)) {
            $fieldModel[] = $field;
        }

        // 调用 model 层分页查询方法
        try {
            $list = $this->model->getLists($likeKeys, $data, $order, $num, $fieldModel);
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
     * @param $id // 商品 id
     * @param $data // 商品修改的信息
     * @return bool
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function isIndex($id, $data)
    {
        $result = $this->getById($id);
        if (!$result) {
            throw new Exception("不存在该条记录");
        }

        if ($result['is_index_recommend'] == $data) {
            throw new Exception("状态修改前和修改后一致");
        }

        $data = [
            "is_index_recommend" => intval($data),
        ];

        try {
            $result = $this->model->updateById($id, $data);
        } catch (\Exception $exception) {
            return false;
        }

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
        // 验证 id
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


    /**
     * 查询首页推荐的商品
     * @return array
     */
    public function getRotationChart()
    {
        $data = [
            "is_index_recommend" => 1,  // 是否推荐字段 (0:不推荐,1:推荐)
        ];

        // 查询字段
        $field = "sku_id as id , title , big_image as image";

        try {
            $result = $this->model->getNormalGoodsByCondition($data, $field);
        } catch (\Exception $exception) {
            return [];
        }

        return $result->toArray();
    }


    /**
     * 传入多个分类 id ,查询该分类下的商品
     * @param $categoryIds
     * @param $field
     * @return array
     */
    public function categoryGoodsRecommend($categoryIds, $field)
    {
        if (!$categoryIds) {
            return [];
        }
        $result = [];
        // 将分类数据写入返回数据
        foreach ($categoryIds as $key => $categoryId) {
            $category = (new CategoryModel())->getById($categoryId);
            $category = $category->toArray();
            $json = [
                "category_id" => $category['id'],
                "name" => $category['name'],
                "icon" => "",
            ];
            $result[$key]["categorys"] = $json;
        }

        // 将商品数据写入返回数据
        foreach ($categoryIds as $key => $categoryId) {
            $result[$key]["goods"] = $this->getNormalGoodsFindInSetCategoryId($categoryId, $field);
        }
        return $result;
    }


    /**
     * 传入分类 id ,查询商品 path 字段
     * @param $categoryId
     * @param $field
     * @return array
     */
    public function getNormalGoodsFindInSetCategoryId($categoryId, $field)
    {
        try {
            $result = $this->model->getNormalGoodsFindInSetCategoryId($categoryId, $field);
        } catch (\Exception $exception) {
            return [];
        }
        return $result->toArray();
    }


    /**
     * 根据传入的分类 id ,查询商品 category_id 字段
     * @param $categoryId
     * @param $field
     * @param $limit
     * @param $order
     * @return array
     */
    public function getByCategoryId($categoryId, $field, $limit, $order)
    {
        $where = [
            "category_id" => $categoryId,
        ];
        try {
            $list = $this->model->getByCategoryId($field, $where, $limit, $order);
        } catch (\Exception $exception) {
            return [];
        }

        $list = $list->toArray();

        return $list;
    }


    /**
     * 根据商品 sku_id 获取商品信息
     * @param $skuId // 商品 sku_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getGoodsDetailBySkuId($skuId)
    {
        $goodsSkuObj = new GoodsSkuBus();
        // 根据 sku_id 查询该 sku 数据以及该 sku 数据下的商品数据
        $goodsSku = $goodsSkuObj->getNormalSkuAndGoods($skuId);

        if (!$goodsSku) {
            return [];
        }
        // 验证该 sku 下是否获取到商品信息
        if (empty($goodsSku['goods'])) {
            return [];
        }
        // 商品信息
        $goods = $goodsSku['goods'];

        // 根据商品 id 查询所属的 sku
        $skus = $goodsSkuObj->getSkusByGoodsId($goods['id']);

        if (!$skus) {
            return [];
        }

        // 查询所点击的商品的 specs_value_ids 字段,用于在页面中显示当前所选择的 sku 数据
        $flagValue = "";
        foreach ($skus as $v) {
            if ($v['id'] == $skuId) {   // 查询所点击的商品的 specs_value_ids 字段
                $flagValue = $v['specs_value_ids'];
            }
        }

        // 处理数据,获取该商品所有 sku 组合所对应的 sku 数据的 id,返回给前端使用 gids 字段
        $gids = array_column($skus, "id", "specs_value_ids");

        // 判断商品是否是多规格
        if ($goods['goods_specs_type'] == 1) {
            $sku = [];  // 当是统一规格时,sku数据设置为空
        } else {
            // 当商品时多规格时,传入该商品所对应的所有sku的数据
            $sku = (new SpecsValueBus())->dealGoodsSkus($gids, $flagValue);
        }

        // 返回数据
        $result = [
            "title" => $goods['title'], // 商品标题
            "price" => $goodsSku['price'],  // 商品该 sku 现价
            "cost_price" => $goodsSku['cost_price'],    // 商品该 sku 原价
            "sales_count" => 0, // 销售额
            "stock" => $goodsSku['stock'],  // 商品该 sku 库存
            "gids" => $gids,    // specs_value_ids 所对应的 sku 表中的 id
            "image" => $goods['carousel_image'],    // 商品轮播图
            "sku" => $sku,  // 商品 sku 数据
            "detail" => [   // 商品详情
                "d1" => [
                    "商品编码" => $goodsSku['id'],
                    "上架时间" => $goods['create_time'],
                ],
                // 处理商品详情中的图片路径
                // preg_replace():执行正则表达式
                "d2" => preg_replace('/(<img.+?src=")(.*?)/', '$1' . "http://localhost:81" . "$2", $goods['description']),
            ],
        ];

        // 记录数据到 redis 中,作为商品 pv 统计
        Cache::inc("mall_pv_" . $goods['id']);
        return $result;
    }

}