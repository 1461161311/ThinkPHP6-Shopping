<?php

namespace app\common\model\mysql;

use think\Model;

class User extends Model
{

    /**
     * 自动写入时间，要求字段必须为 create_time 和 update_time
     * @var bool
     */
    protected $autoWriteTimestamp = true;

    /**
     * 根据用户手机号来查询数据
     * @param $phoneNumber
     * @return array|bool|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUserByPhoneNumber($phoneNumber)
    {
        // 判断参数
        if (empty($phoneNumber)) {
            return false;
        }

        // 添加 where 条件
        $where = [
            "phone_number" => $phoneNumber
        ];

        // 返回 sql 语句
        return $this->where($where)->find();

    }

    /**
     * 根据 ID 来更新表中的数据
     * @param $id
     * @param $data
     * @return bool
     */
    public function updateById($id, $data)
    {
        $id = intval($id);
        if (empty($id) || empty($data) || !is_array($data)) {
            return false;
        }

        $where = [
            "id" => $id,
        ];

        return $this->where($where)->save($data);

    }


}