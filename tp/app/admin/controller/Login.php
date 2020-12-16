<?php

namespace app\admin\controller;

use think\facade\View;

class Login extends AdminBase
{

    /**
     * 覆写父类方法,防止死循环.
     * 判断是否登录,如果登录,就跳转到后台首页
     */
    public function initialize()
    {
        // isLogin() 父类方法,用于判断是否登录
        if ($this->isLogin()){
            // 调转到后台首页
            return $this->redirect(url("index/index"));
        }
    }

    // 跳转登录页面
    public function index()
    {
        return View::fetch();
    }


    public function check()
    {
        if (!$this->request->isPost()){
            return show(config("status.error"),"请求方式错误，非post请求");
        }

        // 验证参数
        $username = $this->request->param("username","","trim");
        $password = $this->request->param("password","","trim");
        $captcha = $this->request->param("captcha","","trim");

        if (empty($username || empty($password) || empty($captcha))){
            return show(config("status.error"),"参数错误");
        }

        // 校验验证码
        if (!captcha_check($captcha)){
            // 验证码校验失败
            return show(config("status.error"),"验证码错误");
        }

        try {

            $adminUserObj = new AdminUser();
            $adminUser = $adminUserObj->getAdminUserByUsername($username);

            if (empty($adminUser) || $adminUser->status != config("status.mysql.table_normal")) {
                return show(config("status.error"), "用户名输入错误");
            }

            $adminUser = $adminUser->toArray();

            if ($adminUser['password'] != md5($password)) {
                return show(config("status.error"), "密码输入错误");
            }

            $updateData = [
                "last_login_time" => time(),
                "last_login_ip" => $this->request->ip(),
            ];

            $result = $adminUserObj->updateById($adminUser['id'], $updateData);

            if (empty($result)) {
                return show(config("status.error"), "登录失败");
            }

        }catch (\Exception $exception){
            // todo 记录日志，$exception->getMessage(); 内部错误信息一般不会暴露给用户，在日志中查看
            dump($exception);
            return show(config("status.error"),"内部错误，登录失败");
        }

        session(config("admin.session_admin"),$adminUser);

        return show(config("status.success"),"成功");
    }
}

