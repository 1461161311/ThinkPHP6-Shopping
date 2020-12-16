<?php

namespace app\admin\controller;

use app\BaseController;

class Test extends BaseController
{

    public function index()
    {
        dump(session(config("admin.session_admin")));
        dump(empty(session(config("admin.session_admin"))));
    }



}
