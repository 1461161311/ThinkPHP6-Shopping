<?php

namespace app\admin\controller;

class Image extends AdminBase
{
    /**
     * 图片上传
     * @return \think\response\Json
     */
    public function upload()
    {
        // 判断请求方式
        if (!$this->request->isPost()) {
            show(config("status.error"), "请求错误，非POST请求");
        }

        // 接收图片参数
        $file = $this->request->file("file");

        // validate 验证数据
        try {
            // fileSize:文件大小 , fileExt:文件格式 , image:200,200,jpg : 文件宽高
            validate(['file' => ['fileSize:1048576', 'fileExt:jpg,png,gif']])->check(['file' => $file]);
        } catch (\think\exception\ValidateException $exception) {
            return show(config("status.error"), $exception->getMessage());
        }

        // 调用 facade 门面模式中的方法保存文件
        // disk('public'):读取 config 中的 fliesystem 文件
        // putFile('image',$file):创建 image 文件,并将 $file 存放在里面
        $fileName = \think\facade\Filesystem::disk('public')->putFile("image", $file);

        // 判断结果
        if (!$fileName) {
            return show(config("status.error"), "图片上传失败");
        }
        // 返回图片url地址,在页面显示时使用
        $imageUrl["image"] = "/upload/" . $fileName;
        return show(config("status.success"), "图片上传成功", $imageUrl);

    }

    /**
     * 文本编辑器中的图片上传
     * @return \think\response\Json
     */
    public function layUpload()
    {
        // 判断请求方式
        if (!$this->request->isPost()) {
            show(config("status.error"), "请求错误，非POST请求");
        }
        // 接收图片参数
        $file = $this->request->file("file");
        // validate 验证数据
        try {
            // fileSize:文件大小 , fileExt:文件格式 , image:200,200,jpg : 文件宽高
            validate(['file' => ['fileSize:1048576', 'fileExt:jpg,png,gif']])->check(['file' => $file]);
        } catch (\think\exception\ValidateException $exception) {
            return show(config("status.error"), $exception->getMessage());
        }
        // 调用 facade 门面模式中的方法保存文件
        // disk('public'):读取 config 中的 fliesystem 文件
        // putFile('image',$file):创建 image 文件,并将 $file 存放在里面
        $fileName = \think\facade\Filesystem::disk('public')->putFile("image", $file);

        // 判断结果
        if (!$fileName) {
            // 返回数据按照 layui 编辑器返回格式返回
            return json(["code" => 1,"data" => []],200);
        }

        // 设置成功上传后的返回数据
        $result = [
            "code" => 0,
            "data" => [
                "src" => "/upload/" . $fileName,
            ],
        ];
        // 返回数据按照 layui 编辑器返回格式返回
        return json($result,200);

    }


}