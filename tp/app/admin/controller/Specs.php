<?php

namespace app\admin\controller;

use app\common\lib\Status as StatusLib;
use think\facade\View;
use think\Exception;
use app\common\business\Specs as SpecsBus;
use app\admin\validate\Specs as SpecsValidate;

class Specs extends AdminBase
{
    /**
     * 规格分页显示
     * @return string
     */
    public function index()
    {
        // 调用 business 层分页查询方法
        $result = (new SpecsBus())->getLists(3);

        // 输出模板
        return View::fetch("", [
            "specs" => $result,
        ]);
    }


    /**
     * 添加页面模板
     * @return string
     */
    public function add()
    {
        return View::fetch();
    }


    /**
     * 添加规格功能
     * @return \think\response\Json
     */
    public function save()
    {
        // 获取参数
        $name = input("param.name", "", "trim");
        $data = [
            "name" => $name,
        ];

        // 验证参数
        $validate = new SpecsValidate();
        if (!$validate->scene('save')->check($data)) {
            return show(config("status.error"), $validate->getError());
        }

        // 调用 business 层添加方法
        try {
            $result = (new SpecsBus())->add($data);
        } catch (\Exception $exception) {
            return show(config("status.error"), $exception->getMessage());
        }

        // 判断结果
        if ($result) {
            return show(config("status.success"), "添加规格成功");
        }
        return show(config("status.error"), "添加规格失败");
    }


    /**
     * 修改规格状态码
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

        // 验证参数
        $validate = new SpecsValidate();
        if (!$validate->scene('status')->check($data)) {
            return show(config("status.error"), $validate->getError());
        }
        // 验证参数是否符合配置文件中的配置
        if (!array($status, StatusLib::getTableStatus())) {
            return show(config("status.error"), "参数错误");
        }

        // 调用 business 层修改状态码方法
        try {
            $result = (new SpecsBus())->status($id, $status);
        } catch (\Exception $exception) {
            return show(config("status.error"), $exception->getMessage());
        }

        // 判断结果
        if ($result) {
            return show(config("status.success"), "状态修改成功");
        }
        return show(config("status.error"), "状态修改失败");
    }


    /**
     * 修改页面模板
     * @return string|\think\response\Json
     */
    public function update()
    {
        // 获取数据
        $id = input("param.id", 0, "intval");

        $data = [
            "id" => $id,
        ];

        // 验证数据
        $validate = (new SpecsValidate());
        if (!$validate->scene('id')->check($data)) {
            return shwo(config("status.error"), $validate->getError());
        }

        // 调用 business 层根据 id 查询数据方法
        try {
            $result = (new SpecsBus())->getById($id);
        } catch (\Exception $exception) {
            return show(config("status.error"), $exception->getMessage());
        }

        // 判断结果
        if (!$result) {
            return show(config("status.error"), "未找到该规格");
        }

        // 输出模板
        return View::fetch("", [
            "specs" => $result,
        ]);
    }


    /**
     * 保存修改数据方法
     * @return \think\response\Json
     */
    public function updateSave()
    {
        // 验证请求方式
        if (!$this->request->isPost()) {
            return show(config("status.error"), "请求方式错误，非post请求");
        }

        // 获取数据
        $id = input("param.id", 0, "intval");
        $name = input("param.name", "", "trim");
        $data = [
            "id" => $id,
            "name" => $name,
        ];

        // 验证数据
        $validate = new SpecsValidate();
        if (!$validate->scene('updateSave')->check($data)) {
            return show(config("status.error"), $validate->getError());
        }

        // 调用 business 层更新数据方法
        try {
            $result = (new SpecsBus())->updateSpecs($id, $data);
        } catch (\Exception $exception) {
            return show(config("status.error"), $exception->getMessage());
        }

        // 判断结果
        if ($result) {
            return show(config("status.success"), "修改成功");
        }
        return show(config("status.error"), "修改失败");
    }


    /**
     * 商品添加时调用规格内容
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function dialog()
    {
        // 获取所有规格信息
        $result = (new SpecsBus())->getNormalSpecs();

        // 输出模板
        return View::fetch("", [
            "specs" => json_encode($result),
        ]);
    }
}