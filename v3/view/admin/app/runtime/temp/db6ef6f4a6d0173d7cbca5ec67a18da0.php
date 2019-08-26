<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:78:"D:\project\v3\view\admin\app\public/../application//goods/view/tag/detail.html";i:1552901920;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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
    .table-box{margin-top:20px;}
</style>
<form class="layui-form table-box" action="">
    <div class="layui-form-item">
        <label class="layui-form-label">参数名</label>
        <div class="layui-input-inline">
            <input id="attr_name" type="text" name="tag_name" required  lay-verify="required" placeholder="请输入标题" autocomplete="off" class="layui-input" >
            <input id="ids" type="hidden" name="    id" required  lay-verify="required" >
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">排序</label>
        <div class="layui-input-inline">
            <input type="number" min="0" id="sort" max="255" name="sort" required  lay-verify="required" placeholder="0-255" autocomplete="off" class="layui-input" onkeyup="checkSort(this)" value="0" >
        </div>
        <div class="layui-form-mid layui-word-aux">(排序由大都小排列)</div>
    </div>
    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit lay-filter="formDemo">保存修改</button>
        </div>
    </div>
</form>
<script>
    var id = getUrlParam('id');
    function checkSort(obj){
        var num = $(obj).val();
        if(num > 255){
            $(obj).val(255);
        }else if(num < 0){
            $(obj).val(0);
        }
    }
    $(document).ready(function(){
        var param = {};
        param.id = id;
        request.setHost(SHOP_DATA).post('/goods/goods_tag/one',param, function(res){
            if (res.code == 0) {
                // 成功提示
                $('#attr_name').val(res.data.tag_name);
                $('#ids').val(res.data.id);
                $('#sort').val(res.data.sort);
            } else {
                // 错误提示
                layer.msg(res.msg);
            }
        });
    });
    layui.use('form', function(){
        var form = layui.form;
        //监听提交
        form.on('submit(formDemo)', function(data){
            request.setHost(SHOP_DATA).post('/goods/goods_tag/edit', data.field, function(res){
                if (res.code == 0) {
                    // 成功提示
                    layer.msg(res.msg);
                    setTimeout(function(){
                        parent.window.callback();
                    },1500);
                } else {
                    // 错误提示
                    layer.msg(res.msg);
                }
            });
            return false;
        });
    });
</script>


</body>
</html>