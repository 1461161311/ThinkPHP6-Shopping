<?php

namespace app\admin\controller;

use think\Exception;
use think\facade\View;
use app\common\business\CategoryBus;
use app\admin\validate\Category as CategoryValidate;
use app\common\lib\Status as StatusLib;

class Category extends AdminBase
{
    /**
     * 首页列表排序
     * @return string
     */
    public function index()
    {
        // 查询子栏目和面包屑时使用
        $pid = input("param.pid", 0, "intval");

        $data = [
            "pid" => $pid,
        ];

        // 设置默认值，否则页面会显示未定义的索引
        $res = [
            'data' => 0
        ];

        // 调用 business 层列表方法
        // 列表分页功能
        try {
            $result = (new CategoryBus())->getLists($data, 3);
        } catch (\Exception $exception) {
            $result = [];
        }

        // 当点击查看分类子栏目时调用，面包屑导航功能
        if ($pid) {
            try {
                $res = (new CategoryBus())->getTree($pid);
            } catch (\Exception $exception) {
                $res = [];
            }
        }

        // 没有子栏目，却点击子栏目时，赋一个默认值。否则页面会显示未定义的索引
        if (!$result) {
            $result['data'] = 0;
            $result['total'] = 0;
            $result['per_page'] = 0;
            $result['current_page'] = 0;
        }

        // 调用模板引擎，将数据库中的数据传到模板中
        return View::fetch("", [
            // 列表分页数据
            "categorys" => $result,
            "pid" => $pid,
            // 面包屑数据
            "res" => $res,
        ]);
    }


    /**
     * 添加分类页面
     * @return string
     */
    public function add()
    {
        // 调用 business 层方法
        try {
            $categorys = (new CategoryBus())->getNormalCategorys();
        } catch (\Exception $exception) {
            $categorys = [];
        }

        // 调用模板引擎，将数据库中的数据传到模板中
        return View::fetch("", [
            // 使用 json_encode() 转换成 json 形式
            "categorys" => json_encode($categorys),
        ]);
    }


    /**
     * 添加分类功能
     * @return \think\response\Json
     */
    public function save()
    {
        // 判断请求
        if (!$this->request->isPost()) {
            return show(config("status.error"), "请求方式错误，非post请求");
        }

        // 验证参数
        // 从请求中获取数据
        $pid = input("param.pid", 0, "intval");
        $name = input("param.name", "", "trim");

        // 将数据存放至数组
        $data = [
            "pid" => $pid,
            "name" => $name,
        ];

        // 对数据进行验证
        $validate = (new CategoryValidate);
        if (!$validate->scene('category')->check($data)) {
            return show(config("status.error"), $validate->getError());
        }

        // 调用 business 添加方法
        try {
            $result = (new CategoryBus())->add($data);
        } catch (\Exception $exception) {
            return show(config("status.error"), $exception->getMessage());
        }

        // 验证
        if ($result) {
            return show(config("status.success"), "ok");
        }
        return show(config("status.error"), "新增分类失败");
    }


    /**
     * 当修改页面中 [排序] 字段时调用，修改数据库中内容
     * @return \think\response\Json
     */
    public function listorder()
    {
        // 获取数据
        $id = input("param.id", 0, "intval");
        $listorder = input("param.listorder", 0, "intval");

        // 验证数据
        $data = [
            "id" => $id,
            "listorder" => $listorder,
        ];
        $validate = (new CategoryValidate);
        if (!$validate->scene('listorder')->check($data)) {
            return show(config("status.error"), $validate->getError());
        }

        // 调用 business 层方法
        try {
            $result = (new CategoryBus())->listorder($id, $listorder);
        } catch (\Exception $exception) {
            return show(config("status.error"), $exception->getMessage());
        }

        // 验证
        if ($result) {
            return show(config("status.success"), "排序成功");
        }
        return show(config("status.error"), "排序失败");
    }

    /**
     * 修改分类状态功能
     * @return \think\response\Json
     */
    public function status()
    {
        // 获取参数
        $status = input("param.status", 0, "intval");
        $id = input("param.id", 0, "intval");

        // 验证参数
        $data = [
            "id" => $id,
            "status" => $status,
        ];
        // validate 验证参数
        $validate = (new CategoryValidate);
        if (!$validate->scene('status')->check($data)) {
            return show(config("status.error"), $validate->getError());
        }
        // 验证参数是否符合配置文件中的配置
        if (!array($status, StatusLib::getTableStatus())) {
            return show(config("status.error"), "参数错误");
        }

        // 调用 business 方法修改数据库
        try {
            $result = (new CategoryBus())->status($id, $status);
        } catch (\Exception $exception) {
            return show(config("status.error"), $exception->getMessage());
        }

        if ($result) {
            return show(config("status.success"), "状态更新成功");
        }

        return show(config("status.error"), "状态更新失败");

    }

    /**
     * 编辑分类页面
     * @return string
     */
    public function update()
    {
        // 需要修改的分类的 id
        $id = input("param.id", 0, "intval");

        // 获取所修改的分类的信息
        try {
            $res = (new CategoryBus())->getById($id);
        } catch (\Exception $exception) {
            $res = [];
        }

        // 获取父类信息
        try {
            $result = (new CategoryBus())->getNormalCategorys();
        } catch (\Exception $exception) {
            $result = [];
        }

        // 获取所要编辑分类的父类名称及 id
        $arr = [0 => "顶级分类"];
        foreach ($result as $value) {
            $arr[$value['id']] = $value['name'];
        }

        // 调用模板引擎，将数据库中的数据传到模板中
        return View::fetch("", [
            // $result 是数组形式，需要使用 json_encode() 转换成 json 形式
            "categorys" => json_encode($result),
            // 传入该类信息
            "res" => $res,
            // 传入父类名称及 id
            "arr" => $arr,
        ]);
    }


    /**
     * 编辑分类功能
     * @return \think\response\Json
     */
    public function saveUpdate()
    {
        if (!$this->request->isPost()) {
            return show(config("status.error"), "请求方式错误，非post请求");
        }

        // 验证参数
        // 从请求中获取数据
        $id = input("param.id", 0, "intval");
        $pid = input("param.pid", 0, "intval");
        $name = input("param.name", "", "trim");

        $data = [
            "id" => $id,
            "pid" => $pid,
            "name" => $name,
        ];

        // 验证数据
        $validate = new CategoryValidate();
        if (!$validate->scene('update')->check($data)) {
            return show(config("status.error"), $validate->getError());
        }

        // 调用 business 层方法
        try {
            $result = (new CategoryBus())->updateCategory($id, $data);
        } catch (\Exception $exception) {
            return show(config("status.error"), $exception->getMessage());
        }

        if ($result) {
            return show(config("status.success"), "修改成功");
        }
        return show(config("status.error"), "修改分类信息失败");
    }


}