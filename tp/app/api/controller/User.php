<?php

namespace app\api\controller;

use app\common\business\User as UserBis;

class User extends AuthBase
{
    /**
     * 个人信息主页
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {
        // 获取用户所有信息     $this->userId 是获取父类中的 userId 数据
        $user = (new UserBis())->getNormalUserById($this->userId);

        // 取出页面需要的用户信息，如密码等敏感信息不要返回
        $resultUser = [
            "id" => $this->userId,
            "username" => $user['username'],
            "sex" => $user['sex'],
        ];

        // 返回数据
        return show(config("status.success"), "ok", $resultUser);
    }

    /**
     * 更新用户个人信息
     * PUT 方式
     * @return \think\response\Json
     * @throws \think\Exception
     */
    public function update()
    {
        // 获取数据
        $username = input('username', '', 'trim');
        $sex = input('sex', 0, 'intval');

        // 存放数据
        $data = [
            'username' => $username,
            'sex' => $sex,
        ];
        // 调用 validate 类验证数据
        $validate = (new \app\api\validate\User())->scene('update_user');
        if (!$validate->check($data)) {
            return show(config("status.error"), $validate->getError());
        }

        // 调用 business 层更新方法
        $userBisObj = new UserBis();
        $user = $userBisObj->update($this->userId, $data);

        // 更新失败
        if (!$user) {
            show(config("status.error"), "更新失败");
        }

        return show(config("status.success"), "更新成功");

    }
}
