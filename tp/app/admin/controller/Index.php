<?php
namespace app\admin\controller;

use app\BaseController;
use think\facade\View;

class Index extends AdminBase {
    public function index()
    {
        return View::fetch();
    }

    public function welcome()
    {
        return View::fetch();
    }

    public function tt()
    {
        halt(session(config_path("admin.session_admin")));
    }


}