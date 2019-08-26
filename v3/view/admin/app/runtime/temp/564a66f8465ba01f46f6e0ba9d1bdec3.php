<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:82:"D:\project\v3\view\admin\app\public/../application//activity/view/coupon/sand.html";i:1554099397;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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
            <div class="layui-form-item">
                <label class="layui-form-label">单独发放</label>
                <div class="layui-input-inline">
                    <input type="text" name="user_id" value="" placeholder="会员id" class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">所有会员统一配发</label>
            </div>
            <div class="layui-form-item">
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
        </div>
    </div>
</div>
<script>
    var id = getUrlParam('id');
    if( id == null ) {
        parent.window.callback();
    }
    function ready(){
        request.setHost(SHOP_DATA).post('/activity/coupon/one',param, function(res){
            if (res.code == 0) {
                console.log(res.code);
            } else {
                // 错误提示
                layer.msg(res.msg);
            }
        });
    },
</script>

</body>
</html>