<?php

namespace app\api\controller;

use app\common\business\Goods as GoodsBus;

class Index extends ApiBase
{
    /**
     * 商城首页轮播图
     * @return \think\response\Json
     */
    public function getRotationChart()
    {
        $result = (new GoodsBus())->getRotationChart();
        return show(config("status.success"), "OK", $result);
    }


    /**
     * 商城首页推荐
     * @return \think\response\Json
     */
    public function cagegoryGoodsRecommend()
    {
        // 首页推荐的分类 id
        $categoryIds = [105, 110];
        $field = "sku_id as id , title , price , recommend_image as image";
        $result = (new GoodsBus())->categoryGoodsRecommend($categoryIds,$field);
        return show(config("status.success"), "OK", $result);
    }




}