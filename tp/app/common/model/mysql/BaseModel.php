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

}