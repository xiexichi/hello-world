<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:80:"D:\project\v3\view\admin\app\public/../application//order/view/order/detail.html";i:1551170226;}*/ ?>
<style>
    .body-tab .title{text-align:right; }
    .body-tab td{ min-width:100px; }
</style>
<div id="vue_main">
    <div class="layui-card">
        <div class="layui-card-header">基础信息</div>
        <div class="layui-card-body">
            <table class="body-tab">
                <tr>
                    <td class="title">店铺：</td>
                    <td>{{ shop_info.name }}</td>
                    <td class="title">发货方式：</td>
                    <td style="color:#FB5A5C;" >{{ order_info.orderTypeDesc }}</td>
                </tr>
                <tr>
                    <td class="title">订单号：</td>
                    <td>{{ order_info.order_sn }}</td>
                    <td class="title">订单状态：</td>
                    <td style="color:#FB5A5C;" >{{ order_info.orderStatusDesc }}</td>
                    <td class="title">支付类型：</td>
                    <td style="color:#FB5A5C;" >{{ order_info.pay_status ? '未关联支付类型' : '未支付' }}</td>
                </tr>
                <tr>
                    <td class="title">会员id：</td>
                    <td>{{ user_info.id }}</td>
                    <td class="title">会员名：</td>
                    <td>{{ user_info.user_name }}</td>
                    <td class="title">会员手机：</td>
                    <td>{{ user_info.phone }}</td>
                    <td class="title">下单时间：</td>
                    <td>{{ order_info.create_time }}</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="layui-card">
        <div class="layui-card-header">收货人信息</div>
        <div class="layui-card-body">
            <table class="body-tab">
                <tr>
                    <td class="title">收货人：</td>
                    <td>{{ consignee.consignee_name }}</td>
                    <td class="title">联系电话：</td>
                    <td>{{ consignee.mobile }}</td>
                    <!--<td class="title">邮编：</td>-->
                    <!--<td>{{ consignee.user_name }}</td>-->
                    <!--<td class="title">邮编:</td>-->
                </tr>
                <tr>
                    <td class="title">收货地址：</td>
                    <td colspan="5">{{ consignee.prov_name+consignee.city_name+consignee.area_name+' '+consignee.address }}</td>
                </tr>
            </table>
        </div>
    </div>
    <!--<div class="layui-card">-->
        <!--<div class="layui-card-body">-->
            <!--优惠信息-->
        <!--</div>-->
    <!--</div>-->
    <div class="layui-card">
        <div class="layui-card-header">综合信息</div>
        <div class="layui-card-body">
            <table class="body-tab">
                <tr>
                    <td style="color:#FB5A5C;">订单总额：</td>
                    <td style="color:#FB5A5C;" >{{ order_info.order_price }}</td>
                    <td style="color:#FB5A5C;">已实付金额：</td>
                    <td style="color:#FB5A5C;" >{{ order_info.pay_price }}</td>
                    <td style="color:#01AAED;">商品总额：</td>
                    <td style="color:#01AAED;">{{ order_info.goods_price }}</td>
                    <td style="color:#01AAED;">运费：</td>
                    <td style="color:#01AAED;">{{ order_info.ship_price }}</td>
                </tr>
                <tr>
                   <td style="color:#01AAED;">积分抵扣：</td>
                    <td style="color:#01AAED;">{{ order_info.discount_price }}</td>
                    <td style="color:#01AAED;">优惠券优惠：</td>
                    <td style="color:#01AAED;">{{ order_info.coupon_price }}</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="layui-card">
        <div class="layui-card-header">商品信息</div>
        <div class="layui-card-body">
            <table class="layui-table">
                <colgroup>
                    <col width="150">
                    <col >
                    <col>
                    <col>
                    <col width="80">
                    <col>
                </colgroup>
                <thead>
                <tr>
                    <th>图片</th>
                    <th>商品名</th>
                    <th>商品规格</th>
                    <th>erp货号</th>
                    <th>单价(现价)</th>
                    <th>数量</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(goods,index) in goods_list" >
                    <td><img :src="goods.item_info.item_img" ></td>
                    <td>{{goods.item_info.goods_name}}</td>
                    <td>{{goods.item_info.pv_name}}</td>
                    <td>{{goods.item_info.erp_code}}</td>
                    <td>{{goods.item_price}}({{goods.item_info.item_price}})</td>
                    <td>{{goods.num}}</td>
                    <td>人生就像是一场修行</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="layui-card">
        <div class="layui-card-header">订单日志</div>
        <div class="layui-card-body">
            <table class="layui-table">
                <colgroup>
                    <col width="150">
                    <col width="200">
                </colgroup>
                <thead>
                <tr>
                    <th>用户</th>
                    <th>内容</th>
                    <th>时间</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(log,index) in log" >
                    <td>{{ log.operator_type ? 'admin' : '会员' }}</td>
                    <td>{{ log.content }}</td>
                    <td>{{ log.create_time }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript" charset="utf-8" src="/static/js/vue.js"></script>
<script>
    var id = getUrlParam('id');

    var form = layui.form;
    var Vue = new Vue({
        el: '#vue_main',
        data:{
            'order_id' : id,
            'order_info' : {},
            'shop_info' : {},
            'user_info' : {},
            'consignee' : {},
            'goods_list' : {},
            'log' : {}
        },
        mounted:function(){
            var that = this;
            that.getOrderInfo();
        },
        methods:{
            getOrderInfo:function(){
                var that = this;
                var param = {};
                param.order_id = that.order_id;
                request.setHost(SHOP_DATA).post('/order/order/getOrderInfo',param, function(res){
                    if (res.code == 0) {
                        that.order_info = res.data.order_info;
                        that.shop_info = res.data.shop_info;
                        that.user_info = res.data.user_info;
                        that.consignee = res.data.consignee;
                        that.goods_list = res.data.goods_list;
                        that.log = res.data.log;
                    } else {
                        // 错误提示
                        layer.msg(res.msg);
                    }
                });
            }
        }
    });


</script>

