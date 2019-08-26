<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:81:"D:\project\v3\view\admin\app\public/../application//goods/view/attribute/add.html";i:1547447746;}*/ ?>
<style>
    .table-box{margin-top:20px;}
</style>
<form class="layui-form table-box" action="">
    <div class="layui-form-item">
        <label class="layui-form-label">参数名</label>
        <div class="layui-input-block">
            <input type="text" name="attr_name" required  lay-verify="required" placeholder="请输入参数名" autocomplete="off" class="layui-input" style="width:auto;">
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
    layui.use('form', function(){
        var form = layui.form;
        //监听提交
        form.on('submit(formDemo)', function(data){
            request.setHost(SHOP_DATA).post('/goods/attribute_cate/add', data.field, function(res){
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

