<?php /*a:1:{s:78:"E:\Code\Git-tp6\ThinkPHP6-Shopping_Project\tp\app\admin\view\specs\update.html";i:1610346598;}*/ ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>商品规格修改</title>
    <link rel="stylesheet" href="/static/admin/lib/layui-v2.5.4/css/layui.css" media="all">
    <link rel="stylesheet" href="/static/admin/css/public.css" media="all">
</head>
<body>
<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>商品规格修改</legend>
</fieldset>
<form class="layui-form" action="">
    <div class="layui-form-item">
        <div class="layui-input-inline">
            <input type="hidden" name="id" value="<?php echo htmlentities($specs['id']); ?>" lay-verify="id" autocomplete="off" placeholder="<?php echo htmlentities($specs['id']); ?>"
                   class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label" style="width: 200px;">规格名</label>
        <div class="layui-input-inline">
            <input type="text" name="name" lay-verify="name" autocomplete="off" placeholder="<?php echo htmlentities($specs['name']); ?>"
                   class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label" style="width: 200px;"></label>
        <div class="layui-input-inline">
            <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="demo1">提交修改</button>
        </div>
    </div>
</form>
<script src="/static/admin/lib/layui-v2.5.4/layui.js" charset="utf-8"></script>
<script src="/static/admin/lib/jquery-3.4.1/jquery-3.4.1.min.js" charset="utf-8"></script>
<script src="/static/admin/js/common.js" charset="utf-8"></script>
<script>
    layui.use(['form', 'laypage'], function () {
        var form = layui.form;

        //监听提交
        form.on('submit(demo1)', function (data) {
            console.log(data.field, '最终的提交信息')
            data = data.field;
            let url = '';
            $.ajax({
                type: "POST",
                data: data,
                url: 'updateSave',
                success(res) {
                    if (res.status == 1) {
                        layer.msg("修改成功", function () {
                            window.parent.location.reload();
                            parent.layer.closeAll('index');
                        });
                    } else {
                        layer.msg(res.message)
                        return false;
                    }
                },
            })
            return false;
        });

    })
</script>
</body>
</html>
