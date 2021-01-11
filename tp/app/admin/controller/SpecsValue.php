<?php

namespace app\admin\controller;

use app\common\business\SpecsValue as SpecsValueBus;
use app\admin\validate\SpecsValue as SpecsValueValidate;
use app\common\lib\Status as StatusLib;

class SpecsValue extends AdminBase
{
    /**
     * 返回指定的规格下的数据
     * @return \think\response\Json
     */
    public function getBySpecsId()
    {
        // 接收数据
        $specsId = input("param.specs_id", 0, "intval");
        if (!$specsId) {
            return show(config("status.success"), "该规格没有数据");
        }

        // 调用 business 方法
        $result = (new SpecsValueBus())->getBySpecsId($specsId);

        return show(config("status.success"), "ok", $result);
    }


    /**
     * 添加功能
     * @return \think\response\Json
     */
    public function save()
    {
        // 获取数据
        $specsId = input("param.specs_id", 0, "intval");
        $name = input("param.name", "", "trim");
        $data = [
            "specs_id" => $specsId,
            "name" => $name,
        ];

        // 验证数据
        $validate = new SpecsValueValidate();
        if (!$validate->scene('save')->check($data)) {
            return show(config("status.error"), $validate->getError());
        }

        // 调用 business 层添加方法
        $result = (new SpecsValueBus())->add($data);

        // 判断结果
        if ($result) {
            return show(config("status.success"), "添加规格成功");
        }
        return show(config("status.error"), "添加规格失败");
    }


    /**
     * 修改状态码 (假删除)
     * @return \think\response\Json
     */
    public function status()
    {
        // 获取参数
        $id = input("param.id", 0, "intval");
        $status = input("param.status", 0, "intval");
        $data = [
            "id" => $id,
            "status" => $status,
        ];

        // validate 验证参数
        $validate = (new SpecsValueValidate);
        if (!$validate->scene('status')->check($data)) {
            return show(config("status.error"), $validate->getError());
        }
        // 验证参数是否符合配置文件中的配置
        if (!array($status, StatusLib::getTableStatus())) {
            return show(config("status.error"), "参数错误");
        }

        // 调用 business 层修改方法
        $result = (new SpecsValueBus())->updateStatus($id, $data);

        // 判断结果
        if (!$result) {
            return show(config("status.error"), "删除失败");
        }
        return show(config("status.success"), "删除成功");
    }


}