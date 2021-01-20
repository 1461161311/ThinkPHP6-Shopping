<?php

namespace app\admin\validate;

use think\Validate;

class Goods extends Validate
{
    // 需要验证的字段以及规则
    protected $rule = [
        'title' => 'require|chsDash|max:32',           // 标题
        'category_id' => 'require',     // 分类
        'sub_title' => 'require|chsDash|max:32',       // 副标题
        'promotion_title' => 'require|chsDash|max:64', // 促销语
        'keywords' => 'require|chsDash',        // 关键词
        'goods_unit' => 'require|chsAlpha',      // 商品单位 (个、件)
        'is_show_stock' => 'require|in:0,1',   // 是否显示库存
        'stock' => 'require|integer',           // 库存
        'goods_specs_type' => 'require|in:1,2',// 商品规格 (1,统一 2,多规格)
        'big_image' => 'require',       // 大图
        'carousel_image' => 'require',  // 推荐图
        'recommend_image' => 'require', // 轮播图
        'description' => 'require|max:500',     // 商品详情
        'skus' => 'require',     // 商品sku
    ];

    // 错误提示
    protected $message = [
        'title.chsDash' => '标题必须是汉字、字母、数字和下划线_及破折号',
        'title.max' => '标题必须在32个字以内',
        'title.require' => '标题必须',
        'category_id' => '分类必须',
        'sub_title.chsDash' => '副标题必须是汉字、字母、数字和下划线_及破折号',
        'promotion_title.chsDash' => '促销语必须是汉字、字母、数字和下划线_及破折号',
        'promotion_title.max' => '促销语必须在64个字以内',
        'promotion_title.require' => '促销语必须',
        'keywords.chsDash' => '关键字必须是汉字、字母、数字和下划线_及破折号',
        'keywords.require' => '关键字必须',
        'goods_unit.chsDash' => '商品单位必须是汉字、字母',
        'goods_unit.require' => '商品单位必须',
        'is_show_stock.integer' => '是否显示库存必须是数字',
        'is_show_stock.require' => '是否显示库存必须',
        'stock.in' => '库存数值错误',
        'stock.require' => '库存必须',
        'goods_specs_type.in' => '商品规格数值错误',
        'goods_specs_type.require' => '商品规格必须',
        'big_image' => '大图',
        'carousel_image' => '推荐图',
        'recommend_image' => '轮播图',
        'description.max' => '商品详情必须在500个字符以内',
        'description.require' => '商品详情必须',
        'skus.require' => '商品sku必须',
    ];


    // 设置验证场景
    protected $scene = [
        // 新增分类时的场景
        'goods' => ['title', 'category_id', 'sub_title', 'promotion_title', 'keywords',
            'goods_unit', 'is_show_stock', 'stock', 'goods_specs_type', 'big_image',
            'carousel_image', 'recommend_image', 'description'],
        'goods_sku' => ['title', 'category_id', 'sub_title', 'promotion_title', 'keywords',
            'goods_unit', 'is_show_stock', 'stock', 'goods_specs_type', 'big_image',
            'carousel_image', 'recommend_image', 'description', 'skus'],
        'saveUpdate' => ['title', 'sub_title', 'promotion_title', 'keywords',
            'goods_unit', 'is_show_stock', 'stock',  'description'],
    ];

}


