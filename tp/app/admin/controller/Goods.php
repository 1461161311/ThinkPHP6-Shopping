<?php

namespace app\admin\controller;

use app\admin\validate\Goods as GoodsValidate;
use app\common\business\Goods as GoodsBus;

class Goods extends AdminBase
{
    /**
     * 商品列表页
     * @return \think\response\View
     */
    public function index()
    {
        return view();
    }

    /**
     * 商品添加页
     * @return \think\response\View
     */
    public function add()
    {
        return view();
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
        $check = $this->request->checkToken('__token__');
        if (!$check){
            return show(config("status.error"),"非法请求");
        }

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

}

