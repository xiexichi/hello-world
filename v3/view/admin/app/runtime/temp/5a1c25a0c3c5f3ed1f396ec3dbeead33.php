<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:80:"D:\project\v3\view\admin\app\public/../application//goods/view/prop/val_add.html";i:1547347201;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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
        <label class="layui-form-label">属性值名</label>
        <div class="layui-input-block">
            <input type="text" name="pv_name" required  lay-verify="required" placeholder="请输入属性值名" autocomplete="off" class="layui-input" style="width:auto;">
            <input type="hidden" name="goods_prop_id" value="">
        </div>
    </div>
    <div class="layui-form-item" id="pv_type_val_box">
        <label class="layui-form-label">显示内容</label>
        <div class="layui-input-block box-input" >
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">erp编码</label>
        <div class="layui-input-block" >
            <input type="text" name="pv_erp_code" required  placeholder="请输入erp编码" autocomplete="off" class="layui-input" style="width:auto;">
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">描述</label>
        <div class="layui-input-block">
            <textarea name="pv_desc" style="resize:none; width:300px;"  placeholder="请输入内容" class="layui-textarea"></textarea>
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit lay-filter="formDemo">立即提交</button>
            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
        </div>
    </div>
</form>
<script>
    var id = getUrlParam('id');
    var show_type = getUrlParam('show_type');
    $(document).ready(function(){
        var form = layui.form;
        $('input[name=goods_prop_id]').val(id);
        //表单配置
        setShowTypeBox(show_type);
        form.render();
        layui.use('form', function(){
            //监听提交
            form.on('submit(formDemo)', function(data){
                request.setHost(SHOP_DATA).post('/goods/goods_prop_val/add', data.field, function(res){
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
    });

    function setShowTypeBox(show_type){
        var pv_type_val_input = '';
        switch(show_type){
            case '0' :
                pv_type_val_input = '<input type="text" name="pv_type_val" required  lay-verify="required" placeholder="显示类型内容" autocomplete="off" class="layui-input" style="width:auto;">';
                break;
            case '3' :
                pv_type_val_input = '<div class="layui-input-inline" style="width: 120px;">' +
                    '<input type="text" name="pv_type_val" value="#fb5a5c" placeholder="请选择颜色" class="layui-input" id="test-form-input">' +
                    '</div>' +
                    '<div class="layui-input-inline" style="width: 120px;">' +
                    '<div id="color"></div>' +
                    '</div>';
                break;
            default :
                pv_type_val_input = '<input type="text" placeholder="商品独立上传图片" disabled class="layui-input"  style="width:auto;" />';
                break;
        }
        $('#pv_type_val_box .box-input').html(pv_type_val_input);
        if( show_type = '3' ){
            layui.use('colorpicker', function(){
                var colorpicker = layui.colorpicker;
                //渲染
                colorpicker.render({
                    elem: '#color',  //绑定元素
                    color: '#fb5a5c',
                    done: function(color){
                        $('input[name=pv_type_val]').val(color);
                    }
                });
            });
        }
    }




</script>


</body>
</html>