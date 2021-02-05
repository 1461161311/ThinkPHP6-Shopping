<?php

namespace app\common\model\mysql;

use think\Model;

class BaseModel extends Model
{
    /**
     * 自动写入时间，要求数据库字段必须为 create_time 和 update_time
     * @var bool
     */
    protected $autoWriteTimestamp = true;


    public function getById($id)
    {
        return $this->find($id);
    }


    /**
     * 更新数据
     * @param $id
     * @param $data
     * @return bool
     */
    public function updateById($id, $data)
    {
        $where = [
            "id" => $id,
        ];
        // 将当前时间写入数据库
        $data['update_time'] = time();
        return $this->where($where)->save($data);
    }

    /**
     * 根据传入的 ids 查询规格值
     * @param $ids // 传入要查询的 id ,需要为数组
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getNormalInIds($ids)
    {
        return $this->whereIn("id", $ids)
            ->where("status", "=", config("status.mysql.table_normal"))
            ->select();
    }


    /**
     * 根据指定条件查询数据
     * @param array $condition
     * @param string[] $order
     * @param string $field
     * @return bool|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getByCondition($condition = [], $order = ["id" => "desc"],$field = "*")
    {
        if (!$condition || !is_array($condition)) {
            return false;
        }

        return $this->where($condition)->order($order)->field($field)->select();
    }

    /**
     * 商品减库存
     * @param $id   // 商品 sku id
     * @param $num  // 需要减少的数量
     * @return mixed
     */
    public function decStock($id,$num)
    {
        return $this->where("id","=",$id)
            ->dec("stock",$num)
            ->update();
    }

    /**
     * 商品加库存
     * @param $id   // 商品 sku id
     * @param $num  // 需要减少的数量
     * @return mixed
     */
    public function incStock($id,$num)
    {
        return $this->where("id","=",$id)
            ->inc("stock",$num)
            ->update();
    }

}