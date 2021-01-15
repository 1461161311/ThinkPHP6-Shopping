<?php

namespace app\common\model\mysql;

use think\Model;

class SpecsValue extends BaseModel
{
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

}