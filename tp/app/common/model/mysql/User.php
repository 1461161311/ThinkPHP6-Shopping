<?php

namespace app\common\model\mysql;

use think\Model;

class User extends BaseModel
{
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
     * 根据用户 id 来获取用户数据
     * @param $id
     * @return array|bool|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUserById($id)
    {
        // 验证 id
        $id = intval($id);
        if (!$id) {
            return false;
        }
        return $this->find($id);
    }

    /**
     * 根据用户名来获取用户数据
     * @param $username
     * @return array|bool|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUserByUsername($username)
    {
        // 判断参数
        if (empty($username)) {
            return false;
        }

        // 添加 where 条件
        $where = [
            "username" => $username
        ];

        // 返回 sql 语句
        return $this->where($where)->find();

    }


}