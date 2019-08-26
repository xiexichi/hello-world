<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:83:"D:\project\v3\view\admin\app\public/../application//goods/view/attribute/group.html";i:1548820800;}*/ ?>
<!--导航-->
<ul class="layui-nav" lay-filter="">
    <li class="layui-nav-item"><a href="/goods/attribute/list.html">参数列表</a></li>
    <li class="layui-nav-item layui-this"><a href="/goods/attribute/group.html">参数组</a></li>
</ul>
<!--列表内容-->
<div class="layui-card">
    <div class="layui-card-body">
        <table id="group" lay-filter="test"></table>
    </div>
</div>
<script>
    $(document).ready(function(){
        loadlist();
    });
    function loadlist() {
        //菜单
        layui.use('element', function () {
            var element = layui.element;
        });
        //列表
        var table = layui.table;
        //执行渲染
        table.render({
            elem: '#group' //指定原始表格元素选择器（推荐id选择器）
            , height: 'auto' //容器高度
            , url: '/goods/attribute_group/getAttrGroupList'
            , page: true
            , toolbar: '<div>' +
                '<button class="layui-btn layui-btn-sm" onclick="add()">添加参数组'+
                '</button>' +
                '</div>'
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
    }

    function add(){
        var toUrl = "/goods/attribute/group_add.html";
        layer.open({
            title:'添加参数组',
            type:2,
            shadeClose: true,
            closeBtn:1,
            area:['30%','30%'],
            content:toUrl
        })
    }

    function setInfo(id){
        var toUrl = "/goods/attribute/group_detail.html?id="+id;
        layer.open({
            title:'添加参数组',
            type:2,
            shadeClose: true,
            closeBtn:1,
            area:['60%','50%'],
            content:toUrl
        })
    }

    function deleted(id){
        layer.confirm("是否确认删除", {
            btn: ["确定","取消"] //按钮
        }, function(){
            var param = {};
            param.id = id;
            request.setHost(SHOP_DATA).post('/goods/attribute_group/deleted',param, function(res){
                if (res.code == 0) {
                    // 成功提示
                    layer.msg(res.msg);
                    setTimeout(function(){
                        loadlist();
                    },1000);
                } else {
                    // 错误提示
                    layer.msg(res.msg);
                }
            });
        });
        return false;
    }

    function callback(){
        loadlist();
        layer.closeAll();
    }

</script>
<!-- 操作模板 -->
<script type="text/html" id="ctrlTpl">
    <button class="layui-btn layui-btn-xs" style="margin:0;" onclick="setInfo({{d.id}})" >管理</button>
    <button class="layui-btn layui-btn-danger layui-btn-xs" style="margin:0;" onclick="deleted({{d.id}})" >删除</button>
</script>
