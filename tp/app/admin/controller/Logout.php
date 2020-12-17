<?php
namespace app\admin\controller;

use app\BaseController;

class Logout extends BaseController {

    /**
     * 退出登录功能
     */
    public function index()
    {
        // 清除 session
        session(config("admin.session_admin"),null);

        // 调转页面
        return redirect(url("login/index"));

    }


}