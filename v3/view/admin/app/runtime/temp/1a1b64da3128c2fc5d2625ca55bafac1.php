<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:83:"D:\project\v3\view\admin\app\public/../application//goods/view/evaluation/list.html";i:1552985658;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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
            <form class="layui-form" action="" id="form" >
                <input type="hidden" name="state" value="<?=isset($_GET['state']) ? $_GET['state'] : '';?>">
                <input type="hidden" name="delete" value="<?=isset($_GET['delete']) ? $_GET['delete'] : '';?>">
                <div class="layui-form-item">
                    <label class="layui-form-label">订单号</label>
                    <div class="layui-input-inline">
                        <input type="text" name="order_sn" value="" placeholder="订单号" autocomplete="off" class="layui-input">
                    </div>
                    <label class="layui-form-label">会员id</label>
                    <div class="layui-input-inline">
                        <input type="text" name="user_id" value="" placeholder="会员id" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">是否有图</label>
                    <div class="layui-input-inline">
                        <select name="has_img" >
                            <option value="0" >全部</option>
                            <option value="1" >有图</option>
                            <option value="2" >无图</option>
                        </select>
                    </div>
                    <label class="layui-form-label">审核状态</label>
                    <div class="layui-input-inline">
                        <select name="verify" >
                            <option value="" >全部</option>
                            <option value="0" >待审核</option>
                            <option value="1" >通过</option>
                            <option value="2" >不通过</option>
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn" lay-submit lay-filter="formDemo">确认</button>
                        <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!--数据列表-->
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <table id="evaluation" lay-filter="test"></table>
        </div>
    </div>
</div>
<!-- 操作模板 -->
<script type="text/html" id="ctrlTpl">
    <button class="layui-btn layui-btn-xs" style="margin:0;" lay-event="detail" >详情</button>
</script>
<script>
    var order_id = getUrlParam('order_id');
    var param = '/?';
    if( order_id != null ){
        param += '&order_id='+order_id;
    }

    var table = layui.table;
    //执行渲染
    table.render({
        id:'eval'
        ,elem: '#evaluation' //
        ,height: 'auto' //容器高度
        ,url: '/goods/evaluation/index'+param
        ,page: true
        ,headers: {
            ctrl: SHOP_DATA
        }
        ,cols: [[
            {field: 'goods_id', title: '商品id', width:80}
            ,{field: 'goods_name', title: '商品'}
            ,{field: 'has_img', title: '图片', width:100}
            ,{field: 'verify_desc', title: '审核状态', width:100}
            ,{field: 'create_time', title: '评论时间', width:180}
            ,{field: 'id', title: '操作', width:200, templet: '#ctrlTpl'}
        ]]
    });

    var form = layui.form;
    layui.use('form', function(){
        form.on('submit', function(data){
            searchData = data.field;
            //上述方法等价于
            table.reload('eval', {
                where: searchData,
                page: {
                    curr: 1 //重新从第 1 页开始
                }
            });
            return false;
        });
        form.render();
    });

    table.on('tool(test)', function(obj) {
        var data = obj.data;
        var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
        var tr = obj.tr; //获得当前行 tr 的DOM对象
        switch (layEvent) {
            case 'detail' ://查看订单详情
                layer.open({
                    title:'评论详情',
                    type:2,
                    shadeClose: true,
                    closeBtn:1,
                    area:['90%','90%'],
                    content:"/goods/evaluation/detail.html?id="+data.id
                });
                break;
            default :
                break;
        }
    });

    function callback(){
        table.reload('eval');
        layer.closeAll();
    }
</script>

</body>
</html>