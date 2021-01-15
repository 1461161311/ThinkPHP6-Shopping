<?php

namespace app\common\business;

use think\Exception;

class SpecsValue extends BaseBusiness
{
    public $model = null;
    /**
     * 构造函数，自动 new Model 类对象
     * CategoryBus constructor.
     */
    public function __construct()
    {
        $this->model = new \app\common\model\mysql\SpecsValue();
    }


    /**
     * 返回指定的规格下的数据
     * @param $specsId
     * @return array|\think\Collection
     */
    public function getBySpecsId($specsId)
    {
        // 调用 model 层查询数据方法 (返回 id,name 字段)
        try {
            $result = $this->model->getBySpecsId($specsId, "id,name");
        } catch (\Exception $exception) {
            return [];
        }

        // 转换数据类型
        $result = $result->toArray();
        return $result;
    }


    /**
     * 添加规格
     * @param $data
     * @return mixed|\think\response\Json
     * @throws Exception
     */
    public function add($data)
    {
        // 调用 model 层按照指定条件查询数据方法 (查询是否重复)
        try {
            $res = $this->model->getSpecsValueName($data['name']);
        } catch (\Exception $exception) {
            return show(config("status.error"), $exception->getMessage());
        }
        if ($res) {
            throw new Exception("该规格已存在，请重新添加");
        }

        // 调用 model 层保存数据方法
        try {
            $this->model->save($data);
        } catch (\Exception $exception) {
            return show(config("status.error"), $exception->getMessage());
        }

        // 返回添加数据的 id
        return $this->model->id;
    }

}