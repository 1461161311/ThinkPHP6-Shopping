<?php
namespace app\api\exception;
use think\exception\Handle;
use think\Response;
use Throwable;

/**
 * 处理不可预知的错误
 * Class Http
 * @package app\api\exception
 */
class Http extends Handle {

    public $httpStatus = 500;
    /**
     * Render an exception into an HTTP response.
     *
     * @access public
     * @param \think\Request   $request
     * @param Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        if ($e instanceof \think\Exception){
            return show($e->getCode(),$e->getMessage());
        }


        if(method_exists($e, "getStatusCode")) {
            $httpStatus = $e->getStatusCode();
        } else {
            $httpStatus = $this->httpStatus;
        }
        // 添加自定义异常处理机制
        return show(config("status.error"), $e->getMessage(), [], $httpStatus);
    }
}