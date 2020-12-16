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
        if ($this->isLogin()) {
            // 调转到后台首页
            return $this->redirect(url("index/index"));
        }
    }

    /**
     * 调转登录页面
     * @return string
     */
    public function index()
    {
        return View::fetch();
    }

    /**
     * 登录功能
     * @return \think\response\Json
     */
    public function check()
    {
        // 验证请求
        if (!$this->request->isPost()) {
            return show(config("status.error"), "请求方式错误，非post请求");
        }

        // 验证参数
        // 从请求中获取数据
        $username = $this->request->param("username", "", "trim");
        $password = $this->request->param("password", "", "trim");
        $captcha = $this->request->param("captcha", "", "trim");

        // 将数据存放至数组
        $data = [
            'username' => $username,
            'password' => $password,
            'captcha' => $captcha
        ];

        // 对数据进行验证
        $validate = new \app\admin\validate\AdminUser();
        if (!$validate->check($data)) {
            return show(config("status.error"), $validate->getError());
        }

        // 验证验证码
        if (!captcha_check($captcha)){
            return show(config("status.error"), "验证码输入错误");
        }

        // 将获取的数据与数据库中的数据对比,并捕获异常
        try {
            $adminUserObj = new \app\admin\business\AdminUser();
            $result = $adminUserObj->login($data);
        } catch (\Exception $exception) {
            return show(config("static.error"), $exception->getMessage());
        }

        // 判断从 business 层传过来的数据是否正确
        if ($result) {
            return show(config("status.success"), "登录成功");
        }

        // 如果 business 层的数据为 false 则返回错误提示
        return show(config("status.error"), $validate->getError());
    }
}

