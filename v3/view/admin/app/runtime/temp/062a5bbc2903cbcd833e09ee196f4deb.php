<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:79:"D:\project\v3\view\admin\app\public/../application//goods/view/prop/detail.html";i:1552901441;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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
<form class="layui-form table-box" lay-filter="form-table" action="">
    <div class="layui-form-item">
        <label class="layui-form-label">属性类型名</label>
        <div class="layui-input-block">
            <input type="text" name="prop_name" required  lay-verify="required" placeholder="请输入属性类型名" autocomplete="off" class="layui-input" style="width:auto;">
            <input type="hidden" name="id" lay-verify="required" class="class-input" value="" >
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">排序</label>
        <div class="layui-input-inline">
            <input type="number" min="0" max="255" name="sort" required  lay-verify="required" placeholder="0-255" value="0" autocomplete="off" class="layui-input" onkeyup="checkSort(this)" >
        </div>
        <div class="layui-form-mid layui-word-aux">(排序由大都小排列)</div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">显示类型</label>
        <div class="layui-input-block">
            <input type="radio" name="show_type" value="0" title="文字" >
            <input type="radio" name="show_type" value="1" title="文图" >
            <input type="radio" name="show_type" value="2" title="图片" >
            <input type="radio" name="show_type" value="3" title="颜色" >
        </div>
    </div>
    <!--<div class="layui-form-item">-->
        <!--<label class="layui-form-label">是否必选</label>-->
        <!--<div class="layui-input-block">-->
            <!--<input type="radio" name="is_changed" value="0" title="否" >-->
            <!--<input type="radio" name="is_changed" value="1" title="是" >-->
        <!--</div>-->
    <!--</div>-->
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">属性描述</label>
        <div class="layui-input-block">
            <textarea name="prop_desc" id="prop_desc" style="resize:none; width:300px;"  placeholder="请输入内容" class="layui-textarea"></textarea>
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit lay-filter="formDemo">立即提交</button>
        </div>
    </div>
</form>
<script>
    function checkSort(obj){
        var num = $(obj).val();
        if(num > 255){
            $(obj).val(255);
        }else if(num < 0){
            $(obj).val(0);
        }
    }
    var id = getUrlParam('id');
    var form = layui.form;
    $(document).ready(function(){
        var param = {};
        param.id = id;
        request.setHost(SHOP_DATA).post('/goods/goods_prop/one',param, function(res){
            if (res.code == 0) {
                $("input[name=id]").val(id);
                $("input[name=prop_name]").val(res.data.prop_name);
                $("input[name=sort]").val(res.data.sort);
                $("input[name=show_type][value="+res.data.show_type+"]").attr("checked",true);
                $("input[name=is_changed][value="+res.data.is_changed+"]").attr("checked", true);
                $("textarea[name=prop_desc]").val(res.data.prop_desc);
                form.render();
            } else {
                // 错误提示
                layer.msg(res.msg);
            }
        });
        form.render();
    });
    layui.use('form', function(){
        //监听提交
        form.on('submit(formDemo)', function(data){
            request.setHost(SHOP_DATA).post('/goods/goods_prop/edit', data.field, function(res){
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