<?php

namespace app\common\model\mysql;

class GoodsSku extends BaseModel
{
    /**
     * 一对一关联
     * 关联 Goods 表, goods_id 对应 Goods 表中的 id 字段
     * @return \think\model\relation\HasOne
     */
    public function goods()
    {
        return $this->hasOne(Goods::class, "id", "goods_id");
    }


    /**
     * 根据商品 id 查询 sku 数据
     * @param int $goodsId  // 传入所要查询的商品 id
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getNormalByGoodsId($goodsId = 0)
    {
        $where = [
            "goods_id" => $goodsId,
            "status" => config("status.mysql.table_normal")
        ];

        return $this->where($where)->select();
    }

}