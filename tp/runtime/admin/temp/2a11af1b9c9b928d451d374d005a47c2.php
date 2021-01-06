<?php /*a:1:{s:80:"E:\Code\Git-tp6\ThinkPHP6-Shopping_Project\tp\app\admin\view\category\index.html";i:1609936693;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>layui</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="/static/admin/lib/layui-v2.5.4/css/layui.css" media="all">
    <link rel="stylesheet" href="/static/admin/css/public.css" media="all">
    <style>
        .inoutCls {
            height: 22px;
            line-height: 22px;
            padding: 0 5px;
            font-size: 12px;
            background-color: #1E9FFF;
            max-width: 80px;
            border: none;
            color: #fff;
            margin-left: 10px;
            display: inline-block;
            white-space: nowrap;
            text-align: center;
            border-radius: 2px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
        <a href="/admin/category/add">
            <button type="button" class="layui-btn add">添 加</button>
        </a>
        <a href="<?php echo url('index',['pid' => 0]); ?>">
            <button>分类首页</button>
        </a>
        <?php if(is_array($res['data']) || $res['data'] instanceof \think\Collection || $res['data'] instanceof \think\Paginator): $i = 0; $__LIST__ = $res['data'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
        <a href="<?php echo url('index',['pid' => $vo['id']]); ?>">
            <button><?php echo htmlentities($vo['name']); ?></button>
        </a>
        <?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
        <div class="layui-form" style="margin-top: 20px;">
            <table class="layui-table">
                <colgroup>
                    <col width="60">
                    <col width="60">
                    <col width="60">
                    <col width="130">
                    <col width="130">
                    <col width="70">
                    <col width="70">
                    <col width="185">
                </colgroup>
                <thead>
                <tr>
                    <th style="text-align:center;">编号</th>
                    <th style="text-align:center;">标题</th>
                    <th style="text-align:center;">排序</th>
                    <th style="text-align:center;">创建时间</th>
                    <th style="text-align:center;">更新时间</th>
                    <th style="text-align:center;">操作人</th>
                    <th style="text-align:center;">状 态</th>
                    <th style="text-align:center;">操作管理</th>
                </tr>
                </thead>
                <tbody>
                <?php if(is_array($categorys['data']) || $categorys['data'] instanceof \think\Collection || $categorys['data'] instanceof \think\Paginator): $i = 0; $__LIST__ = $categorys['data'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                <tr>
                    <td align="center">
                        <?php echo htmlentities($vo['id']); ?>
                    </td>
                    <td align="center">
                        <?php echo htmlentities($vo['name']); ?>
                    </td>
                    <td align="center">
                        <div class="layui-input-inline">
                            <input type="text" value="<?php echo htmlentities($vo['listorder']); ?>" data-id="<?php echo htmlentities($vo['id']); ?>"
                                   class="changeSort layui-input">
                        </div>
                    </td>
                    <td align="center">
                        <?php echo htmlentities($vo['create_time']); ?>
                    </td>
                    <td align="center">
                        <?php echo htmlentities($vo['update_time']); ?>
                    </td>
                    <td align="center">
                        <?php echo htmlentities($vo['operate_user']); ?>
                    </td>

                    <td align="center" data-id="<?php echo htmlentities($vo['id']); ?>}"><input type="checkbox" <?php if($vo['status']== 1): ?>checked <?php endif; ?>
                        name="status"
                        lay-skin="switch"
                        lay-filter="switchStatus"
                        lay-text="ON|OFF">
                    </td>
                    <td align="center">
                        <a class="layui-btn layui-btn-xs layui-btn-normal"
                           href="<?php echo url('update',['id' => $vo['id']]); ?>">编辑</a>
                        <a class="layui-btn layui-btn-xs layui-btn-danger data-count-delete delete" data-ptype="1"
                           lay-event="delete" data-id="<?php echo htmlentities($vo['id']); ?>}">删除</a>
                        <a class="layui-btn layui-btn-xs layui-btn-normal" href="<?php echo url('index',['pid' => $vo['id']]); ?>">子栏目
                            (<?php echo htmlentities($vo['childCount']); ?>)</a>
                    </td>
                </tr>
                <?php endforeach; endif; else: echo "" ;endif; ?>
                </tbody>
            </table>
        </div>
        <div id="pages"></div>
    </div>
</div>
<script>
</script>
<script src="/static/admin/lib/jquery-3.4.1/jquery-3.4.1.min.js"></script>
<script src="/static/admin/lib/layui-v2.5.4/layui.js" charset="utf-8"></script>
<script src="/static/admin/js/common.js?v5" charset="utf-8"></script>
<script>
    layui.use(['form', 'laypage'], function () {
        var form = layui.form
            , laypage = layui.laypage;

        laypage.render({ //分页
            elem: 'pages'
            , count: <?php echo htmlentities($categorys['total']); ?>
            , theme: '#FFB800'
            , limit: <?php echo htmlentities($categorys['per_page']); ?>
            , curr: <?php echo htmlentities($categorys['current_page']); ?>
            , jump: function (obj, first) {
                if (!first) {
                    location.href = "?page=" + obj.curr + "&pid=<?php echo htmlentities($pid); ?>";
                }
            }
        });


        // 添加 分类
        // $('.add').on('click', function () {
        //     layObj.dialog("<?php echo url('add'); ?>")
        // });

        //监听状态 更改
        form.on('switch(switchStatus)', function (obj) {
            let id = obj.othis.parent().attr('data-id');
            let status = obj.elem.checked ? 1 : 0;
            $.ajax({
                url: '<?php echo url("category/status"); ?>?id=' + id + '&status=' + status,
                success: (res) => {
                    if (res.status == 1) {
                        window.location.reload();
                    } else {
                        layer.msg("更改失败");
                    }
                }
            });
            return false;
        });


        function editCls(that, id, type) { // 分类修改  type 是 1 顶级  2级  3级
            let name = $(that).val();
            if (!name && (type == 1 || type == 2)) {
                return layObj.msg('分类名称不能为空')
            }
            if (!name && type == 3) { // 演示 应该放到修改回调中  进行处理
                return $(that).parent().remove()
            }
            let url = '<?php echo url("admin/edit"); ?>?id=' + id + '&name=' + name
            layObj.get(url, (res) => {
                if (name && res) {
                    $(that).val(name)
                }
            })
            $.ajax({
                url: '<?php echo url("admin/edit"); ?>?id=' + id + '&name=' + name,
                success(res) {
                    if (name && res) {
                        $(that).val(name)
                    }
                }
            })
        }

        // 删除二级分类
        $('.delete').on('click', function () {
            let ptype = $(this).attr('data-ptype'); // fu
            let id = $(this).attr('data-id'); // fu
            layObj.box(`是否删除当前分类`, () => {
                let url = '<?php echo url("status"); ?>?id=' + id + "&status=99"
                layObj.get(url, (res) => {
                    if (res.status == 1) {
                        window.location.reload();
                    } else {
                        layer.msg("删除失败");
                    }
                })

            })
        })

        $('.changeSort').on('change', function () {
            let id = $(this).attr('data-id');
            let val = $(this).val();

            if (!val) {
                return;
            }
            let url = '<?php echo url("category/listorder"); ?>?id=' + id + '&listorder=' + val;
            layObj.get(url, function (res) {
                if (res.status == 1) {
                    window.location.reload();
                } else {
                    layer.msg("排序失败");
                }
            })
        })
    })
</script>
</body>
</html>
