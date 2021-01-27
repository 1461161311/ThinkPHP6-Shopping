<?php

namespace app\api\controller\mall;

use app\api\controller\AuthBase;
use app\common\business\Cart as CartBus;

class Init extends AuthBase
{
    /**
     * 首页显示购物车内商品数量
     * @return \think\response\Json
     */
    public function index()
    {
        if (!$this->request->isPost()) {
            return show(config("status.error"), "请求错误");
        }
        $count = (new CartBus())->getCount($this->userId);

        $data = [
            "cart_num" => $count,
        ];
        return show(config("status.success"), "OK", $data);

    }


}