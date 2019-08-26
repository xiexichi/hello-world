<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:87:"D:\project\v3\view\admin\app\public/../application//user/view/user_level/page_list.html";i:1556332579;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>25BOY 新零售系统v3</title>
<link rel="stylesheet" href="/static/layui/css/layui.css" media="all">
<link rel="stylesheet" href="/static/style/common.css" media="all">
<link rel="stylesheet" href="/static/style/admin.css" media="all">
<script src="/static/js/jquery-3.1.1.min.js"></script>

<!-- 百度echarts -->
<script src="/static/js/echarts.min.js"></script>

<!-- 自定义js -->
<script src="/static/js/common.js"></script>
<script src="/static/js/request.js"></script>

<!-- layui组件js -->
<!-- <script src="/static/layui/layui.js"></script> -->
<script src="/static/layui/layui.all.js"></script>

<script src="/static/js/layui-common.js"></script>
<!-- 全局参数 -->
<script type="text/javascript">
const photo_space_token = "<?php echo \think\Session::get('photojwttoken'); ?>"
const photo_handle_url = "<?php echo url('/handlePhoto.html','','',true);?>"
</script>
</head>

<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <table id="table" lay-filter="table"></table>
        </div>
    </div>
</div>
<!-- 头部操作模板 -->
<script type="text/html" id="toolbarTpl">
    <div>
        <button class="layui-btn layui-btn-sm" lay-event="add">添加会员等级</button>
    </div>
</script>
<!-- 表内操作模板 -->
<script type="text/html" id="ctrlbarTpl">
    <button class="layui-btn layui-btn-xs" style="margin:0;" lay-event="edit" >编辑</button>
    <button class="layui-btn layui-btn-danger layui-btn-xs" style="margin:0;"  lay-event="del">删除</button>
</script>
<script type="text/javascript">
var table = layui.table;
var form = layui.form;
var layer = layui.layer;

$(document).ready(function() {
    layui.use(['form', 'table', 'layer'], function() {
        layer.load(2);
        //执行渲染
        table.render({
            id: 'table',
            elem: '#table',
            height: 'auto', //容器高度
            url: '/user/user_level/index',
            page: true,
            toolbar: '#toolbarTpl',
            headers: {
                ctrl: CENTER_DATA
            },
            cols: [
                [
                    { field: 'id', title: 'ID' },
                    { field: 'name', title: '等级名称' },
                    { field: 'discount', title: '等级折扣' },
                    { field: 'level_limit', title: '自动升级界限' },
                    { field: 'auto_update', title: '自动升级', templet: function(d) { return d.auto_update == 1 ? '是' : '<span style="color: #c00;">否</span>' } },
                    { field: 'is_free',title: '包邮',templet: function(d) { return d.is_free == 1 ? '是' : '<span style="color: #c00;">否</span>' }},
                    { field: 'is_use_coupon',title: '使用优惠券',templet: function(d) { return d.is_use_coupon == 1 ? '是' : '<span style="color: #c00;">否</span>' }},
                    { field: 'id', title: '操作', templet: '#ctrlbarTpl' }
                ]
            ],
            done: function() {
                layer.closeAll();
            }
        });


        //监听工具栏事件
        table.on('toolbar(table)', function(obj) {
            const data = obj.data
            const layEvent = obj.event
            switch (layEvent) {
                case 'add':
                    layer.open({
                        title: '添加会员等级',
                        type: 2,
                        area: ['60%', '80%'],
                        scrollbar: false,
                        shadeClose: true,
                        content: '/user/user_level/page_add'
                    })
                    break;
                default:
                    // statements_def
                    break;
            }
        });

        //监听行工具事件
        table.on('tool(table)', function(obj) {
            const data = obj.data
            const layEvent = obj.event
            const tr = obj.tr

            switch (layEvent) {
                case 'edit':
                    layer.open({
                        title: '编辑会员等级',
                        type: 2,
                        area: ['60%', '80%'],
                        scrollbar: false,
                        shadeClose: true,
                        content: '/user/user_level/page_edit?id=' + data.id
                    })
                    break;
                case 'del':
                    layer.confirm("是否确认删除！删除后 无法恢复！", { btn: ["确定", "取消"] }, function() {
                        request.setHost(CENTER_DATA).get('/user/user_level/delete', { ids: data.id }, function(res) {
                            if (res.code == 0) {
                                // 成功提示
                                layer.msg('删除成功');
                                setTimeout(function() {
                                    location.reload();
                                }, 1000);
                            } else {
                                // 错误提示
                                layer.msg(res.msg);
                            }
                        });
                    });
                    return false;
                    break;
                default:
                    // statements_def
                    break;
            }
        });
    });
    form.render();
});

function callback() {
    table.reload('table');
    layer.closeAll();
}
</script>
</body>
</html>