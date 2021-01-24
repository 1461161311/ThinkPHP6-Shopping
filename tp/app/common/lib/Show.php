<?php

namespace app\common\lib;

class Show
{
    /**
     * 返回成功 json 数据
     * @param array $data   返回数据
     * @param string $message   返回信息
     * @return \think\response\Json
     */
    public static function success($data = [], $message = "OK")
    {
        $result = [
            "status" => config("status.success"),
            "message" => $message,
            "return" => $data,
        ];

        return json($result);

    }


    /**
     * 返回失败 json 数据
     * @param array $data   返回数据
     * @param string $message   返回信息
     * @param int $status  状态码
     * @return \think\response\Json
     */
    public static function error($data = [], $message = "error", $status = 0)
    {
        $return = [
            "status" => $status,
            "message" => $message,
            "return" => $data,
        ];

        return json($return);
    }

}