<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:91:"D:\project\v3\view\admin\app\public/../application//order/view/order/goods_change_list.html";i:1554710051;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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

<style>
    [v-cloak]{display:none;}
</style>
<div class="layui-fluid" id="vue_main" v-cloak >
    <div class="layui-card">
        <div class="layui-card-body">
            <table class="layui-table">
                <colgroup>
                    <col>
                    <col>
                    <col v-for="(prop,p_index) in order_info.prop_list">
                    <col width="200">
                    <col>
                </colgroup>
                <thead>
                <tr>
                    <th>商品图</th>
                    <th>商品名</th>
                    <th v-for="(prop,p_index) in order_info.prop_list">{{prop}}</th>
                    <th>erp编码</th>
                    <th>售价</th>
                    <th>库存</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(sku,s_index) in order_info.sku_list" v-if="sku.sales_status == 1 && sku.is_deleted == 0 " >
                    <td><img alt="" :src="sku.item_image" onerror="this.src='/static/jwt/images/upload_add.png'" ></td>
                    <td>{{order_info.goods_name}}</td>
                    <th v-for="(prop,p_index) in order_info.prop_list">
                        <span v-for="(pv,pv_index) in order_info.prop_val_list[p_index]" v-if="sku.pv_id.indexOf(pv_index) >= 0"  >{{pv}}</span>
                    </th>
                    <td>{{sku.erp_code}}</td>
                    <td>{{sku.price}}</td>
                    <td>{{sku.stock}}</td>
                    <td v-if="item_id == sku.item_id">当前商品</td>
                    <td v-else >
                        <button class="layui-btn layui-btn-danger layui-btn-xs" style="margin:0;"  v-on:click="replace(sku)" >替换</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript" charset="utf-8" src="/static/js/vue.js"></script>
<script>
    var Vue = new Vue({
        el: '#vue_main',
        data: {
            'order_goods_id': getUrlParam('order_goods_id'),
            'item_id': getUrlParam('item_id'),
            'goods_id': getUrlParam('goods_id'),
            'shop_id': getUrlParam('shop_id'),
            'order_info' : [],
        },
        mounted:function(){
            var that = this;
            var param = {};
            param.id = that.goods_id;
            param.shop_id = that.shop_id;
            request.setHost(SHOP_DATA).post('/goods/goods/getGoodsInfo',param, function(res){
                if (res.code == 0) {
                    that.order_info = res.data;
                } else {
                    // 错误提示
                    layer.msg(res.msg);
                }
            });
        },
        methods:{
            replace:function(item_info){
                var that = this;
                if( item_info.stock == 0 ){
                    layer.msg('没有库存');
                    return false;
                }
                layer.confirm('是否确认更换商品 （若价格有差异不会主动进行补差业务，请尽量选择同价商品）', {
                    btn: ["确定","取消"] //按钮
                }, function(){
                    var param = {};
                    param.order_goods_id = that.order_goods_id;
                    param.item_id = item_info.item_id;
                    layer.prompt({
                        title: '请填写替换数量'
                    }, function(val, index){
                        if(isNaN(val)){
                            layer.msg('必须填写数字');
                            return false;
                        }
                        param.num = val;
                        request.setHost(SHOP_DATA).post('/order/order/replaceOrderGoods',param, function(res){
                            if (res.code == 0) {
                                that.order_info = res.data;
                                setTimeout(function(){
                                    parent.callback();
                                },1000);
                            } else {
                                // 错误提示
                                layer.msg(res.msg);
                            }
                            layer.close(index);
                        });
                    });
                });
            }
        }
    });
</script>

</body>
</html>