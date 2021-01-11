<?php

namespace app\common\business;

use app\common\model\mysql\Specs as SpecsModel;
use think\Exception;

class Specs
{
    public $model = null;

    /**
     * 构造函数，自动 new Model 类对象
     * CategoryBus constructor.
     */
    public function __construct()
    {
        $this->model = new SpecsModel();
    }


    /**
     * 分页查询
     * @param $num
     * @return array
     */
    public function getLists($num)
    {
        // 调用 model 层分页查询方法
        try {
            $list = $this->model->getLists($num);
        } catch (\Exception $exception) {
            return [];
        }

        // 转换数据
        $result = $list->toArray();
        // 存放分页信息
        $result['render'] = $list->render();
        return $result;
    }

    /**
     * 添加数据
     * @param $data
     * @return mixed // 返回所保存的数据 id
     * @throws Exception
     */
    public function add($data)
    {
        // 调用 model 层根据名称查询数据方法 (数据库中存在假删除数据时，允许添加同名数据)
        try {
            $specs = $this->model->getSpecsByname($data);
        } catch (\Exception $exception) {
            throw new Exception("验证数据失败");
        }

        // 需要添加的规格已存在数据库中
        if ($specs) {
            throw new Exception("该规格已存在，请重新添加");
        }

        // 调用 model 层保存数据方法
        try {
            $this->model->save($data);
        } catch (\Exception $exception) {
            throw new Exception("保存数据失败");
        }

        // 返回所保存的数据 id
        return $this->model->id;
    }


    /**
     * 修改规格状态码
     * @param $id
     * @param $status
     * @return bool
     * @throws Exception
     */
    public function status($id, $status)
    {
        // 验证所要修改的数据内容是否合理
        $specs = $this->getById($id);
        if (!$specs) {
            throw new Exception("找不到该规格");
        }
        if ($status == $specs['status']) {
            throw new Exception("状态修改前和修改后一致");
        }

        // 转换数据格式
        $data = [
            "status" => intval($status),
        ];

        // 调用 model 层根据 id 更改数据方法
        try {
            $result = $this->model->updateById($id, $data);
        } catch (\Exception $exception) {
            return false;
        }

        return $result;
    }


    /**
     * 编辑规格
     * @param $id
     * @param $data
     * @return bool
     * @throws Exception
     */
    public function updateSpecs($id, $data)
    {
        // 验证所要修改的数据内容是否合理
        $specs = $this->getById($id);
        if (!$specs) {
            throw new \think\Exception("找不到该规格");
        }
        if ($data['name'] == $specs['name']) {
            throw new \think\Exception("修改前后数据相同");
        }

        // 组装数据
        $data = [
            "name" => $data['name'],
        ];

        // 调用 model 层根据 id 更新数据方法
        try {
            $result = $this->model->updateById($id, $data);
        } catch (\Exception $exception) {
            return false;
        }

        return $result;
    }


    /**
     * 根据 id 查询数据
     * @param $id
     * @return array
     * @throws Exception
     */
    public function getById($id)
    {
        // 调用 model 层根据 id 查询单条数据方法
        try {
            $result = $this->model->find($id);
        } catch (\Exception $exception) {
            throw new Exception($exception->getMessage());
        }

        // 查询失败返回空数组
        if (!$result) {
            return [];
        }

        // 转换查询结果格式
        $result = $result->toArray();
        return $result;
    }


    /**
     * 获取所有规格类型的 id 和 name
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getNormalSpecs()
    {
        // 设置要获取的字段
        $field = [
            "id" => 'id',
            "name" => 'name',
        ];

        // 调用 model 层获取数据方法
        try {
            $result = $this->model->getNormalSpecs($field);
        } catch (\Exception $exception) {
            return [];
        }

        // 转换查询结果格式
        $result = $result->toArray();
        return $result;
    }


}