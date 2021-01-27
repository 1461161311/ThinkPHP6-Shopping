<?php

namespace app\common\business;

use think\Exception;
use think\facade\Cache;

class Cart extends BaseBusiness
{
    /**
     * 添加购物车
     * @param $userId   // 用户 id
     * @param $id   // 商品 sku id
     * @param $num  // 商品数量
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function insertRedis($userId, $id, $num)
    {
        // 根据 sku id 查询 sku 信息及商品信息
        $goodsSku = (new GoodsSku())->getNormalSkuAndGoods($id);
        if (!$goodsSku) {
            return FALSE;
        }

        // 组装数据
        $data = [
            "title" => $goodsSku['goods']['title'], // 商品标题
            "image" => $goodsSku['goods']['recommend_image'],   // 商品图片
            "num" => $num,  // 商品个数
            "goods_id" => $goodsSku['goods']['id'], // 商品 id
            "create_time" => time(),    // 添加购物车时间
        ];

        try {
            // hGet() 根据 key 值查询 redis 数据库
            $get = Cache::hGet(config("redis.cart_pre") . $userId, $id);
            if ($get) {     // 当添加同一个商品时,仅增加数量
                $get = json_decode($get, true); // json_decode() 将json数组转成字符串
                $data['num'] = $data['num'] + $get['num'];  // 添加的商品数量 + 原有商品数量
            }
            // hSet() 添加 redis 数据
            $res = Cache::hSet(config("redis.cart_pre") . $userId, $id, json_encode($data));
        } catch (\Exception $exception) {
            return FALSE;
        }
        return $res;
    }


    /**
     * 购物车列表
     * @param $userId
     * @return array
     */
    public function lists($userId)
    {
        try {
            // hGetAll() 根据 hash 值获取该值下所有数据
            $res = Cache::hGetAll(config("redis.cart_pre") . $userId);
        } catch (\Exception $exception) {
            $res = [];
        }
        if (!$res) {
            return [];
        }

        // 将获取到的购物车数据的 key 值取出
        $skuIds = array_keys($res);

        // 查询商品规格 id 所对应的规格名
        $skus = (new GoodsSku())->getNormalInIds($skuIds);
        // array_column(): 转换成 id => price 格式数组
        $skuIdsPrice = array_column($skus, "price", "id");
        $result = [];
        // array_column(): 转换成 id => specs_value_ids 格式数组
        $skuIdSpecsValueIds = array_column($skus, "specs_value_ids", "id");

        // 获取规格名
        $specsValues = (new SpecsValue())->dealSpecsValue($skuIdSpecsValueIds);

        // 拼接数据
        foreach ($res as $key => $value) {
            $value = json_decode($value, true); // json_decode(): json 数组转字符串
            $value['id'] = $key;    // 商品 sku 的 id
            // preg_match(): 执行正则  判断图片路径是否正确
            $value['image'] = preg_match("/http:\/\//", $value['image']) ? $value['image'] : "http://localhost:81" . $value['image'];
            $value['price'] = $skuIdsPrice[$key] ?? 0;  // 商品价格
            $value['sku'] = $specsValues[$key] ?? "暂无规格";   // 商品规格
            $result[] = $value;
        }

        // 当成功获取购物车数据,对购物车根据添加时间进行排序
        if (!empty($result)) {
            $re = array_column($result, "create_time");
            array_multisort($re, SORT_DESC, $result);
        }
        return $result;
    }


    /**
     * 删除购物车
     * @param $userId   // 用户 id
     * @param $id   // 要删除的商品 sku 的 id
     * @return bool
     */
    public function delete($userId, $id)
    {
        try {
            // 删除指定 key 值
            $result = Cache::hDel(config("redis.cart_pre") . $userId, $id);
        } catch (\Exception $exception) {
            return FALSE;
        }

        return $result;
    }


    /**
     * 购物车编辑
     * @param $userId   // 用户 id
     * @param $id   // 要编辑的商品 sku 的 id
     * @param $num  // 要修改的商品数量
     * @return bool
     * @throws Exception
     */
    public function update($userId, $id, $num)
    {
        try {
            // 获取要修改的商品 redis 数据
            $res = Cache::hGet(config("redis.cart_pre") . $userId, $id);
        } catch (\Exception $exception) {
            return FALSE;
        }

        // 如果存在该购物车
        if ($res) {
            // 修改其数量
            $res = json_decode($res, true);
            $res['num'] = $num;
        } else {
            throw new Exception("不存在该购物车");
        }

        try {
            // 保存修改的购物车数据
            $result = Cache::hSet(config("redis.cart_pre") . $userId, $id, json_encode($res));
        } catch (\Exception $exception) {
            return FALSE;
        }
        return $result;

    }


    /**
     * 获取购物车中的商品数量
     * @param $userId   // 用户 id
     * @return int
     */
    public function getCount($userId)
    {
        try {
            // hLen() 根据购用户 id 获取购物车中的商品数量
            $count = Cache::hLen(config("redis.cart_pre") . $userId);
        } catch (\Exception $exception) {
            return 0;
        }
        return $count;
    }

}