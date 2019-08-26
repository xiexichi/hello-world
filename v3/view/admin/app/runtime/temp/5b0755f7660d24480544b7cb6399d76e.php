<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:77:"D:\project\v3\view\admin\app\public/../application//goods/view/prop/list.html";i:1553242634;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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
            <table id="prop" lay-filter="test"></table>
        </div>
    </div>
</div>
<script>
    var table = layui.table;
    //执行渲染
    table.render({
        id:'prop',
        elem: '#prop' //
        , height: 'auto' //容器高度
        , url: '/goods/goods_prop/getPropAll'
        , page: true
        , toolbar: '<div>' +
            '<button class="layui-btn layui-btn-sm" onclick="add()">添加属性类型' +
            '</button>' +
            '</div>'
        , headers: {
            ctrl: SHOP_DATA
        }
        , cols: [[
            {field: 'id', title: 'ID', width: 80}
            , {field: 'prop_name', title: '属性类型'}
            , {field: 'show_type_desc', title: '显示类型', width: 200}
            , {field: 'sort', title: '排序', width: 100}
            , {field: 'id', title: '操作', width: 200, templet: '#ctrlTpl'}
        ]]
    });

    function add(){
        var toUrl = "/goods/prop/add.html";
        layer.open({
            title:'添加属性类型',
            type:2,
            shadeClose: true,
            closeBtn:1,
            area:['50%','50%'],
            content:toUrl
        })
    }

    function detail(id){
        var toUrl = "/goods/prop/detail.html?id="+id;
        layer.open({
            title:'属性类型详情',
            type:2,
            shadeClose: true,
            closeBtn:1,
            area:['50%','50%'],
            content:toUrl
        })
    }

    function set_val(id,prop_name){
        var toUrl = "/goods/prop/set_val.html?id="+id;
        layer.open({
            title:prop_name+'&nbsp&nbsp属性值设置',
            type:2,
            shadeClose: true,
            closeBtn:1,
            area:['90%','90%'],
            content:toUrl
        })
    }
    function deleted(id){
        layer.confirm("是否确认删除！", {
            btn: ["确定","取消"] //按钮
        }, function(){
            var param = {};
            param.id = id;
            request.setHost(SHOP_DATA).post('/goods/goods_prop/deleted',param, function(res){
                if (res.code == 0) {
                    // 成功提示
                    layer.msg(res.msg);
                    setTimeout(function(){
                        table.reload('prop');
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
        table.reload('prop');
        layer.closeAll();
    }

</script>

<!-- 操作模板 -->
<script type="text/html" id="ctrlTpl">
    <button class="layui-btn layui-btn-xs" style="margin:0;" onclick="set_val({{d.id}},'{{d.prop_name}}')" >设置属性值</button>
    <button class="layui-btn layui-btn-xs" style="margin:0;" onclick="detail({{d.id}})" >详情</button>
    <button class="layui-btn layui-btn-danger layui-btn-xs" style="margin:0;" onclick="deleted({{d.id}})" >删除</button>
</script>

</body>
</html>