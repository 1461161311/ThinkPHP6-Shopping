<?php

namespace app\common\business;

use app\common\business\Specs as SpecsBus;
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


    /**
     * 获取商品所有的 sku 数据
     * @param $gids // specs_value_ids对应 sku 的 id 的数组
     * @param $flagValue // specs_value_ids 字段
     * @return array
     */
    public function dealGoodsSkus($gids, $flagValue)
    {
        // 取出 $gids 中的 key 值组成数组
        $specsValueKeys = array_keys($gids);

        $specsValueKey = [];
        $new = [];
        $specsValueIds = [];
        foreach ($specsValueKeys as $value) {
            $specsValueKey = explode(",", $value);  // explode():按照指定条件分割字符串
            foreach ($specsValueKey as $key => $v) {
                $new[$key][] = $v;
                $specsValueIds[] = $v;
            }
        }

        // 参数去重
        $specsValueIds = array_unique($specsValueIds);  // array_unique():去除重复的数据

        // 获取商品的所有 sku 数据的规格名
        $specsValues = $this->getNormalInIds($specsValueIds);

        // 拼接前端所需 list 数据
        $flagValue = explode(",", $flagValue);   // explode():按照指定条件分割字符串
        $result = [];
        foreach ($new as $key => $value) {
            $newValue = array_unique($value);   // array_unique(): 去除数组中重复的数据
            $list = [];
            foreach ($newValue as $v) {
                $list[] = [
                    "id" => $v, // sku_id
                    "name" => $specsValues[$v]['name'], // sku 规格名
                    // in_array():在 $flagValue 中搜索 sku 的 id , 显示该商品目前所选规格
                    "flag" => in_array($v, $flagValue) ? 1 : 0,
                ];
            }

            // 拼装数据
            $result[$key] = [
                // 该商品所有的规格名
                "name" => $specsValues[$newValue[0]]['specs_name'],
                "list" => $list,
            ];
        }
        return $result;
    }


    /**
     * 将数据拼接成 sku_id [sku_name => S , specs_name => 尺寸] 格式
     * @param $ids // 传入商品的所有 sku_id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getNormalInIds($ids)
    {
        if (!$ids) {
            return [];
        }
        // 根据商品的 sku 表中的 specs_value_ids 字段查询所属规格
        try {
            $result = $this->model->getNormalInIds($ids);
        } catch (\Exception $exception) {
            return [];
        }
        $result = $result->toArray();

        if (!$result) {
            return [];
        }
        // 查询所有规格名
        $specsNames = (new SpecsBus())->getNormalSpecs();
        if (!$specsNames) {
            return [];
        }
        // 处理数据,将规格转换成 id => name 的格式
        $specsNamesArrs = array_column($specsNames, "name", "id");

        // 将数据拼接成 sku_id [sku_name => S , specs_name => 尺寸] 格式
        $res = [];
        foreach ($result as $value) {
            $res[$value['id']] = [
                'name' => $value['name'],
                'specs_name' => $specsNamesArrs[$value['specs_id']] ?? "",
            ];
        }
        return $res;
    }


    /**
     * 转换成 [ 规格名:规格属性 ]  [ 颜色:红色 ]  的形式
     * @param $skuIdSpecsValueIds   // 传入规格 ids
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function dealSpecsValue($skuIdSpecsValueIds)
    {
        // 整理数据
        $ids = array_values($skuIdSpecsValueIds);   // array_values(): 取数组value值
        $ids = implode(",", $ids);   // implode(): 将数组转换成字符串
        $ids = array_unique(explode(",", $ids));    // explode():字符串转数组,array_unique():数组去重

        // 拼接数据,取得规格 id 对应的规格名
        $result = $this->getNormalInIds($ids);
        if (!$result) {
            return [];
        }

        $res = [];
        // 拼装数据,转换成   [ 颜色:红色  大小:L ]    形式
        foreach ($skuIdSpecsValueIds as $key => $value) {
            $value = explode(",", $value);
            $skuStr = [];
            foreach ($value as $v) {
                $skuStr[] = $result[$v]['specs_name'] . ":" . $result[$v]['name'];
            }
            $res[$key] = implode("  ", $skuStr);
        }

        return $res;
    }

}