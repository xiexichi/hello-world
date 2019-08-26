<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:83:"D:\project\v3\view\admin\app\public/../application//goods/view/categorys/index.html";i:1548753029;}*/ ?>
<div class="layui-card">
    <div class="layui-card-body">
        <div>
            <button class="layui-btn layui-btn-sm layui-btn-primary" id="btn-refresh" style="display:none;">刷新表格</button>
            <table id="table1" class="layui-table" lay-filter="table1"></table>
        </div>
    </div>
</div>
<script>
    layui.config({
        base: '/static/layui/module/'
    }).extend({
        treetable: 'treetable-lay/treetable'
    }).use(['layer', 'table', 'treetable'], function () {
        var $ = layui.jquery;
        var table = layui.table;
        var layer = layui.layer;
        var treetable = layui.treetable;
        // 渲染表格
        function loadlist(){
            layer.load(2);
            treetable.render({
                treeColIndex: 1,
                treeSpid: 0,
                treeParentKey: 'id',
                treeChildKey: 'pid',
                elem: '#table1',
                urlType: 'get',
                url: '/goods/category/getCateAll/?showType=list',
                urlHeaders: {
                    ctrl: SHOP_DATA
                },
                toolbar: '<div>' +
                    '<button class="layui-btn layui-btn-sm" onclick="add()">添加分类</button>' +
                    '<button class="layui-btn layui-btn-sm" id="btn-expand" >全部展开</button>' +
                    '<button class="layui-btn layui-btn-sm" id="btn-fold">全部折叠</button>' +
                    '</div>'
                ,
                cols: [[
                    {field: 'id', title: 'id', width:80},
                    {field: 'cate_name', title: '分类名'},
                    {field: 'sort', title: '排序', width:80},
                    {field: 'id', title: '操作', width:200, templet: '#ctrlTpl'}
                ]],
                done: function () {
                    layer.closeAll('loading');
                }
            });

            $('#btn-expand').click(function () {
                treetable.expandAll('#table1');
            });

            $('#btn-fold').click(function () {
                treetable.foldAll('#table1');
            });
        }
        loadlist();
        $('#btn-refresh').click(function () {
            loadlist();
        });
    });

    function add(){
        var toUrl = "/goods/categorys/add.html";
        layer.open({
            title:'添加分类',
            type:2,
            shadeClose: true,
            closeBtn:1,
            area:['60%','60%'],
            content:toUrl
        })
    }

    function detail(id){
        var toUrl = "/goods/categorys/detail.html?id="+id;
        layer.open({
            title:'编辑分类',
            type:2,
            shadeClose: true,
            closeBtn:1,
            area:['50%','50%'],
            content:toUrl
        })
    }

    function deleted(id){
        layer.confirm("是否确认删除！删除后 商品关联分类将失效！", {
            btn: ["确定","取消"] //按钮
        }, function(){
            var param = {};
            param.id = id;
            param.is_deleted = 1;
            request.setHost(SHOP_DATA).post('/goods/category/edit',param, function(res){
                if (res.code == 0) {
                    // 成功提示
                    layer.msg('删除成功');
                    setTimeout(function(){
                        $('#btn-refresh').click();
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
        $('#btn-refresh').click();
        layer.closeAll();
    }
</script>
<!-- 操作模板 -->
<script type="text/html" id="ctrlTpl">
    <button class="layui-btn layui-btn-xs" style="margin:0;" onclick="detail({{d.id}})" >详情</button>
    <button class="layui-btn layui-btn-danger layui-btn-xs" style="margin:0;" onclick="deleted({{d.id}})" >{{ d.is_deleted ? '恢复' : '删除' }}</button>
</script>
