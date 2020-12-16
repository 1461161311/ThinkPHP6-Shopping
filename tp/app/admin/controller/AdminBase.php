<?php

namespace app\admin\controller;

use app\BaseController;
use think\exception\HttpResponseException;

class AdminBase extends BaseController
{
    // 用于存放 session
    public $adminUser = null;

    /**
     * 用于判断用户是否登录,如果未登录则返回登录界面
     */
    public function initialize()
    {
        // 继承父类方法
        parent::initialize();
        // 判断是否登录
        if (empty($this->isLogin())){
            // 返回登录界面
            return $this->redirect(url("login/index"),302);
        }
    }

    /**
     * 判断是否登录
     */
    public function isLogin()
    {
        // 接收 session 内容
        $this->adminUser = session(config("admin.session_admin"));
        // 判断 session 是否有内容
        if (empty($this->adminUser)) {
            return false;
        }
        return true;
    }

    /**
     * 以 Base 控制器来控制跳转需要此方法.
     * throw new HttpResponseException : 捕获异常
     * @param mixed 动态参数 ...$args
     */
    public function redirect(...$args){
        throw new HttpResponseException(redirect(...$args));
    }
}

