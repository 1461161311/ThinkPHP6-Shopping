<?php
return [
    // 手机验证码前缀，方便查询
    "code_pre" => "mall_code_pre_",
    // 手机验证码失效时间
    "code_expire" => 60,

    // token 前缀
    "token_pre" => "mall_token_pre_",

    // 购物车前缀
    "cart_pre" => "mall_cart_",

    // 延迟队列 - 订单是否需要取消状态检查
    "order_status_key" => "order_status",

    // 订单失效时间 (20分钟)
    "order_expire" => 20 * 60,

];

