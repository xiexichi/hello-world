<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:83:"D:\project\v3\view\admin\app\public/../application//power/view/admin/page_list.html";i:1556503250;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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
            <form class="layui-form layui-form-pane" action="" id="form" >
                <div class="layui-form-item layui-card-header" style="padding-bottom: 10px;">
                    <div class="layui-input-inline">
                        <button class="layui-btn" lay-submit lay-filter="formDemo">确认</button><!-- lay-submit  //绑定触发提交的元素  lay-filter //事件过滤器，用于匹配事件的准确性。不仅仅用于form-->
                        <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                    </div>
                </div>
                <div class="layui-form-item layui-card-body">
                    <label class="layui-form-label">真实姓名</label>
                    <div class="layui-input-inline">
                        <input type="text" name="realname" value="" placeholder="填写真实姓名" autocomplete="off" class="layui-input">
                    </div>
                    <label class="layui-form-label">账号状态</label>
                    <div class="layui-input-inline">
                        <select name="status">
                            <option value=""></option>
                            <option value="1" >启用</option>
                            <option value="2">禁用</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <table id="admin" lay-filter="admin"></table>
        </div>
    </div>
</div>


<!-- 操作模板 -->
<script type="text/html" id="toolbar">
    <div>
        <button class="layui-btn layui-btn-sm" lay-event="add">添加管理员</button>
    </div>
</script>

<script type="text/html" id="ctrlTpl" >
    <button class="layui-btn layui-btn-xs" style="margin:0;" lay-event="edit" >编辑</button>
    <button class="layui-btn layui-btn-danger layui-btn-xs" style="margin:0;"  lay-event="del">删除</button>
</script>

<script type="text/javascript">
    var form = layui.form;
    var table = layui.table;
    var layer = layui.layer;
    layui.use(['form','table','layer'], function(){
        form.render(); //刷新全部
        //layer.load(2);
        //执行渲染
        table.render({
            id:'admin',
            elem: '#admin' ,//找到属性id=admin的标签,渲染数据
            height: 'auto', //容器高度
            url: '/power/admin/index',//渲染数据的来源 API
            page: true,
            toolbar: '#toolbar',
            headers: {
                ctrl: CENTER_DATA
            }
            ,cols: [[
                {field: 'id', title: 'ID', width:'5%'},
                {field: 'realname', title: '真实姓名'},
                {field: 'code', title: '员工编码'},
                {field: 'status', title: '状态', width:'5%',templet: function(d){return d.status==1?'启用':'<span style="color: #c00;">禁用</span>'}},
                {field: 'merchant_name', title: '商户主体'},
                {field: 'last_time', title: '最后登录时间'},
                {field: 'ip', title: 'IP'},
                {field: 'id', title: '操作', width:'10%',templet: '#ctrlTpl'}
            ]],
            done: function () {
                layer.closeAll('loading');
            }
        });

            //监听行工具事件
        table.on('tool(admin)', function (obj) {
            const data = obj.data
            const layEvent = obj.event
            const tr = obj.tr

            switch(layEvent){
                case 'edit':
                    layer.open({
                        title: '编辑管理员',
                        type: 2,
                        area: ['90%', '95%'],
                        scrollbar:false,
                        shadeClose:true,
                        content: '/power/admin/page_edit?id='+data.id
                    })
                    break;
                case 'del':
                    layer.confirm("是否确认删除！删除后 无法恢复！", {btn: ["确定","取消"] },
                     function(){
                        request.setHost(CENTER_DATA).get('/power/admin/delete',{ids: data.id}, function(res){
                            if (res.code == 0) {
                                // 成功提示
                                layer.msg('删除成功');
                                setTimeout(function(){
                                    table.reload('admin');
                                },1000);
                            } else {
                                // 错误提示
                                layer.msg(res.msg);
                            }
                        });
                    });
                    return false;
                    break;
            }
        });

        //监听工具栏事件
        table.on('toolbar(admin)', function (obj) {
            const data = obj.data
            const layEvent = obj.event
            if (layEvent === 'add') {
              layer.open({
                title: '添加管理员',
                type: 2,
                area: ['90%', '95%'],
                scrollbar:false,
                shadeClose:true,
                content: '/power/admin/page_add'
              })
            }
        });

        form.on('submit', function(data){
            table.reload('admin', {
                where: data.field,
                page: {
                    curr: 1, //重新从第 1 页开始
                }
            });
            return false;
        });
    });

    function callback(){
        table.reload('admin');
        layer.closeAll();
    }
</script>







</body>
</html>