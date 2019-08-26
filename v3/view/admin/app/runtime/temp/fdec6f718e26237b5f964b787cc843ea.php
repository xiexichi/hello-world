<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:88:"D:\project\v3\view\admin\app\public/../application//power/view/power_group/page_add.html";i:1554711892;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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
    .layui-form-label{width:100px;}
    .star{color:#FB5A5C;}
    .layui-card-body .layui-form-checkbox{margin: 5px;}

    #access_view{padding: 10px;}
    .ctrl_div{padding: 0 20px 20px 20px;background-color: #fff;}
    .ctrl_div .layui-row{background-color: #f2f2f2}
    fieldset{background-color: #fff;}

</style>
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
                                <label class="layui-form-label required">组名称<span class="star"> * </span></label>
                                <div class="layui-input-inline">
                                    <input type="text" name="title" lay-verify="required" placeholder="请输入权限组名称" autocomplete="off" class="layui-input" style="width:300px;" >
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
                                    <input type="number" name="sort" value="50" autocomplete="off" class="layui-input" lay-verify="required|number">
                                </div>
                                <div class="layui-form-mid layui-word-aux">(排序由大到小排列)</div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">备注</label>
                                <div class="layui-input-inline">
                                    <textarea name="note" style="resize:none; width:300px;"  placeholder="请输入备注" class="layui-textarea"></textarea>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">上级模块</label>
                                <div class="layui-input-inline">
                                    <select name="module_id" lay-search lay-filter="selectModule" lay-verify="required">
                                    </select>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">上级控制器</label>
                                <div class="layui-input-inline">
                                    <select name="controller_id" lay-search lay-filter="selectController" lay-verify="required">
                                    </select>
                                </div>
                            </div>

                            <div id="action_view"></div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script id="action_tpl" type="text/html">
    <fieldset class="layui-elem-field site-demo-button" >
        <legend>权限项</legend>
        <div class="layui-card">
            {{# layui.each(d, function(index,item){  }}
            <input type="checkbox" value="{{ item.id }}" name="action_ids[]" title="{{ item.name }}">
            {{# }); }}
        </div>
    </fieldset>
</script>

<script type="text/javascript">
    var form = layui.form;
    var layer = layui.layer;
    var laytpl = layui.laytpl;
    var controllerDom = $('select[name=controller_id]');

    var selectModuleID = selectControllerID = 0;


    $(document).ready(function(){
        loadModelList();
        layui.use(['layer', 'form'], function(){
            form.on('select(selectModule)',function(data){
                selectModuleID = data.value;
                loadControllerList();
            });
            form.on('select(selectController)',function(data){
                selectControllerID = data.value;
                loadActionList();
            });
        });
        form.render();
    });

    layui.use(['layer','laytpl','form'], function(){
        form.on('submit(formAdd)', function (data) {
            let loadLayer = layer.load()
            request.setHost(CENTER_DATA).post('/power/power_group/add', data.field, function (res){
                layer.close(loadLayer)
                if (res.code == 0) {
                    // 成功提示
                    layer.msg(res.msg);
                    setTimeout(function(){
                        parent.window.callback();
                    },1000);
                }else{
                   layer.msg(res.msg);
                }
            });
            return false;
        })
    });

    function loadModelList(){
        // 获取模块列表
        request.setHost(CENTER_DATA).get('/power/power_module/all',{}, function(res){
            console.log(res);
            if( res.code == 0 ){
                var selectDom = $('select[name=module_id]');
                selectDom.append('<option value="">请选择上级模块</option>');
                for(var i in res.data){
                    selectDom.append('<option value="'+res.data[i].id+'">'+res.data[i].title+'</option>');
                }
                form.render();
            }
        });
    }
    function loadControllerList(){
        flushTpl([]);
        controllerDom.empty();
        controllerDom.append('<option value="">请选择上级模块</option>');
        if(selectModuleID){
            //获取控制器列表
            request.setHost(CENTER_DATA).get('/power/power_controller/all',{module_id:selectModuleID,type:'son_of_module'}, function(res){
                if( res.code == 0 ){
                    for(var i in res.data){
                        controllerDom.append('<option value="'+res.data[i].id+'">'+res.data[i].title+'</option>');
                    }
                }
                form.render();
            });
        }
        form.render();
    }
    function loadActionList(){
        if(!selectModuleID){
            layer.msg('请选择模块');
        }
        if(!selectControllerID){
            layer.msg('请选择控制器');
        }
        request.setHost(CENTER_DATA).get('/power/power_action/all',{controller_id:selectControllerID,type:'son_of_controller'}, function(res){
            if( res.code == 0 ){
                flushTpl(res.data);
            }
        });
    }
    function flushTpl(data){
        var getTpl = action_tpl.innerHTML,view = document.getElementById('action_view');
        laytpl(getTpl).render(data, function(html){
            view.innerHTML = html;
        });
        form.render();
    }


</script>



</body>
</html>