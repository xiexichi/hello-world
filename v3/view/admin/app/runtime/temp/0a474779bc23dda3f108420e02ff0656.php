<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:86:"D:\project\v3\view\admin\app\public/../application//user/view/user_level/page_add.html";i:1556332579;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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
    <form class="layui-form table-box" action="" id="form" lay-filter="form">
        <div class="layui-card">
            <div class="layui-card-body">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <button class="layui-btn" lay-submit lay-filter="submitBtn">立即提交</button>
                        <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                        <button class="layui-btn layui-btn-primary" onclick="parent.window.callback()">返回</button>
                    </div>
                </div>
                <div class="layui-tab layui-tab-card">
                    <div class="layui-tab-content" >
                        <div class="layui-tab-item layui-show">
                            <div class="layui-form-item layui-form-text">
                                <label class="layui-form-label">等级名称</label>
                                <div class="layui-input-block">
                                    <input class="layui-input" lay-verify="required" type="text" name="name" placeholder="等级名称" />
                                </div>
                            </div>
                            <div class="layui-form-item layui-form-text">
                                <label class="layui-form-label">等级折扣</label>
                                <div class="layui-input-block">
                                    <input class="layui-input" value="10" lay-verify="required" type="number" name="discount" placeholder="等级折扣" />
                                </div>
                            </div>
                            <div class="layui-form-item layui-form-text">
                                <label class="layui-form-label">升级界限</label>
                                <div class="layui-input-block">
                                    <input class="layui-input" value="0" lay-verify="required" type="number" name="level_limit" placeholder="自动升级的界限" />
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">自动升级</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="auto_update" value="1" title="是">
                                    <input type="radio" name="auto_update" value="0" title="否" checked="">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">包邮</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="if_free" value="1" title="是">
                                    <input type="radio" name="if_free" value="0" title="否" checked="">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">使用优惠券</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="is_use_coupon" value="1" title="是">
                                    <input type="radio" name="is_use_coupon" value="0" title="否" checked="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        layui.use(['jquery', 'form', 'layer'], function(){
            var $ = layui.jquery;
            var form = layui.form;
            var layer = layui.layer;
            //监听提交
            form.on('submit(submitBtn)', function(data){
                request.setHost(CENTER_DATA).post('/user/user_level/add', data.field, function(res){
                    if (res.code == 0) {
                        layer.msg(res.msg);
                        setTimeout(function(){
                            parent.window.callback();
                        },1000);
                    } else {
                        layer.msg(res.msg);
                    }
                });
                return false;
            });

            form.render();
            
        });
    });

</script>


</body>
</html>