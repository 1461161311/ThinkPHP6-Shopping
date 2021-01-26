<?php

namespace app\api\controller\mall;

use app\api\controller\ApiBase;
use app\common\business\Category as CategoryBus;
use app\common\business\Goods as GoodsBus;

class Detail extends ApiBase
{
    /**
     * 商品详情页
     * @return \think\response\Json
     */
    public function index()
    {
        // 获取商品 sku_id
        $id = input("param.id", 0, "intval");

        if (!$id) {
            return show(config("status.error"));
        }

        // 获取商品数据
        $result = (new GoodsBus())->getGoodsDetailBySkuId($id);

        if (!$result) {
            return show(config("status.error"));
        }

        return show(config("status.success"),"OK",$result);
    }

}