<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:88:"D:\project\v3\view\admin\app\public/../application//power/view/power_role/page_list.html";i:1555997870;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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

<style>

</style>
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <table id="roles" lay-filter="roles"></table>
        </div>
    </div>
</div>


<!-- 操作模板 -->
<script type="text/html" id="toolbar">
    <div>
        <button class="layui-btn layui-btn-sm" lay-event="add">添加角色</button>
    </div>
</script>

<script type="text/html" id="ctrlTpl" >
    <button class="layui-btn layui-btn-xs" style="margin:0;" lay-event="edit" >编辑</button>
    <button class="layui-btn layui-btn-danger layui-btn-xs" style="margin:0;"  lay-event="del">删除</button>
</script>

<script type="text/javascript">
    var table = layui.table;
    //执行渲染
    table.render({
        id:'roles',
        elem: '#roles' ,
        height: 'auto', //容器高度
        url: '/power/power_role/getList',
        page: true,
        toolbar: '#toolbar',
        headers: {
            ctrl: CENTER_DATA
        }
        ,cols: [[
            {field: 'id', title: 'ID', width:'5%'},
            {field: 'title', title: '角色名称', width:'20%'},
            {field: 'status', title: '状态', width:'10%',templet: function(d){return d.status==1?'启用':'<span style="color: #c00;">禁用</span>'}},
            {field: 'note', title: '备注', width:'55%'},
            {field: 'id', title: '操作', width:'10%',templet: '#ctrlTpl'}
        ]],
        done: function () {
            layer.closeAll('loading');
        }
    });


    //监听工具栏事件
    table.on('toolbar(roles)', function (obj) {
        const data = obj.data
        const layEvent = obj.event
        if (layEvent === 'add') {
          layer.open({
            title: '添加角色',
            type: 2,
            area: ['90%', '95%'],
            scrollbar:false,
            shadeClose:true,
            content: '/power/power_role/page_add'
          })
        }
    });

    //监听行工具事件
    table.on('tool(roles)', function (obj) {
        const data = obj.data
        const layEvent = obj.event
        const tr = obj.tr
        if (layEvent === 'edit') {
            layer.open({
                title: '编辑角色',
                type: 2,
                area: ['90%', '95%'],
                scrollbar:false,
                shadeClose:true,
                content: '/power/power_role/page_edit?id='+data.id
            })
        }
        else if (layEvent == 'del') {
            layer.confirm("是否确认删除！删除后 无法恢复！", {btn: ["确定","取消"] },
             function(){
                request.setHost(CENTER_DATA).get('/power/power_role/delete',{ids: data.id}, function(res){
                    if (res.code == 0) {
                        // 成功提示
                        layer.msg('删除成功');
                        setTimeout(function(){  
                            location.reload();
                        },1000);
                    } else {
                        // 错误提示
                        layer.msg(res.msg);
                    }
                });
            });
            return false;
        }
    });



    function callback(){
        table.reload('roles');
        layer.closeAll();
    }
</script>







</body>
</html>