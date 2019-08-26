<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:78:"D:\project\v3\view\admin\app\public/../application//order/view/order/list.html";i:1551159937;}*/ ?>
<style>
    .layui-table-body .layui-table-cell{
        height:auto;
        overflow:hidden;
        border:1px solid #ccc;
        padding:0;
    }
    .layui-btn{min-width:80px;}
    .order-info{padding:0 0 0 20px; height:35px; line-height:35px; border-bottom:1px solid #ccc;}
    .order-info span{margin-right:10px;}
    .goods-list{float:left;padding:15px 0 15px 20px; height:170px; line-height:150px;}
    .goods-list .goods{float:left; height:140px; width:100px; line-height:140px; text-align:center; margin-right:10px;}
    .goods-list img{height:100px; width:100px;}
    .goods-list p{height:20px; width:100px; line-height:20px; font-size:12px;}
    .order-status-box,.order-content{float:right;padding:45px 0 15px 0; height:190px;}
    .order-status-box{width:200px; text-align:center; border-left:1px solid #ccc; border-right:1px solid #ccc;}
    .order-content{width:200px; text-align:center;}
</style>
<!--导航-->
<ul class="layui-nav" lay-filter="nav">
    <li class="layui-nav-item layui-this"><a href="/order/order/list.html">全部</a></li>
    <li class="layui-nav-item"><a href="/order/order/list.html">未审核</a></li>
    <li class="layui-nav-item"><a href="/order/order/list.html/?verify=1">已审核</a></li>
    <li class="layui-nav-item"><a href="/order/order/list.html/?verify=2">不通过</a></li>
</ul>
<!--数据列表-->
<div class="layui-card">
    <div class="layui-card-body">
        <table id="order" lay-filter="test"></table>
    </div>
</div>
<script>
    var table = layui.table;
    $(document).ready(function(){
        loadlist();
    });
    function loadlist(){
        //执行渲染
        table.render({
            elem: '#order' //
            ,height: 'auto' //容器高度
            ,url: '/order/order/index'
            ,page: true
            ,toolbar:true
            ,headers: {
                ctrl: SHOP_DATA
            }
            ,cols: [[
                {title: '',templet: '#orderTpl'}
            ]]
        });
    }

    table.on('tool(test)', function(obj){
        var data = obj.data;
        var layEvent = obj.event; //获得 lay-event 对应的值（也可以是表头的 event 参数对应的值）
        var tr = obj.tr; //获得当前行 tr 的DOM对象
        if(layEvent === 'detail'){ //查看订单详情
            var toUrl = "/order/order/detail.html?id="+data.order_id;
            layer.open({
                title:'订单详情',
                type:2,
                shadeClose: true,
                closeBtn:1,
                area:['90%','90%'],
                content:toUrl
            });
        } else if(layEvent === 'set_pay'){ //设为已支付
            layer.confirm("是否确认设为已支付！", {
                btn: ["确定","取消"] //按钮
            }, function(){
                return false;
            });
        } else if(layEvent === 'set_verify'){ //审核
            layer.confirm("审核订单", {
                btn: ["通过","不通过"] //按钮
            }, function(){
                alert(2);
                return false;
            },function(){
                alert(3);
                return false;
            });
        } else if(layEvent === 'set_ship'){ //发货
            var toUrl = "/order/order/detail.html?id="+data.order_id;
            layer.open({
                title:'填写发货信息',
                type:2,
                shadeClose: true,
                closeBtn:1,
                area:['90%','90%'],
                content:toUrl
            });
        } else if(layEvent === 'set_finished'){ //设为已收货（完成订单）
            layer.confirm("是否确认设为已收货！", {
                btn: ["确定","取消"] //按钮
            }, function(){
                return false;
            });
        } else if(layEvent === 'evalition_detail'){ //查看订单评价
            var toUrl = "/order/order/detail.html?id="+data.order_id;
            layer.open({
                title:'用户订单评价',
                type:2,
                shadeClose: true,
                closeBtn:1,
                area:['90%','90%'],
                content:toUrl
            });
        }
    });

    function callback(){
        loadlist();
        layer.closeAll();
    }
</script>

<!-- 操作模板 -->
<script type="text/html" id="orderTpl">
    <div class="order-info">
        <span>店铺：{{d.shop_info.name}}</span>
        <span style="color:#FB5A5C;">订单号：<i>{{d.order_sn}}</i></span>
        <span style="color:#01AAED;">会员：{{d.user_info.user_name}}</span>
        <span>下单时间：{{d.create_time}}</span>
    </div>
    <div class="goods-list">
        {{# layui.each(d.goods_list,function(index,goods){ if( index < 5 ){ }}
            <div class="goods" >
                <img src="{{goods.item_info.item_img}}" >
                <p style="text-align:center;" title="{{goods.erp_code}}">{{goods.erp_code}}</p>
                <p style="text-align:center;">{{goods.item_price}} × {{goods.num}}</p>
            </div>
        {{# } }) }}
        <div>
            {{ d.goods_list.length > 5 ? '......' : ''}}
        </div>
    </div>
    <div class="order-content">
        总计：{{ d.order_price }}<br />
        优惠：{{ eval(d.discount_price+'+'+d.coupon_price) }}<br />
        运费：{{ d.ship_price }}<br />
        <button class="layui-btn layui-btn-xs" style="margin:0;" lay-event="detail" >查看详情</button>
    </div>
    <div class="order-status-box">
        <div style="color:#FB5A5C;">{{d.orderStatusDesc}}</div>
        {{# if(d.order_status == 0) { }}
            {{# if(d.pay_status == 0) { }}
            <button class="layui-btn layui-btn-sm layui-btn-warm" lay-event="set_pay" >设为已支付</button>
            {{# } }}
            {{# if ( d.pay_status == 1 && d.order_verify == 0 ) { }}
            <button class="layui-btn layui-btn-sm layui-btn-warm" lay-event="set_verify" >审核</button>
            {{# } }}
            {{# if ( d.order_verify == 1 && d.shipping_status == 0 ) { }}
            <button class="layui-btn layui-btn-sm layui-btn-warm" lay-event="set_ship" >发货</button>
            {{# } }}
            {{# if ( d.shipping_status == 1 && d.finished_status == 0 && d.evaluation_status == 0 ) { }}
            <button class="layui-btn layui-btn-sm layui-btn-warm" lay-event="set_finished" >设为已收货</button>
            {{# } }}
            {{# if ( d.evaluation_status == 1 && d.finished_status == 1 ) { }}
            <button class="layui-btn layui-btn-sm layui-btn-warm" lay-event="evalition_detail" >查看订单评价</button>
            {{# } }}
        {{# }else{ }}
        <button class="layui-btn layui-btn-sm layui-btn-warm" onclick="set(d.order_id)">设为正常</button>
        {{# } }}
    </div>
</script>
