<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:81:"D:\project\v3\view\admin\app\public/../application//user/view/user/page_list.html";i:1556332579;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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

<style type="text/css">
    .layui-table-cell{height: 50px;line-height: 50px;}
</style>
<div class="layui-fluid">
    <div class="layui-card">
        <form class="layui-form searchForm">
            <div class="layui-card-header" style="padding: 10px 20px">
                <button class="layui-btn" lay-submit>搜索</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
            <div class="layui-card-body">
                <div class="layui-form-item layui-inline">
                    <label class="layui-form-label">会员ID</label>
                    <div class="layui-input-inline">
                        <input type="text" type="number" name="user_id" placeholder="" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item layui-inline">
                    <label class="layui-form-label">会员名称</label>
                    <div class="layui-input-inline">
                        <input type="text" name="user_name" placeholder="" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item layui-inline">
                    <label class="layui-form-label">会员手机号</label>
                    <div class="layui-input-inline">
                        <input type="text" type="number" name="phone" placeholder="" autocomplete="off" class="layui-input">
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="layui-card">
        <div class="layui-card-body">
            <table id="table" lay-filter="table"></table>
        </div>
    </div>
</div>
<!-- 头部操作模板 -->
<script type="text/html" id="toolbarTpl">
    <div>
        <button class="layui-btn layui-btn-sm" lay-event="add">添加会员</button>
    </div>
</script>
<!-- 表内操作模板 -->
<script type="text/html" id="ctrlbarTpl">
    <button class="layui-btn layui-btn-xs" style="margin:0;" lay-event="edit" >编辑</button>
    <button class="layui-btn layui-btn-danger layui-btn-xs" style="margin:0" lay-event="resetPwd">重置密码</button>
    <button class="layui-btn layui-btn-danger layui-btn-xs" style="margin:0;"  lay-event="disable">禁用</button>
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
            url: '/user/user/getIndex',
            page: true,
            toolbar: '#toolbarTpl',
            headers: {
                ctrl: CENTER_DATA
            },
            cols: [
                [
                    { field: 'id', title: 'ID' },
                    { field: 'avatar', title: '头像', templet: d => {
                        return '<img style="border-radius:50%;width:50px;" src="' + (d.avatar ? d.avatar : 'http://www.25boy.cn/images/avatar.jpg') + '">'
                    }, width: 80},
                    { field: 'user_name', title: '会员名' },
                    { field: 'email', title: '邮箱' },
                    { field: 'phone', title: '手机号' },
                    { field: 'remark', title: '备注' },
                    { field: 'status', title: '账户状态', templet: function(d) { return d.status == 1 ? '正常' : '<span style="color: #c00;">异常</span>' }},
                    //{ field: 'id', title: '操作', templet: '#ctrlbarTpl' },
                    { fixed: 'right', width: 180, align: 'center', toolbar: '#ctrlbarTpl', style: 'vertical-align:center' }

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
                        title: '添加会员',
                        type: 2,
                        area: ['60%', '80%'],
                        scrollbar: false,
                        shadeClose: true,
                        content: '/user/user/page_add'
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
                        title: '编辑会员',
                        type: 2,
                        area: ['60%', '80%'],
                        scrollbar: false,
                        shadeClose: true,
                        content: '/user/user/page_edit?id=' + data.id
                    })
                    break;
                case 'resetPwd':
                    layer.confirm("是否确认重置密码！重置后会将密码发送到用户手机短信或邮箱", { btn: ["确定", "取消"] }, function() {
                        request.setHost(CENTER_DATA).get('/user/user/resetPwd', { user_id: data.id }, function(res) {
                            if (res.code == 0) {
                                // 成功提示
                                layer.msg('成功');
                                setTimeout(function() {
                                    callback();
                                }, 1000);
                            } else {
                                // 错误提示
                                layer.msg(res.msg);
                            }
                        });
                    });
                    return false;
                    break;
                case 'disable':
                    layer.confirm("是否确认禁用该会员！", { btn: ["确定", "取消"] }, function() {
                        request.setHost(CENTER_DATA).get('/user/user/disable', { id: data.id }, function(res) {
                            if (res.code == 0) {
                                // 成功提示
                                layer.msg('禁用成功');
                                setTimeout(function() {
                                    callback();
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

        form.on('submit', function(data){
            table.reload('table', {
                where: data.field,
                page: {
                    curr: 1, //重新从第 1 页开始
                }
            });
            return false;
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