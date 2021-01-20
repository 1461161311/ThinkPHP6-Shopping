<?php

namespace app\admin\controller;

use app\admin\validate\Goods as GoodsValidate;
use app\common\business\Goods as GoodsBus;
use app\common\business\CategoryBus as CategoryBus;
use think\facade\View;

class Goods extends AdminBase
{
    /**
     * 商品列表
     * @return string
     */
    public function index()
    {
        $data = [];
        $title = input("param.title", "", "trim");
        $time = input("param.time", "", "trim");
        $ti = $time;

        if (!empty($title)) {
            $data['title'] = $title;
        }
        if (!empty($time)) {
            $data['create_time'] = explode(" - ", $time);
        }

        $result = (new GoodsBus())->getLists($data, 3);

        if (empty($data['title'])) {
            $data['title'] = "";
        }
        if (empty($data['create_time'])) {
            $data['create_time'] = "";
        }

        $data['create_time'] = $ti;

        return View::fetch("", [
            "goods" => $result,
            "search" => $data,
        ]);
    }

    /**
     * 修改商品状态
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function status()
    {
        $id = input("param.id", 0, "intval");
        // 商品状态码
        $status = input("param.status", 3, "intval");
        // 商品是否首页推荐
        $index = input("param.is_index_recommend", 3, "intval");

        $data = [
            "status" => $status,
            "is_index_recommend" => $index,
        ];

        // 验证是要修改状态码还是修改是否首页推荐
        if ($data['status'] == 0 || $data['status'] == 1 || $data['status'] == 99) {
            $result = (new GoodsBus())->status($id, $data['status']);
        } else {
            $result = (new GoodsBus())->isIndex($id, $data['is_index_recommend']);
        }

        // 验证结果
        if ($result) {
            return show(config("status.success"), "状态更新成功");
        }
        return show(config("status.error"), "状态更新失败");
    }


    /**
     * 商品添加页
     * @return string
     */
    public function add()
    {
        return View::fetch();
    }


    /**
     * 商品添加功能
     * @return \think\response\Json
     * @throws \think\Exception
     */
    public function save()
    {
        // 判断请求
        if (!$this->request->isPost()) {
            return show(config("status.error"), "请求错误，非POST请求");
        }

        // 获取页面传回的所有数据
        $data = input("param.");

        // 验证页面的 token , 防止 csrf 攻击
//        $check = $this->request->checkToken('__token__');
//        if (!$check) {
//            return show(config("status.error"), "非法请求");
//        }

        // 验证参数
        $validate = new GoodsValidate();
        // 当商品选择单规格时
        if ($data['goods_specs_type'] == 1) {
            if (!$validate->scene('goods')->check($data)) {
                return show(config("status.error"), $validate->getError());
            }
            // 当商品选择多规格时
        } else {
            if (!$validate->scene('goods_sku')->check($data)) {
                return show(config("status.error"), $validate->getError());
            }
        }

        // 处理数据
        // category_path_id 是商品的分类路径,保存了多个分类id.最后一位id,就是商品所属分类
        $data['category_path_id'] = $data['category_id'];
        $result = explode(",", $data['category_path_id']);  // explode():将字符串分割成数组
        $data['category_id'] = end($result);    // end():取数组的最后一位


        // 当商品选择单规格时
        if ($data['goods_specs_type'] == 1) {
            // 处理数据,页面中的字段与数据库字段不同
            $data['price'] = $data['market_price'];
            $data['cost_price'] = $data['sell_price'];
            // 删除不需要的字段
            unset($data['sell_price'], $data['market_price']);

            // 调用 business 层添加商品以及sku方法
            $res = (new GoodsBus())->insertData($data);
        } else {
            // 调用 business 层添加商品以及sku方法
            $res = (new GoodsBus())->insertData($data);
        }

        // 验证参数
        if (!$res) {
            return show(config("status.error"), "商品新增失败");
        }
        return show(config("status.success"), "商品新增成功");
    }


    /**
     * 编辑页面
     * @return string|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function update()
    {
        // 获取 id 查询要修改的数据
        $id = input("param.id", 0, "intval");

        // 查询数据
        $result = (new GoodsBus())->getById($id);

        // 分割分类信息,方便在页面回显
        $path = explode(',', $result['category_path_id']);
        $res = (new CategoryBus());
        $arr = [];
        // 以商品所属分类 id , 查询分类信息
        foreach ($path as $value) {
            $category = $res->getById($value);
            $arr[] = $category['name'];
        }

        // 页面回显分类
        $result['category_path_name'] = $arr;
        // 页面回显轮播图
        $carousel_image['image'] = explode(',', $result['carousel_image']);

        return View::fetch("", [
            "goods" => $result,
            // 回显轮播图
            "carousel_image" => $carousel_image,
        ]);

    }


    /**
     * 编辑功能
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function saveUpdate()
    {
        // 验证请求
        if (!$this->request->isPost()) {
            show(config("status.error"), "请求类型错误，非POST请求");
        }

        // 接收参数
        $data = input("param.");

        // 验证参数
        $validate = new GoodsValidate();
        if (!$validate->scene('saveUpdate')->check($data)) {
            return show(config("status.error"), $validate->getError());
        }

        // 当重新选择分类时
        if (!empty($data['category_id'])) {
            // 处理数据
            // category_path_id 是商品的分类路径,保存了多个分类id.最后一位id,就是商品所属分类
            $data['category_path_id'] = $data['category_id'];
            $result = explode(",", $data['category_path_id']);  // explode():将字符串分割成数组
            $data['category_id'] = end($result);    // end():取数组的最后一位
        }

        // 处理数据,页面中的字段与数据库字段不同
        $data['price'] = $data['market_price'];
        $data['cost_price'] = $data['sell_price'];
        // 删除不需要的字段
        unset($data['sell_price'], $data['market_price']);

        // 当页面并没有修改图片时
        if ($data['carousel_image'] == "") {
            unset($data['carousel_image']);
        }
        if ($data['recommend_image'] == "") {
            unset($data['recommend_image']);
        }

        // 调用 business 层修改方法
        $result = (new GoodsBus())->saveUpdate($data['id'], $data);

        // 验证数据
        if ($result) {
            return show(config("status.success"), "商品编辑成功");
        }
        return show(config("status.error"), "商品编辑失败");
    }

}

