<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:83:"D:\project\v3\view\admin\app\public/../application//goods/view/attribute/group.html";i:1552552350;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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

<!--导航-->
<ul class="layui-nav" lay-filter="">
    <li class="layui-nav-item"><a href="/goods/attribute/list.html">参数列表</a></li>
    <li class="layui-nav-item layui-this"><a href="/goods/attribute/group.html">参数组</a></li>
</ul>
<!--列表内容-->
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <table id="group" lay-filter="test"></table>
        </div>
    </div>
</div>
<!-- 操作模板 -->
<script type="text/html" id="toolbarTpl">
    <button class="layui-btn layui-btn-sm" lay-event="add">添加参数组</button>
</script>
<script type="text/html" id="ctrlTpl">
    <button class="layui-btn layui-btn-xs" style="margin:0;" lay-event="setInfo" >管理</button>
    <button class="layui-btn layui-btn-danger layui-btn-xs" style="margin:0;" lay-event="deleted" >删除</button>
</script>
<script>
    //列表
    var table = layui.table;
    //执行渲染
    table.render({
        id:'group',
        elem: '#group' //指定原始表格元素选择器（推荐id选择器）
        , height: 'auto' //容器高度
        , url: '/goods/attribute_group/getAttrGroupList'
        , page: true
        , toolbar: '#toolbarTpl'
        , headers: {
            ctrl: SHOP_DATA
        }
        , cols: [[
            {field: 'id', title: 'ID', width: 80}
            , {field: 'group_name', title: '参数组名'}
            , {field: 'update_time', title: '更新时间', width: 200}
            , {field: 'id', title: '操作', width: 200, templet: '#ctrlTpl'}
        ]] //设置表头
        //,…… //更多参数参考右侧目录：基本参数选项
    });
    //头部按钮事件
    table.on('toolbar(test)', function(obj) {
        var data = obj.data;
        var layEvent = obj.event;
        switch( layEvent ){
            case 'add' :
                layer.open({
                    title:'添加参数组',
                    type:2,
                    shadeClose: true,
                    closeBtn:1,
                    area:['30%','30%'],
                    content: '/goods/attribute/group_add.html'
                });
                break;
            default :
                return false;
                break;
        }
        return false;
    });
    //列表项事件
    table.on('tool(test)', function(obj) {
        var data = obj.data;
        var layEvent = obj.event;
        switch(layEvent) {
            case 'setInfo' ://详情
                layer.open({
                    title:'设置参数组信息',
                    type:2,
                    shadeClose: true,
                    closeBtn:1,
                    area:['60%','50%'],
                    content:"/goods/attribute/group_detail.html?id="+data.id
                });
                break;
            case 'deleted' ://删除
                layer.confirm("是否确认删除", {
                    btn: ["确定","取消"] //按钮
                }, function(){
                    request.setHost(SHOP_DATA).post('/goods/attribute_group/deleted',{id:data.id}, function(res){
                        if (res.code == 0) {
                            // 成功提示
                            layer.msg(res.msg);
                            setTimeout(function(){
                                table.reload('group');
                            },1000);
                        } else {
                            // 错误提示
                            layer.msg(res.msg);
                        }
                    });
                });
                break;
            default :
                return false;
                break;
        }
        return false;
    });

    function callback(){
        table.reload('group');
        layer.closeAll();
    }
</script>

</body>
</html>