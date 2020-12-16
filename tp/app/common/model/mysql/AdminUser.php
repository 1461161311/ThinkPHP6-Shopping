<?php
namespace app\common\model\mysql;

use think\Model;

class AdminUser extends Model {

    /**
     * 根据用户名获取用户表数据
     * @param $username
     * @return array|bool|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAdminUserByUsername($username)
    {
        // 判断参数
        if(empty($username)){
            return false;
        }

        // 添加 where 条件
        $where = [
            "username" => trim($username)
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
    public function updateById($id,$data)
    {
        $id = intval($id);
        if (empty($id) || empty($data) || !is_array($data)){
            return false;
        }

        $where = [
            "id" => $id,
        ];

        return $this->where($where)->save($data);

    }



}