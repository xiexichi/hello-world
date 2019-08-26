<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:90:"D:\project\v3\view\admin\app\public/../application//goods/view/attribute/group_detail.html";i:1552902444;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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

<form class="layui-form" action="" style="margin-top:30px;">
    <div class="layui-form-item">
        <label class="layui-form-label">参数组名</label>
        <div class="layui-input-block">
            <input type="text" id="group_name" name="group_name" required  lay-verify="required" placeholder="请输入参数组名" autocomplete="off" class="layui-input" style="width:auto;">
            <input id="id" name="id" type="hidden" class="layui-input" value="">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">参数成员</label>
        <div class="layui-input-block" id="attr_checkbox">
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
        getGroupInfo();
        // 渲染表单
    });

    //获取组别信息
    function getGroupInfo(){
        var param = {};
        param.id = id;
        request.setHost(SHOP_DATA).post('/goods/attribute_group/getAttrGroupInfo',param, function(res){
            if (res.code == 0) {
                // 成功提示
                $('#group_name').val(res.data.group_name);
                $('#id').val(res.data.id);
                getAttrList(res.data.attr_list);
            } else {
                // 错误提示
                layer.msg(res.msg);
                if( res.code == 200004 ){
                    history.go(-1);
                }
            }
        });
    }

    function getAttrList(checked_list){
        request.setHost(SHOP_DATA).get('/goods/attribute_cate/getAttrCateList/?page=1&limit=0', function(res){
            if (res.code == 0) {
                // 成功提示
                var checkbox_input = '';
                for( var i = 0; i < res.data.length; i++ ){
                    checkbox_input += '<input type="checkbox" name="attr[]" lay-filter="attr" value="'+res.data[i].id+'" title="'+res.data[i].attr_name+'" lay-skin="primary" '+(checked_list.indexOf(res.data[i].id) >= 0 ? 'checked' : '')+'>';
                }
                $('#attr_checkbox').html(checkbox_input);
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
            request.setHost(SHOP_DATA).post('/goods/attribute_group/saveAttrGroupInfo', data.field, function(res){
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