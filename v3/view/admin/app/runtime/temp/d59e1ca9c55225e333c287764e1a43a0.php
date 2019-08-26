<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:87:"D:\project\v3\view\admin\app\public/../application//power/view/power_role/page_add.html";i:1556619432;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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
                            <div class="layui-form-item" id="input_type">
                                <label class="layui-form-label">角色类型</label>
                                <div class="layui-input-block">
                                    <select name="type" lay-verify="required" lay-filter="selectRoleType">
                                        <option></option>
                                        <option value="1">后台角色</option>
                                        <option value="2">商户角色</option>
                                    </select>
                                </div>
                            </div>
                            <div class="layui-form-item layui-form-text">
                                <label class="layui-form-label">角色名称</label>
                                <div class="layui-input-block">
                                    <input class="layui-input" lay-verify="required" type="text" name="title" placeholder="请输入角色名称" />
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">是否启用</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="status" value="1" title="启动" checked="">
                                    <input type="radio" name="status" value="2" title="禁用">
                                </div>
                            </div>
                            <div class="layui-form-item layui-form-text">
                                <label class="layui-form-label">备注</label>
                                <div class="layui-input-block">
                                    <textarea name="note" placeholder="请输入内容" class="layui-textarea"></textarea>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">选择权限</label>
                                <div class="layui-input-block">
                                    <div id="LAY-auth-tree-index"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style type="text/css">
    #input_type{display: none}
</style>

<script type="text/javascript">
    layui.config({
        base: '/static/layui/lib/extends/',
    }).extend({
        authtree: 'authtree',
    });



    $(document).ready(function(){
        layui.use(['jquery', 'authtree', 'form', 'layer'], function(){
            var $ = layui.jquery;
            var authtree = layui.authtree;
            var form = layui.form;
            var layer = layui.layer;


            request.setHost(CENTER_DATA).get('/power/power_role/getAddData',{}, function(res){
                if( res.code == 0 ){

                    if(res.data.adminIsSuper == 1){https://hnzzmsf.github.io/example/example_v4.html
                        $('#input_type').show();
                    }else{
                        //去除必填
                        $("#input_type select").removeAttr("lay-verify");
                    }

                    //角色权限
                    var trees = res.data.trees;
                    //如果后台返回的不是树结构，请使用 authtree.listConvert 转换
                    authtree.render('#LAY-auth-tree-index', trees, {
                        inputname: 'authids[]',
                        layfilter: 'lay-check-auth',
                        autowidth: true,
                    });
                    form.render();
                }
            });


            //监听提交
            form.on('submit(submitBtn)', function(data){
                request.setHost(CENTER_DATA).post('/power/power_role/add', data.field, function(res){
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
    });

</script>


</body>
</html>