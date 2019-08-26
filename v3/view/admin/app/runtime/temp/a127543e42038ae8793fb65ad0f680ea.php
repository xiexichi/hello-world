<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:78:"D:\project\v3\view\admin\app\public/../application//goods/view/tag/detail.html";i:1547607693;}*/ ?>
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
        request.setHost(SHOP_DATA).post('/goods/goods_tag/getTagInfo',param, function(res){
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

