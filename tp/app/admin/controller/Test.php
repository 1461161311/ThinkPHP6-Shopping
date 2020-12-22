<?php

namespace app\admin\controller;

use app\BaseController;
use SUBMAIL_PHP_SDK\lib\MESSAGEsend;

//require 'ThinkPHP6-Shopping_Project/tp/extend/SUBMAIL_PHP_SDK/config.php';
//require_once('ThinkPHP6-Shopping_Project/tp/extend/SUBMAIL_PHP_SDK/SUBMAILAutoload.php');

class Test extends BaseController
{

    public function index()
    {
        dump(session(config("admin.session_admin")));
        dump(empty(session(config("admin.session_admin"))));
    }


    // 测试短信发送
    public function tt()
    {
        $message_configs = [
            'server' => 'http://api.mysubmail.com/',
            'appid' => '59079',
            'appkey' => 'ad47b92f5bf1f824ddffa11bdf0dca00',
            'sign_type' => 'normal'
        ];

        // 初始化 MESSAGEXsend 类
        $submail=new MESSAGEsend(config("Submail"));
        // 设置短信接收的11位手机号码
        $submail->setTo('17549207021');
        // 设置短信正文
        $submail->SetContent('【毛越】您的短信验证码：4438，请在10分钟内输入。');
        // 调用 send 方法发送短信
        $send=$submail->send();
        // 打印服务器返回值
        dump($send);

    }



}
