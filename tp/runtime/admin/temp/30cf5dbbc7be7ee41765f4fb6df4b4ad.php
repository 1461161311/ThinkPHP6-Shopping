<?php /*a:1:{s:81:"E:\Code\Git-tp6\ThinkPHP6-Shopping_Project\tp\app\admin\view\category\update.html";i:1609940098;}*/ ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>编辑商品分类</title>
    <link rel="stylesheet" href="/static/admin/lib/layui-v2.5.4/css/layui.css" media="all">
    <link rel="stylesheet" href="/static/admin/css/public.css" media="all">
</head>
<body>
<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>商品分类编辑</legend>
</fieldset>

<form class="layui-form" action="">
    <div class="layui-form-item">
        <div class="layui-input-inline">
            <input type="hidden" name="id" value="<?php echo htmlentities($res['id']); ?>" lay-verify="id" autocomplete="off" placeholder="<?php echo htmlentities($res['id']); ?>"
                   class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-inline">
            <label class="layui-form-label" style="width: 200px;">所属父级分类</label>
            <div class="layui-input-inline">
                <select name="pid" id="classif"></select>
            </div>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label" style="width: 200px;">商品分类名</label>
        <div class="layui-input-inline">
            <input type="text" name="name" value="<?php echo htmlentities($res['name']); ?>" lay-verify="name" autocomplete="off" placeholder="<?php echo htmlentities($res['name']); ?>"
                   class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label" style="width: 200px;"></label>
        <div class="layui-input-inline">
            <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="demo1">提交编辑</button>
        </div>
        <div class="layui-input-inline">
            <a class="layui-btn layui-btn-danger" href="<?php echo url('index'); ?>">取消编辑</a>
        </div>
    </div>
</form>
<script src="/static/admin/lib/layui-v2.5.4/layui.js" charset="utf-8"></script>
<script src="/static/admin/lib/jquery-3.4.1/jquery-3.4.1.min.js" charset="utf-8"></script>
<script src="/static/admin/js/common.js" charset="utf-8"></script>
<script>
    layui.use(['form', 'laypage'], function () {
        var form = layui.form;

        function _classif(res = []) {
            // res 分类数据 先期模拟
            let temps = '<option value="<?php echo htmlentities($res['pid']); ?>"><?php echo $arr[$res['pid']]; ?></option>';
            temps += '<option value="0">-| 顶级分类</option>';
            // 输出 controller 层传过来的分类数据，
            // 注意：框架为了避免出现 XSS 安全问题，默认变量输出会进行转义。加 |raw 参数可避免转义
            var data = <?php echo $categorys; ?>

            let toTrees = toTree(data);
            for (let item of toTrees) {
                temps += `<optgroup  data-id="${item["id"]}">`;
                temps += `<option  data-id="${item['id']}" value="${item['id']}">-| ${item["name"]}</option>`
                if (item['children'] && item['children'].length > 0) {
                    for (let child of item['children']) {
                        temps += `<option  data-id="${child['id']}" value="${child['id']}"> &nbsp;&nbsp;&nbsp;--| ${child["name"]} </option>`
                    }
                }
                temps += `</optgroup>`;
            }
            $('#classif').html(temps)
            form.render('select');
        }

        function queryClassif() { // 请求分类 后端接口
            let url = '';
            layObj.get(url, function (res) {
                console.log(res)
            }); // 封装的ajax
            _classif()
        }


        queryClassif(); // 获取后端分类数据


        //监听提交
        form.on('submit(demo1)', function (data) {
            console.log(data.field, '最终的提交信息')
            data = data.field;
            let url = '';
            // layObj.post(url, data, function (res) {
            //
            // });
            $.ajax({
                type: "POST",
                data: data,
                url: '/admin/category/saveUpdate',
                success(res) {
                    if (res.status == 1) {
                        layer.msg("修改成功", function () {
                            window.location = "<?php echo url('index'); ?>";
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
