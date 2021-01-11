<?php

namespace app\common\model\mysql;

use think\Model;

class SpecsValue extends Model
{
    /**
     * 自动写入时间，要求数据库字段必须为 create_time 和 update_time
     * @var bool
     */
    protected $autoWriteTimestamp = true;

    /**
     * 根据规格 id 查询规格表单
     * @param $specsId
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getBySpecsId($specsId, $field = "*")
    {
        $where = [
            "specs_id" => $specsId,
            "status" => config("status.mysql.table_normal")
        ];

        return $this->where($where)->field($field)->select();
    }

    /**
     * 根据 name 值查询数据库是否有重复
     * @param $name
     * @return array|bool|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getSpecsValueName($name)
    {
        if (empty($name)) {
            return false;
        }
        $where = [
            "name" => $name,
        ];
        return $this->where($where)->find();
    }


    /**
     * 修改数据
     * @param $id
     * @param $data
     * @return bool
     */
    public function updateById($id,$data)
    {
        $where = [
            "id" => $id
        ];
        // 更新时间
        $data['update_time'] = time();

        return $this->where($where)->save($data);
    }

}