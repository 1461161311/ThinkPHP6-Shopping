<?php

namespace app\admin\business;

use app\common\model\mysql\AdminUser as AdminUserModel;

class AdminUser
{
    public $adminUserObj = null;

    /**
     * 构造方法. new一个Model层对象.
     * AdminUser constructor.
     */
    public function __construct()
    {
        $this->adminUserObj = new AdminUserModel();
    }


    /**
     * 登录验证
     * @param $data
     * @return bool|\think\response\Json
     * @throws \think\Exception
     */
    public function login($data)
    {
        // 根据传入的数据$data['username']查询数据库.
        $adminUser = $this->getAdminUserByUsername($data['username']);
        // 判断查询结果
        if (!$adminUser) {
            throw new \think\Exception("用户名输入错误");
        }
        // 判断密码
        if ($adminUser['password'] != md5($data['password'])) {
            throw new \think\Exception("密码输入错误");
        }

        // 更新数据库内容
        $updateData = [
            "last_login_time" => time(),    // 最后登录时间
            "last_login_ip" => request()->ip(),    // 最后登录ip
            "update_time" => time(),    // 数据更新时间
        ];
        // 调用Model层方法,根据ID来更新数据库
        $result = $this->adminUserObj->updateById($adminUser['id'], $updateData);
        // 判断是否更新成功,更新失败则登录失败
        if (empty($result)) {
            return show(config("status.error"), "登录失败");
        }

        // 将登录信息写入session
        session(config("admin.session_admin"), $adminUser);
        return true;
    }

    /**
     * 根据用户名查询数据
     * @param $username
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAdminUserByUsername($username)
    {
        // 调用Model层方法,根据用户名查询数据库
        $adminUser = $this->adminUserObj->getAdminUserByUsername($username);

        // 判断是否查询到数据与数据是否正常,返回空数组
        if (empty($adminUser) || $adminUser->status != config("status.mysql.table_normal")) {
            return [];
        }
        // 将从数据库获取到的数据转换为数组形式
        $adminUser = $adminUser->toArray();

        // 返回数组
        return $adminUser;

    }


}