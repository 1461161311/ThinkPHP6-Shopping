<?php

namespace app\common\business;

use think\Exception;

class BaseBusiness
{
    /**
     * 根据 id 查询数据 (直接调用 find() 方法，不需要在 model 层写 )
     * @param $id
     * @return array|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getById($id,$field = true)
    {
        try {
            $result = $this->model->field($field)->find($id);
        } catch (\Exception $exception) {
            throw new \think\Exception("status.error", $exception->getMessage());
        }
        if (!$result) {
            return [];
        }
        $result = $result->toArray();
        return $result;
    }


    /**
     * 修改分类状态码
     * @param $id
     * @param $status
     * @return bool
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function status($id, $status)
    {
        $result = $this->getById($id);
        if (!$result) {
            throw new Exception("不存在该条记录");
        }
        if ($result['status'] == $status) {
            throw new Exception("状态修改前和修改后一致");
        }

        $data = [
            "status" => intval($status),
        ];
        try {
            $result = $this->model->updateById($id, $data);
        } catch (\Exception $exception) {
            return false;
        }

        // 返回查询的数据
        return $result;
    }


    /**
     * 添加商品
     * @param $data
     * @return mixed
     * @throws Exception
     */
    public function add($data)
    {
        // 设置分类状态码为 1 正常
        $data['status'] = config("status.mysql.table_normal");

        // 保存数据
        try {
            $this->model->save($data);
        } catch (\Exception $exception) {
            throw new Exception("添加数据失败");
        }
        // 返回 id
        return $this->model->id;
    }
}