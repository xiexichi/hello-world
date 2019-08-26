<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:82:"D:\project\v3\view\admin\app\public/../application//order/view/order/ship_set.html";i:1554626227;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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
    #goods_list .goods-list{float:left; width:100px; font-size:12px; padding: 0 20px;}
    #goods_list .goods-list div{text-align:center; margin:0 auto;}
</style>
<div class="layui-card">
    <div class="layui-card-body">
        <form class="layui-form" action="">
            <div class="layui-form-item">
                <label class="layui-form-label">商品列表</label>
                <div class="layui-input-block" id="goods_list">
                </div>
            </div>
            <div class="layui-form-item" id="delivery_list">
                <label class="layui-form-label">物流公司</label>
                <div class="layui-input-inline">
                    <select name="express_id" lay-verify="" lay-search id="delivery">
                    </select>
                </div>
                <div class="layui-form-mid layui-word-aux">自提单请选其他</div>
            </div>
            <div class="layui-form-item" id="delivery_sn">
                <label class="layui-form-label">物流单号</label>
                <div class="layui-input-inline">
                    <input type="text" name="express_sn" autocomplete="off" class="layui-input">
                </div>
                <div class="layui-form-mid layui-word-aux">自提单不填</div>
            </div>
            <div class="layui-form-item layui-form-text">
                <label class="layui-form-label">发货备注</label>
                <div class="layui-input-block">
                    <textarea name="desc" placeholder="请输入内容" class="layui-textarea"></textarea>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit lay-filter="formDemo">立即提交</button>
                    <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    var order_id = getUrlParam('id');
    var order_type = getUrlParam('type');
    var form = layui.form;
    layui.use('form', function(){
        //监听提交
        form.on('submit(formDemo)', function(data) {
            data.field.order_id = order_id;
            request.setHost(SHOP_DATA).post('/order/order_delivery_package/createPackage', data.field, function (res) {
                if (res.code == 0) {
                    layer.msg(res.msg);
                    setTimeout(function(){
                        parent.window.callback();
                    },1500);
                } else {
                    // 错误提示
                    layer.msg(res.msg);
                }
                return false;
            });
            return false;
        });
    });
    $(document).ready(function(){
        if( order_type == 1 ){
            $('#delivery_sn').remove();
        }
        getDeliveryList(order_type);
        getOrderGoods();
    });

    function getOrderGoods(){
        request.setHost(SHOP_DATA).post('/order/order_goods/getOrderGoodsList',{'order_id':order_id}, function(res){
            if (res.code == 0) {
                var goodsHtml = '';
                for( var g = 0; g < res.data.length; g++){
                    goodsHtml += '<div class="goods-list">' +
                        '<div><img src="'+res.data[g].item_images+'"></div>' +
                        '<div><span>'+res.data[g].erp_code+'</span><br /> ×<span>'+res.data[g].num+'</span></div>' +
                        '<div><span>'+res.data[g].goods_status_desc+'</span></div>';
                    if( res.data[g].ship_status == 0 && res.data[g].status == 0 ){
                        goodsHtml += '<div>' +
                        '<input type="checkbox" name="orderGoods[]"" value="'+res.data[g].id+'" lay-skin="primary" >'+
                        '</div>';
                    }
                    goodsHtml += '</div>'
                }
                $('#goods_list').html(goodsHtml);
                form.render();
            } else {
                // 错误提示
                layer.msg(res.msg);
            }
        });
    }

    //获取物流公司
    function getDeliveryList(order_type){
        request.setHost(SHOP_DATA).get('/system/express/all', function(res){
            if (res.code == 0) {
                var deliveryHtml = '';
                for( var i = 0; i < res.data.length; i++ ){
                    var selected = '';
                    if( order_type == 1 && res.data[i].code == 'OTHER' ){
                        selected = 'selected';
                    }
                    deliveryHtml += '<option value="'+res.data[i].id+'" '+selected+' >'+res.data[i].name+'</option>';
                }
                $('#delivery').html(deliveryHtml);
                form.render();
            } else {
                // 错误提示
                layer.msg(res.msg);
            }
        });
    }

</script>

</body>
</html>