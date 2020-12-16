<?php
// 开启强类型校验,所传入的参数必须是指定类型参数
// php默认是弱类型校验
declare(strict_types=1);

namespace app\admin\middleware;

/**
 * 中间件
 * Class Auth
 * @package app\admin\middleware
 */
class Auth
{

    public function handle($request, \Closure $next)
    {
        // 前置中间件(在应用中的代码执行之前,就会执行前置中间件)
        // 判断 session 中是否有用户信息,以及判断 $request->pathinfo() 中是否有 /login/ 字符串
        // preg_match() 执行正则表达式匹配,将 /login/ 在 $request->pathinfo() 中进行搜索. 搜索到了则返回 true
        if (empty(session(config("admin.session_admin"))) && !preg_match("/login/", $request->pathinfo())) {
            // url 返回的是对象,因为在上方开启强制类型.所以这里需要将类型转换成 string 字符串类型
            return redirect((string)url('login/index'));
        }

        // 回调函数. 将请求继续在应用中传递. 回调函数之前的代码为前置中间件,后面的代码为后置中间件
        $response = $next($request);

        // 后置中间件(是在应用中的代码执行完毕后,才会执行后置中间件)
//        if (empty(session(config("admin.session_admin"))) && $request->controller() != "Login"){
//            // url 返回的是对象,因为开启强制类型.所以这里需要将类型转换成 string 字符串类型
//            return redirect((string)url('login/index'));
//        }

        return $response;

    }


}
