<?php

namespace app\api\controller;

use app\api\validate\Cart as CartValidate;
use PhpMyAdmin\Config\Forms\Page\ImportForm;
use think\facade\Cache;
use app\common\business\Cart as CartBus;

/**
 * 购物车功能
 * Class Cart
 * @package app\api\controller
 */
class Cart extends AuthBase
{

    /**
     * 添加购物车场景
     * @return \think\response\Json
     */
    public function add()
    {
        // 验证请求
        if (!$this->request->isPost()) {
            return show(config("status.error"), "请求方式错误！");
        }

        // 验证参数
        $id = input("param.id", 0, "intval");
        $num = input("param.num", 0, "intval");
        $data = [
            "id" => $id,
            "num" => $num,
        ];
        $validate = new CartValidate();
        if (!$validate->scene('id')->check($data)) {
            return show(config("status.error"), $validate->getError());
        }

        // 调用 business 添加购物车方法
        try {
            $res = (new CartBus)->insertRedis($this->userId, $id, $num);
        } catch (\Exception $exception) {
            return show(config("status.error"), $exception->getMessage());
        }

        // 判断结果
        if ($res === FALSE) {
            return show(config("status.error"), "商品添加购物车失败");
        }
        return show(config("status.success"), "商品添加购物车成功");
    }


    /**
     * 购物车列表
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function lists()
    {
        $ids = input("param.id", "", "trim");

        $result = (new CartBus())->lists($this->userId, $ids);

        if ($result === FALSE) {
            return show(config("status.error"), "获取购物车失败");
        }
        return show(config("status.success"), "", $result);
    }


    /**
     * 购物车删除
     * @return \think\response\Json
     */
    public function delete()
    {
        // 验证数据
        if (!$this->request->isPost()) {
            return show(config("status.error"), "请求方式错误");
        }
        $id = input("param.id", 0, "intval");
        if (!$id) {
            return show(config("status.error"), "参数不合法");
        }

        // 调用方法
        $result = (new CartBus())->delete($this->userId, $id);

        // 验证结果
        if ($result === FALSE) {
            return show(config("status.error"), "删除失败");
        }
        return show(config("status.success"), "删除成功");
    }


    /**
     * 购物车修改
     * @return \think\response\Json
     */
    public function update()
    {
        // 验证参数
        if (!$this->request->isPost()) {
            return show(config("status.error"), "请求方式错误");
        }
        $id = input("param.id", 0, "intval");
        $num = input("param.num", 0, "intval");
        if (!$id || !$num) {
            return show(config("status.error"), "参数不合法");
        }

        // 更新购物车
        try {
            $result = (new CartBus())->update($this->userId, $id, $num);
        } catch (\Exception $exception) {
            return show(config("status.error"), $exception->getMessage());
        }

        // 验证结果
        if ($result === FALSE) {
            return show(config("status.error"));
        }
        return show(config("status.success"), "修改成功");

    }

}