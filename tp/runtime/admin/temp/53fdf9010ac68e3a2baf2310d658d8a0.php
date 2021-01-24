<?php /*a:1:{s:77:"E:\Code\Git-tp6\ThinkPHP6-Shopping_Project\tp\app\admin\view\goods\index.html";i:1611108078;}*/ ?>
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
</head>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
        <fieldset class="layui-elem-field layuimini-search">
            <legend>搜索信息</legend>
            <div style="margin: 10px 10px 10px 10px">
                <form class="layui-form layui-form-pane" action="<?php echo url('index'); ?>" method="GET">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">商品名称</label>
                            <div class="layui-input-inline">
                                <input value="<?php echo htmlentities($search['title']); ?>" type="text" name="title" autocomplete="off" class="layui-input">
                            </div>
                        </div>

                        <div class="layui-inline">
                            <label class="layui-form-label">发布时间</label>
                            <div class="layui-input-inline" style="width: 280px;">
                                <div class="layui-input-inline" style="width: 280px;">
                                    <input value="<?php echo htmlentities($search['create_time']); ?>" type="text" name="time" class="layui-input" id="test10"
                                           placeholder=" - ">
                                </div>
                            </div>
                        </div>
                        <div class="layui-inline">
                            <button class="layui-btn" lay-submit="" lay-filter="data-search-btn">搜索</button>
                            <a href="<?php echo url('index'); ?>" class="layui-btn layui-btn-danger">清空</a>
                        </div>
                    </div>
                </form>
            </div>
        </fieldset>
    </div>
    <div class="layui-form" style="margin-top: 20px;">
        <table class="layui-table">
            <colgroup>
                <col width="60">
                <col width="200">
                <col width="130">
                <col width="70">
                <col width="200">
                <col width="100">
                <col width="100">
                <col width="200">
                <col width="100">
            </colgroup>
            <thead>
            <tr>
                <th style="text-align:center;">id</th>
                <th style="text-align:center;">商品名称</th>
                <th class="text-center" style="text-align:center;">商品图片</th>
                <th class="text-center" style="text-align:center;">库存</th>
                <th class="text-center" style="text-align:center;">发布时间</th>
                <th class="text-center" style="text-align:center;">状 态</th>
                <th class="text-center" style="text-align:center;">是否推荐</th>
                <th class="text-center" style="text-align:center;">关键词</th>
                <th style="text-align:center;">操作管理</th>
            </tr>
            </thead>
            <tbody>
            <?php if(is_array($goods['data']) || $goods['data'] instanceof \think\Collection || $goods['data'] instanceof \think\Paginator): $i = 0; $__LIST__ = $goods['data'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
            <tr>
                <td align="center"><?php echo htmlentities($vo['id']); ?></td>
                <td align="center"><?php echo htmlentities($vo['title']); ?></td>
                <td class="show-img" align="center">
                    <img src="<?php echo htmlentities($vo['recommend_image']); ?>" data-src="<?php echo htmlentities($vo['recommend_image']); ?>"
                         style="width: 24px;height: 24px;"/>
                </td>
                <td align="center"><?php echo htmlentities($vo['stock']); ?></td>
                <td align="center"><?php echo htmlentities($vo['create_time']); ?></td>
                <td align="center" data-id="<?php echo htmlentities($vo['id']); ?>}"><input type="checkbox" <?php if($vo['status']== 1): ?>checked <?php endif; ?>
                    name="status"
                    lay-skin="switch"
                    lay-filter="switchStatus"
                    lay-text="ON|OFF">
                </td>
                <td align="center" data-id="<?php echo htmlentities($vo['id']); ?>}"><input type="checkbox" <?php if($vo['is_index_recommend']== 1): ?>checked
                                                              <?php endif; ?>
                    name="is_index_recommend"
                    lay-skin="switch"
                    lay-filter="switchIndex"
                    lay-text="ON|OFF">
                </td>
                <td align="center">
                    <?php echo htmlentities($vo['keywords']); ?>
                </td>
                <td align="center">
<!--                    <a type="button" class="layui-btn layui-btn-xs  edit" lay-event="edit">编辑</a>-->
                    <a type="button" class="layui-btn layui-btn-xs  edit" lay-event="edit" href="<?php echo url('update'); ?>?id=<?php echo htmlentities($vo['id']); ?>' ">编辑</a>
                    <a type="checkbox" lay-filter="delete" class="layui-btn layui-btn-xs layui-btn-danger data-count-delete delete" data-ptype="1"
                       lay-event="delete" data-id="<?php echo htmlentities($vo['id']); ?>">删除</a>
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
<script src="/static/admin/lib/jquery-3.4.1/jquery-3.4.1.min.js" charset="utf-8"></script>
<script src="/static/admin/lib/layui-v2.5.4/layui.js" charset="utf-8"></script>
<script src="/static/admin/js/common.js?v5" charset="utf-8"></script>
<script>
    layui.use(['form', 'laypage'], function () {
        var form = layui.form
            , laypage = layui.laypage;

        laypage.render({ //分页
            elem: 'pages'
            , count: <?php echo htmlentities($goods['total']); ?>
            , theme: '#FFB800'
            , limit: <?php echo htmlentities($goods['per_page']); ?>
            , curr: <?php echo htmlentities($goods['current_page']); ?>
            , jump: function (obj, first) {
                if (!first) {
                    location.href = "?page=" + obj.curr  + "&title=<?php echo htmlentities($search['title']); ?>" + "&time=<?php echo htmlentities($search['create_time']); ?>";
                }
            }
        });

        // 编辑 规格
        // $('.update').on('click', function () {
        //     let id = $(this).attr('data-id'); // fu
        //     let url = '<?php echo url("update"); ?>?id=' + id
        //     layObj.dialog(url)
        // })


        //监听状态 更改
        form.on('switch(switchStatus)', function (obj) {
            let id = obj.othis.parent().attr('data-id');
            let status = obj.elem.checked ? 1 : 0;
            $.ajax({
                url: '<?php echo url("goods/status"); ?>?id=' + id + '&status=' + status,
                success: (res) => {
                    if (res.status == 1) {
                        // window.location.reload();
                    } else {
                        layer.msg("更改失败");
                    }
                }
            });
            return false;
        });

        //监听状态 更改
        form.on('switch(switchIndex)', function (obj) {
            let id = obj.othis.parent().attr('data-id');
            let is_index_recommend = obj.elem.checked ? 1 : 0;
            $.ajax({
                url: '<?php echo url("goods/status"); ?>?id=' + id + '&is_index_recommend=' + is_index_recommend,
                success: (res) => {
                    if (res.status == 1) {
                        // window.location.reload();
                    } else {
                        layer.msg("更改失败");
                    }
                }
            });
            return false;
        });

        // 删除二级分类
        $('.delete').on('click', function () {
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

        // 显示图片
        layui.use(['form', 'table', 'laydate', 'jquery', 'laypage'], function () {
            var $ = layui.jquery,
                form = layui.form,
                laypage = layui.laypage,
                laydate = layui.laydate;

            //日期时间范围 搜索
            laydate.render({
                elem: '#test10'
                , type: 'datetime'
                , range: true
            });

            $('.show-img').on('click', function () {
                var imgurl = $(this).find('img').attr('data-src');
                //页面层
                layer.open({
                    type: 1,
                    shade: 0.8,
                    offset: 'auto',
                    area: [600 + 'px', 600 + 'px'],
                    scrollbar: false,
                    title: '图片预览',
                    shadeClose: true, //开启遮罩关闭
                    end: function (index, layero) {
                        return false;
                    },
                    content: `<div style="text-align:center"><img src="${imgurl}" /></div>`
                });
            })
        });
    })
</script>
</body>
</html>
