<?php
// 开启强类型校验,所传入的参数必须是指定类型参数
// php默认是弱类型校验
declare(strict_types=1);

namespace app\admin\middleware;

/**
 * 中间件验证登录
 * Class Auth
 * @package app\admin\middleware
 */
class Auth
{
    public function handle($request, \Closure $next)
    {
        // 前置中间件(在应用中的代码执行之前,就会执行前置中间件)

        // 当用户未登录时，访问后台页面，会跳转至登录页面
        // preg_match() 执行正则表达式匹配,将 /login/ 在 $request->pathinfo() 中进行搜索. 搜索到了则返回 true
        if (empty(session(config("admin.session_admin"))) && !preg_match("/login/", $request->pathinfo())) {
            // url 返回的是对象,因为在上方开启强制类型.所以这里需要将类型转换成 string 字符串类型
            return redirect((string)url('login/index'));
        }

        // 当用户登录后，访问登录页面，会跳转至后台首页
        if (!empty(session(config("admin.session_admin"))) && preg_match("/login/", $request->pathinfo())) {
            // url 返回的是对象,因为在上方开启强制类型.所以这里需要将类型转换成 string 字符串类型
            return redirect((string)url('index/index'));
        }


        // 回调函数. 将请求继续在应用中传递. 回调函数之前的代码为前置中间件,后面的代码为后置中间件
        $response = $next($request);

        // 后置中间件(是在应用中的代码执行完毕后,才会执行后置中间件)

        return $response;

    }


}
