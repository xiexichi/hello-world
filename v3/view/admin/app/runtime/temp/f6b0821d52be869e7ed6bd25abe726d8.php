<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:93:"D:\project\v3\view\admin\app\public/../application//powershop/view/power_module/page_add.html";i:1555997869;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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
        <input type="hidden" name="id" value="0">
        <div class="layui-card">
            <div class="layui-card-body" id="upload_main" >
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <button class="layui-btn" lay-submit lay-filter="formAdd">立即提交</button>
                        <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                        <button class="layui-btn layui-btn-primary" onclick="parent.window.callback()">返回</button>
                    </div>
                </div>
                <div class="layui-tab layui-tab-card">
                    <div class="layui-tab-content" >
                        <div class="layui-tab-item layui-show">
                            <div class="layui-form-item">
                                <div class="layui-inline">
                                    <label class="layui-form-label">显示名称</label>
                                    <div class="layui-input-inline">
                                        <input type="title" name="title" lay-verify="required" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <div class="layui-inline">
                                    <label class="layui-form-label">代码名称</label>
                                    <div class="layui-input-inline">
                                        <input type="name" name="name" lay-verify="required" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">是否显示</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="is_show" value="1" title="显示" checked="">
                                    <input type="radio" name="is_show" value="2" title="隐藏">
                                </div>
                            </div>

                            <div class="layui-form-item">
                                <label class="layui-form-label">排序</label>
                                <div class="layui-input-inline">
                                    <input type="number" min="0" max="255" name="sort" placeholder="0-255" value="50" autocomplete="off" class="layui-input">
                                </div>
                                <div class="layui-form-mid layui-word-aux">(排序由大到小排列)</div>
                            </div>

                            <div class="layui-form-item">
                                <label class="layui-form-label">图标代码</label>
                                <div class="layui-input-inline">
                                    <input type="text" min="0" max="255" name="icon_code" autocomplete="off" class="layui-input">
                                </div>
                                <div class="layui-form-mid layui-word-aux">(如layui-icon-fire)</div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    var form = layui.form;
    $(document).ready(function(){
        layui.use('form', function(){
            //监听提交
            form.on('submit(formAdd)', function(data){
                request.setHost(SHOP_DATA).post('/power/power_module/add', data.field, function(res){
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
        });
        form.render();
    });
</script>


</body>
</html>