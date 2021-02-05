<?php

namespace app\common\business;

use app\common\lib\Snowflake;
use app\common\model\mysql\Order as OrderModel;
use app\common\model\mysql\OrderGoods as OrderGoodsModel;
use app\common\model\mysql\Address as AddressModel;
use app\common\business\OrderGoods as OrderGoodsBus;
use think\facade\Cache;

class Order extends BaseBusiness
{
    public $model = NULL;

    public function __construct()
    {
        $this->model = new OrderModel();
    }


    /**
     * 保存订单
     * @param $data
     * @return bool|string[]
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function save($data)
    {
        // 随机生成订单号
        $workId = rand(1, 1023);
        $orderId = Snowflake::getInstance()->setWorkId($workId)->id();

        // 获取 redis 中的购物车数据
        $cartObj = (new Cart());
        $result = $cartObj->lists($data['user_id'], $data['ids']);
        if (!$result) {
            return false;
        }

        // 获取整个订单总价
        $price = array_sum(array_column($result, "total_price"));

        // 订单信息
        $orderData = [
            "user_id" => $data['user_id'],        // 用户 id
            "order_id" => $orderId,              // 订单号
            "total_price" => $price,             // 订单总价
            "address_id" => $data["address_id"],    // 地址 id
        ];

        // order_goods 信息
        $newResult = array_map(function ($v) use ($orderId) {
            $v['sku_id'] = $v['id'];
            unset($v['id']);
            $v['order_id'] = $orderId;
            return $v;
        }, $result);

        try {
            // 开启事务
            $this->model->startTrans();

            // 新增 order
            $id = $this->add($orderData);
            if (!$id) {
                return false;
            }
            // 批量新增 order_goods
            (new OrderGoodsBus())->saveAll($newResult);
            // 更新 goods_sku 库存
            (new GoodsSku())->updateStock($newResult);
            // 更新 goods 库存
            (new Goods())->updateStock($newResult);
            // 删除购物车
            $cartObj->delete($data['user_id'], $data['ids']);

            // 事务提交
            $this->model->commit();

            // 将订单 ID 放入消息队列中,定期检测订单是否已经支付
            // 此处需要额外的 try catch 因为不能因为消息队列影响到订单的生成
            try {
                Cache::zAdd(config("redis.order_status_key"), time() + config("redis.order_expire"), $orderId);
            } catch (\Exception $exception) {
                // 此处添加日志
            }

            return ["id" => (string)$orderId];
        } catch (\Exception $exception) {
            // 事务回滚
            $this->model->rollback();
            return false;
        }
    }


    /**
     * 获取订单信息
     * @param $data
     * @return array|bool|mixed
     */
    public function detail($data)
    {
        $condition = [
            "user_id" => $data['user_id'],      // 用户 id
            "order_id" => $data['order_id'],    // 订单 id
        ];


        // 查询该订单信息
        try {
            $orders = $this->model->getByCondition($condition);
        } catch (\Exception $exception) {
            return false;
        }
        if (!$orders) {
            return [];
        }
        $orders = $orders->toArray();
        $orders = !empty($orders) ? $orders[0] : [];


        // 获取用户收货地址信息
        $addressCondition = [
            "id" => $orders['address_id'],  // 收货地址 id
            "user_id" => $data['user_id'],  // 用户 id
        ];
        try {
            $address = (new AddressModel())->getByCondition($addressCondition);
        } catch (\Exception $exception) {
            return false;
        }
        if (!$address) {
            return [];
        }
        $address = $address->toArray();
        $address = !empty($address) ? $address[0] : [];


        // 获取该订单下所有商品信息
        $orderGoodsCondition = [
            "order_id" => $condition['order_id'],
        ];
        try {
            $orderGoods = (new OrderGoodsModel())->getByCondition($orderGoodsCondition);
        } catch (\Exception $exception) {
            return false;
        }
        $orderGoods = $orderGoods->toArray();


        // 拼装数据
        $orders['id'] = $orders['order_id'];    // 订单 id
        $orders['consignee_info'] = $address['consignee_info']; // 收货地址
        $orders['malls'] = $orderGoods;     // 订单商品信息
        return $orders;
    }

    /**
     * 自动脚本触发该方法,用于查询 redis 数据库中过时未支付的订单,并修改其状态
     * @return bool
     */
    public function checkOrderStatus()
    {
        // 返回有序集合中超过规定时间未支付的订单信息
        $result = Cache::store('redis')->zRangeByScore("order_status", 0, time(), ['limit' => 0, 1]);

        if (empty($result) || empty($result[0])) {
            return false;
        }
        try {
            // 删除有序集合中超过规定时间未支付的订单信息
            $deleteRedis = Cache::store('redis')->zRem("order_status", $result[0]);
        } catch (\Exception $exception) {
            $deleteRedis = "";
        }
        // 当订单超时未支付时
        if ($deleteRedis) {
            try {
                // 开启事务
                $this->model->startTrans();

                // 查询 order 表中的订单状态码是否为 status = 1 (待支付) 修改为 status = 7 (已取消订单状态)
                $orderObj = new OrderModel();
                // 获取订单号
                $condition = [
                    "order_id" => $result[0],
                ];
                // 查询该订单状态码是否为 status = 1 (待支付)
                $order = $orderObj->getByCondition($condition);
                if (!$order == 1) {
                    return false;
                }
                // 修改该订单的状态码为 status = 7 (已取消订单状态)
                $orderObj->updateByOrderId($condition, ["status" => 7]);


                // 查询 order_goods 表,拿到 sku_id num 把减去的库存,再加回去
                $orderGoods = (new OrderGoodsModel)->getByCondition($condition);
                if (!$orderGoods) {
                    return false;
                }
                $orderGoods = $orderGoods->toArray();
                // 更新 goods_sku 库存
                (new GoodsSku())->updateStock($orderGoods, "inc");
                // 更新 goods 库存
                (new Goods())->updateStock($orderGoods, "inc");
                // 信息提示
                echo "订单id:{$result[0]}未在规定时间内完成支付,判定为无效订单删除" . PHP_EOL;  // PHP_EOL : 换行

                // 事务提交
                $this->model->commit();
            } catch (\Exception $exception) {
                // 事务回滚
                $this->model->rollback();
                return false;
            }


        }

    }


}