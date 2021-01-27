<?php

namespace app\api\controller\mall;

use app\api\controller\ApiBase;
use app\common\business\Category as CategoryBus;
use app\common\business\Goods as GoodsBus;

class Lists extends ApiBase
{

    /**
     * 前台商品列表页
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        // 获取数据
        $data = input("param.");
        // 实例化
        $categoryObj = new CategoryBus();
        $goodsObj = new GoodsBus();

        // 默认要查询的字段
        $field = "sku_id as id,title,price,recommend_image as image";

        // 默认的排序方式
        $order = [
            "id" => "desc",
        ];
        // 当页面选择排序方法时
        if (!empty($data["field"])) {
            // 按照价钱排序
            if ($data["field"] == "price") {
                if ($data["order"] == 1) {
                    $order = [
                        "price" => "by",
                        "id" => "desc",
                    ];
                } else {
                    $order = [
                        "price" => "desc",
                        "id" => "desc",
                    ];
                }
            }
        }

        // 当页面使用搜索功能时
        if (!empty($data["keyword"])) {
            // 模糊查询条件
            $title['title'] = $data["keyword"];
            // 调用 business 层模糊查询方法
            $res = $goodsObj->getLists($title, $order, $data['page_size'], $field);

            // 当页面没有使用搜索功能时
        } else {
            // 根据传过来的分类 id ,查询出该分类信息
            $category = $categoryObj->getById($data['category_id']);
            // 当点击父级分类时
            if ($category['path'] == null) {
                $two = $categoryObj->getNormalByPid($category['id']);
                $three = $categoryObj->getNormalByPid($two[0]['id']);
                $res = $goodsObj->getByCategoryId($three[0]['id'], $field, $data['page_size'], $order);
                // 当点击二级分类时
            } elseif (!strstr($category['path'], ',')) {
                $three = $categoryObj->getNormalByPid($category['id']);
                $res = $goodsObj->getByCategoryId($three[0]['id'], $field, $data['page_size'], $order);
                // 当点击三级分类时
            } else {
                $res = $goodsObj->getByCategoryId($category['id'], $field, $data['page_size'], $order);
            }
        }

        // 返回数据的数据格式
        $result = [
            // 总页数
            'total_page_num' => $res['last_page'],
            // 查询商品总数
            'count' => $res['total'],
            // 当前页
            'page' => $data['page'],
            // 每页显示多少条
            'page_size' => $data['page_size'],
            // 商品数据
            'list' => $res['data'],
        ];

        return show(config("status.success"), "OK", $result);


    }

}