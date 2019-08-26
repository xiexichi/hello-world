<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:81:"D:\project\v3\view\admin\app\public/../application//goods/view/goods/set_tag.html";i:1548748226;}*/ ?>
<form class="layui-form" action="" style="margin-top:30px;">
    <div class="layui-form-item">
        <label class="layui-form-label">标签列表</label>
        <div class="layui-input-block" id="tag_checkbox">
        </div>
    </div>
    <div class="layui-form-item">
        <div class="layui-input-block">
            <button class="layui-btn" lay-submit lay-filter="formDemo">保存修改</button>
        </div>
    </div>
</form>

<script>
    var form = layui.form;
    var id = getUrlParam('id');
    $(document).ready(function(){
        getGoodsBind();
        // 渲染表单
    });

    //获取组别信息
    function getGoodsBind(){
        var param = {};
        param.id = id;
        request.setHost(SHOP_DATA).post('/goods/goods/getGoodsTag',param, function(res){
            if (res.code == 0) {
                // 成功提示
                getTagList(res.data);
            } else {
                // 错误提示
                layer.msg(res.msg);
                if( res.code == 200004 ){
                    history.go(-1);
                }
            }
        });
    }

    function getTagList(checked_list){
        request.setHost(SHOP_DATA).get('/goods/goods_tag/getTagAll/?page=1&limit=0', function(res){
            if (res.code == 0) {
                // 成功提示
                var checkbox_input = '';
                for( var i = 0; i < res.data.length; i++ ){
                    checkbox_input += '<input type="checkbox" name="tag[]" value="'+res.data[i].id+'" title="'+res.data[i].tag_name+'" '+(checked_list.indexOf(res.data[i].id) >= 0 ? 'checked' : '')+'>';
                }
                $('#tag_checkbox').html(checkbox_input);
                form.render();
            } else {
                // 错误提示
                layer.msg(res.msg);
            }
        });
    }

    layui.use('form', function(){

        //监听提交
        form.on('submit(formDemo)', function(data){
            var param = data.field;
            param.id = id;
            request.setHost(SHOP_DATA).post('/goods/goods/bindTag', param, function(res){
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
