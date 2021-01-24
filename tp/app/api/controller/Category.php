<?php

namespace app\api\controller;

use app\common\business\Category as CategoryBus;

class Category extends ApiBase
{
    /**
     * 首页分类模块
     * @return \think\response\Json
     */
    public function index()
    {
        try {
            // 调用 common 中的 business 层方法
            $result = (new \app\common\business\Category())->getNormalCategorys("api");
        } catch (\Exception $exception) {
            // 状态码设置为 success,因为这只是在页面显示。如果设置error,前端可能会显示报错。那样不合理。所以设置成success
            return show(config("status.success"), "内部异常");
        }
        // 当获取数据为空
        if (!$result) {
            // 状态码设置为 success,因为这只是在页面显示。如果设置error,前端可能会显示报错。那样不合理。所以设置成success
            return show(config("status.success"), "数据为空");
        }

        // 调用 lib 库中的方法处理数据
        $result = \app\common\lib\Arr::getTree($result);    // 无限极分类
        $result = \app\common\lib\Arr::sliceTreeArr($result);   // 设置首页显示多少种分类
        // 返回 json 数据
        return show(config("status.success"), "ok", $result);
    }


    /**
     * 前台商品列表的分类显示功能
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function search()
    {
        // 获取点击的分类 id
        $id = $this->request->query();
        $id = explode("/", $id);
        // 实例化
        $categoryObj = (new CategoryBus());
        $FatherCategory = [];

        // 所点击的分类信息
        $Category = $categoryObj->getById($id[5], "id,name,pid,path");

        // 获取所点击的分类的父级分类信息
        if ($Category['path'] != null) {
            $FatherCategory = $categoryObj->getTree($Category['id']);
            $FatherCategory = [
                'id' => $FatherCategory['data'][0]['id'],
                'pid' => $FatherCategory['data'][0]['pid'],
                'name' => $FatherCategory['data'][0]['name'],
            ];
        }

        // 当点击父级分类时
        if ($Category['path'] == null) {
            $two = $categoryObj->getNormalByPid($Category['id']);
            $three = $categoryObj->getNormalByPid($two[0]['id']);
            $focus_ids = [$two[0]['id'], $three[0]['id']];
            $name = $Category['name'];

            // 当点击二级分类时
        } else if (!strstr($Category['path'], ',')) {
            $two = $categoryObj->getNormalByPid($Category['pid']);
            $three = $categoryObj->getNormalByPid($Category['id']);
            $focus_ids = [$Category['id'], $three[0]['id']];
            $name = $FatherCategory['name'];

            // 当点击三级分类时
        } else {
            $twoCategory = $categoryObj->getById($Category['pid']);
            $two = $categoryObj->getNormalByPid($twoCategory['pid']);
            $three = $categoryObj->getNormalByPid($Category['pid']);
            $focus_ids = [$Category['pid'], $Category['id']];
            $name = $FatherCategory['name'];
        }

        // 返回数据的数据格式
        $result = [
            "name" => $name,
            "focus_ids" => $focus_ids,
            "list" => [$two, $three],
        ];

        return show(config("status.success"), "OK", $result);

    }


    /**
     * 在列表页点击二级分类获取三级分类信息
     * @return \think\response\Json
     */
    public function sub()
    {
        $id = input("param.id", 0, "intval");
        $res = (new CategoryBus())->getNormalByPid($id, "id,name");
        return show(config("status.success"), "OK", $res);
    }

}

