<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:76:"D:\project\v3\view\admin\app\public/../application//goods/view/tag/list.html";i:1548913180;}*/ ?>
<div class="layui-card">
    <div class="layui-card-body">
        <table id="tag" lay-filter="test"></table>
    </div>
</div>
<script>
    $(document).ready(function(){
        loadlist();
    });
    function loadlist(){
        var table = layui.table;
        //执行渲染
        table.render({
            elem: '#tag' //
            ,height: 'auto' //容器高度
            ,url: '/goods/goods_tag/getTagAll'
            ,page: true
            , toolbar: '<div>' +
                '<button class="layui-btn layui-btn-sm" onclick="add()">添加标签'+
                '</button>' +
                '</div>'
            ,headers: {
                ctrl: SHOP_DATA
            }
            ,cols: [[
                {field: 'id', title: 'ID', width:80}
                ,{field: 'tag_name', title: '标签名'}
                ,{field: 'sort', title: '排序', width:100}
                ,{field: 'id', title: '操作', width:200, templet: '#ctrlTpl'}
            ]]
        });
    }
    function add(){
        var toUrl = "/goods/tag/add.html";
        layer.open({
            title:'添加商品标签',
            type:2,
            shadeClose: true,
            closeBtn:1,
            area:['50%','50%'],
            content:toUrl
        })
    }
    function detail(id){
        var toUrl = "/goods/tag/detail.html?id="+id;
        layer.open({
            title:'商品标签详情',
            type:2,
            shadeClose: true,
            closeBtn:1,
            area:['50%','50%'],
            content:toUrl
        })
    }

    function deleted(id){
        layer.confirm("是否确认删除！删除后相关商品关联将取消！", {
            btn: ["确定","取消"] //按钮
        }, function(){
            var param = {};
            param.id = id;
            request.setHost(SHOP_DATA).post('/goods/goods_tag/deleted',param, function(res){
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
    <button class="layui-btn layui-btn-xs" style="margin:0;" onclick="detail({{d.id}})" >详情</button>
    <button class="layui-btn layui-btn-danger layui-btn-xs" style="margin:0;" onclick="deleted({{d.id}})" >删除</button>
</script>
