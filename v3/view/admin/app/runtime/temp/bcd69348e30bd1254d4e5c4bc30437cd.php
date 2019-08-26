<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:82:"D:\project\v3\view\admin\app\public/../application//activity/view/coupon/list.html";i:1556084010;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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
                <div class="layui-form-item">
                    <label class="layui-form-label">券类型</label>
                    <div class="layui-input-inline">
                        <select name="type" >
                            <option value="" >全部</option>
                            <option value="0" >代金券</option>
                            <option value="1" >折扣券</option>
                        </select>
                    </div>
                    <label class="layui-form-label">发放状态</label>
                    <div class="layui-input-inline">
                        <select name="status" >
                            <option value="" >全部</option>
                            <option value="0" >不可发放</option>
                            <option value="1" >发放中</option>
                        </select>
                    </div>
                    <label class="layui-form-label">使用范围</label>
                    <div class="layui-input-inline">
                        <select name="is_goods" >
                            <option value="" >全部</option>
                            <option value="0" >所有商品</option>
                            <option value="1" >部分商品</option>
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">有效时间</label>
                    <div class="layui-input-inline">
                        <input type="text" name="start_time" id="start_time" value="" placeholder="开始时间" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-form-mid">至</div>
                    <div class="layui-input-inline">
                        <input type="text" name="end_time" id="end_time" value="" placeholder="结束时间" autocomplete="off" class="layui-input" >
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
            <table id="coupon" lay-filter="test"></table>
        </div>
    </div>
</div>
<!-- 操作模板 -->
<script type="text/html" id="toolbarTpl">
    <button class="layui-btn layui-btn-sm" lay-event="add" >创建优惠券</button>
    <button class="layui-btn layui-btn-sm" lay-event="user_list" >查看会员已领券列表</button>
</script>
<script type="text/html" id="ctrlTpl">
    <button class="layui-btn layui-btn-xs" style="margin:0;" lay-event="detail" >详情</button>
    <button class="layui-btn layui-btn-xs" style="margin:0;" lay-event="sand_detail" >查看发放</button>
    {{# if( d.is_invalid == 0 && d.is_deleted == 0 ){ }}
    <button class="layui-btn layui-btn-xs" style="margin:0;" lay-event="set_qty" >修改发放量</button>
    <button class="layui-btn layui-btn-xs layui-bg-red" style="margin:0;" lay-event="status_change" >{{ d.status == 1 ? '关闭' : '开启' }}发放</button>
    <button class="layui-btn layui-btn-xs layui-bg-orange" style="margin:0;" lay-event="del" >删除</button>
    {{# } }}
</script>
<script>
    var table = layui.table;
    var form = layui.form;
    //执行渲染
    table.render({
        id:'coupon'
        ,elem: '#coupon' //
        ,height: 'auto' //容器高度
        ,url: '/activity/coupon/index'
        ,page: true
        ,toolbar: '#toolbarTpl'
        ,headers: {
            ctrl: SHOP_DATA
        }
        ,cols: [[
            {field: 'id', title: 'id', width:50}
            ,{field: 'title', title: '标题'}
            ,{field: 'qty', title: '可发量', width:80}
            ,{field: 'sand_sum', title: '已发量', width:80}
            ,{field: 'type_desc', title: '优惠类型', width:90}
            ,{field: 'status_desc', title: '发放状态', width:90}
            ,{field: 'goods_area', title: '使用范围', width:90}
            ,{field: 'start_time', title: '开始使用时间', width:160}
            ,{field: 'end_time', title: '结束使用时间', width:160}
            ,{field: 'id', title: '操作', templet: '#ctrlTpl',width:300}
        ]]
    });
    //头部按钮事件
    table.on('toolbar(test)', function(obj) {
        var data = obj.data;
        var layEvent = obj.event;
        switch( layEvent ){
            case 'add' :
                layer.open({
                    title:'创建优惠券',
                    type:2,
                    shadeClose: true,
                    closeBtn:1,
                    area:['90%','90%'],
                    content:'/activity/coupon/detail.html'
                });
                break;
            case 'user_list' :
                layer.open({
                    title:'用户优惠券列表',
                    type:2,
                    shadeClose: true,
                    closeBtn:1,
                    area:['90%','90%'],
                    content:'/activity/coupon/user_list.html'
                });
                break;
            default :
                return false;
                break;
        }
        return false;
    });
    layui.use('form', function(){
        form.on('submit', function(data){
            searchData = data.field;
            //上述方法等价于
            table.reload('coupon', {
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
            case 'status_change' :
                var param = {};
                param.id = data.id;
                param.status = data.status ? 0 : 1;
                msg = "是否确认关闭！";
                if( param.status == 1 ){
                    msg = "是否确认开启";
                }
                layer.confirm(msg, {
                    btn: ["确定","取消"] //按钮
                }, function(){
                    request.setHost(SHOP_DATA).post('/activity/coupon/edit',param, function(res){
                        if (res.code == 0) {
                            // 成功提示
                            layer.msg(res.msg);
                            setTimeout(function(){
                                table.reload('coupon');
                            },1000);
                        } else {
                            // 错误提示
                            layer.msg(res.msg);
                        }
                    });
                });
                break;
            case 'set_qty' :
                layer.prompt({
                    title: '请输入数量'
                }, function(val, index){
                    if( isNaN(val) || val < 0 || val.split(".").length > 1 ){
                        layer.msg('只能输入非负整数');
                    }
                    layer.confirm('是否确认修改发放量为：'+val, {
                        btn: ["确定","取消"] //按钮
                    }, function(){
                        var param = {};
                        param.id = data.id;
                        param.qty = val;
                        request.setHost(SHOP_DATA).post('/activity/coupon/edit',param, function(res){
                            if (res.code == 0) {
                                // 成功提示
                                layer.msg(res.msg);
                                setTimeout(function(){
                                    table.reload('coupon');
                                },1000);
                            } else {
                                // 错误提示
                                layer.msg(res.msg);
                            }
                        });
                    });
                    layer.close(index);
                });
                break;
            case 'detail' ://详情
                layer.open({
                    title:'优惠券详情',
                    type:2,
                    shadeClose: true,
                    closeBtn:1,
                    area:['90%','90%'],
                    content:"/activity/coupon/detail.html?id="+data.id
                });
                break;
            case 'sand_detail' :
                layer.open({
                    title:'查看发放',
                    type:2,
                    shadeClose: true,
                    closeBtn:1,
                    area:['80%','80%'],
                    content:"/activity/coupon/user_list.html?coupon_id="+data.id
                });
                break;
            case 'del' :
                var param = {};
                param.id = data.id;
                layer.confirm('是否确认删除 / 失效<br />（设为失效已领券变为已失效，删除即直接删除）', {
                    btn: ["设为删除","设为失效"] //按钮
                }, function(){
                    param.is_deleted = 1;
                    request.setHost(SHOP_DATA).post('/activity/coupon/edit',param, function(res){
                        if (res.code == 0) {
                            // 成功提示
                            layer.msg(res.msg);
                            setTimeout(function(){
                                table.reload('coupon');
                            },1000);
                        } else {
                            // 错误提示
                            layer.msg(res.msg);
                        }
                    });
                },function(){
                    param.is_invalid = 1;
                    request.setHost(SHOP_DATA).post('/activity/coupon/edit',param, function(res){
                        if (res.code == 0) {
                            // 成功提示
                            layer.msg(res.msg);
                            setTimeout(function(){
                                table.reload('coupon');
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
        return true;
    });

    function callback(){
        table.reload('coupon');
        layer.closeAll();
    }
</script>

</body>
</html>