<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:80:"D:\project\v3\view\admin\app\public/../application//goods/view/prop/set_val.html";i:1553242642;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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
            <table id="prop_val" lay-filter="test"></table>
        </div>
    </div>
</div>
<script>
    var id = getUrlParam('id');
    var info = null;
    $(document).ready(function(){
        loadinfo();
    });
    //获取属性信息
    function loadinfo(){
        var param = {};
        param.id = id;
        request.setHost(SHOP_DATA).post('/goods/goods_prop/one',param, function(res){
            if (res.code == 0) {
                $('.title').html(res.data.prop_name);
                info = res.data;
                loadlist();
            } else {
                // 错误提示
                layer.msg(res.msg);
            }
        });
    }

    function loadlist() {
        var table = layui.table;
        //执行渲染
        table.render({
            elem: '#prop_val' //
            , height: 'auto' //容器高度
            , url: '/goods/goods_prop_val/getPropValAll?prop_id='+id
            , page: true
            , toolbar: '<div>' +
                '<button class="layui-btn layui-btn-sm" onclick="add()">添加属性值' +
                '</button>' +
                '</div>'
            , headers: {
                ctrl: SHOP_DATA
            }
            , cols: [[
                {field: 'pv_name', title: '属性值名'}
                , {field: 'pv_type_val', title: info.show_type_desc+'内容', width: 200}
                , {field: 'pv_erp_code', title: 'erp编码', width: 100}
                , {field: 'id', title: '操作', width: 200, templet: '#ctrlTpl'}
            ]]
        });
    }

    function add(){
        var toUrl = "/goods/prop/val_add.html?id="+info.id+"&show_type="+info.show_type;
        layer.open({
            title:'添加属性类型',
            type:2,
            shadeClose: true,
            closeBtn:1,
            area:['60%','60%'],
            content:toUrl
        })
    }

    function detail(id){
        var toUrl = "/goods/prop/val_detail.html?id="+id+"&show_type="+info.show_type;
        layer.open({
            title:'编辑属性值',
            type:2,
            shadeClose: true,
            closeBtn:1,
            area:['60%','60%'],
            content:toUrl
        })
    }

    function deleted(id){
        layer.confirm("是否确认删除！", {
            btn: ["确定","取消"] //按钮
        }, function(){
            var param = {};
            param.id = id;
            request.setHost(SHOP_DATA).post('/goods/goods_prop_val/deleted',param, function(res){
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

</body>
</html>