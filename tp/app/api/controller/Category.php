<?php

namespace app\api\controller;

class Category extends ApiBase
{
    /**
     * 首页分类模块
     * @return \think\response\Json
     */
    public function index()
    {
        try{
            // 调用 common 中的 business 层方法
            $result = (new \app\common\business\CategoryBus())->getNormalCategorys("api");
        }catch(\Exception $exception){
            // 状态码设置为 success,因为这只是在页面显示。如果设置error,前端可能会显示报错。那样不合理。所以设置成success
            return show(config("status.success"),"内部异常");
        }
        // 当获取数据为空
        if (!$result){
            // 状态码设置为 success,因为这只是在页面显示。如果设置error,前端可能会显示报错。那样不合理。所以设置成success
            return show(config("status.success"),"数据为空");
        }

        // 调用 lib 库中的方法处理数据
        $result = \app\common\lib\Arr::getTree($result);    // 无限极分类
        $result = \app\common\lib\Arr::sliceTreeArr($result);   // 设置首页显示多少种分类
        // 返回 json 数据
        return show(config("status.success"),"ok",$result);
    }


}

