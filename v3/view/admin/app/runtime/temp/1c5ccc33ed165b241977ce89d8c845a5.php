<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:84:"D:\project\v3\view\admin\app\public/../application//goods/view/attribute/detail.html";i:1547085340;}*/ ?>
<style>
    .table-box{margin-top:20px;}
</style>
<form class="layui-form table-box" action="">
    <div class="layui-form-item">
        <label class="layui-form-label">参数名</label>
        <div class="layui-input-block">
            <input id="attr_name" type="text" name="attr_name" required  lay-verify="required" placeholder="请输入标题" autocomplete="off" class="layui-input" style="width:auto;">
            <input id="ids" type="hidden" name="id" required  lay-verify="required" >
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit lay-filter="formDemo">保存修改</button>
        </div>
    </div>
</form>
<script>
    var id = getUrlParam('id');
    $(document).ready(function(){
        var param = {};
        param.id = id;
        request.setHost(SHOP_DATA).post('/goods/attribute_cate/getGoodsAttrCateInfo',param, function(res){
            if (res.code == 0) {
                // 成功提示
                $('#attr_name').val(res.data.attr_name);
                $('#ids').val(res.data.id);
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
            request.setHost(SHOP_DATA).post('/goods/attribute_cate/edit', data.field, function(res){
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

