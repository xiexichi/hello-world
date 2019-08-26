<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:90:"D:\project\v3\view\admin\app\public/../application//admin/view/admin_access/page_list.html";i:1554270298;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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
    <div class="layui-card">
        <div class="layui-card-body">
            <div>
                <button class="layui-btn layui-btn-sm" id="addMenu">添加菜单</button>
                <button class="layui-btn layui-btn-sm" id="addAccess">添加权限</button>
            </div>
        </div>
    </div>
</div>
<div id="access_view"></div>
<style type="text/css">
    #access_view{padding: 10px;}
    .ctrl_div{padding: 0 20px 20px 20px;background-color: #fff;}
    .ctrl_div .layui-row{background-color: #f2f2f2}
    fieldset{background-color: #fff;}
</style>
<script id="access_tpl" type="text/html">
    {{# layui.each(d, function(index1,item1){  }}
    <fieldset class="layui-elem-field site-demo-button" style="margin-top: 20px;">
        <legend>{{ item1.title }}</legend>
        <div class="ctrl_div">
            <div class="layui-row layui-col-space10">

                {{# layui.each(item1.children, function(index2,item2){ }}
                <div class="layui-col-md6">
                    <div class="layui-card">
                        <div class="layui-card-header">{{ item2.title }}</div>
                        <div class="layui-card-body">
                            {{# layui.each(item2.children, function(index3,item3){ }}
                            <button class="layui-btn layui-btn-sm" style="margin:5px 0;">{{ item3.title }}</button>
                            {{# }) }}
                        </div>
                    </div>
                </div>
                {{# }) }}

            </div>
        </div>
    </fieldset>
    {{# }); }}
</script>

<script>
        var $ = layui.jquery;
        var table = layui.table;
        var layer = layui.layer;
        var form = layui.form;
        var element = layui.element;
        var laytpl = layui.laytpl;
        function loadTpl(){
            // 获取分类信息
            request.setHost(CENTER_DATA).get('/admin/admin_access/getAllList',{}, function(res){
                console.log(res);
                if( res.code == 0 ){
                    var getTpl = access_tpl.innerHTML,view = document.getElementById('access_view');
                    laytpl(getTpl).render(res.data, function(html){
                      view.innerHTML = html;
                    });
                }
            });
        }
    layui.use(['layer', 'table','laytpl'], function(){

        loadTpl();

        //绑定事件
        $(document).on('click', '#addAccess', function(data) {
            layer.open({
                title:'添加权限配置',
                type:2,
                shadeClose: true,
                closeBtn:1,
                area:['60%','60%'],
                content:"/admin/admin_access/page_add_access.html",
                success:function (layero,index){
                    LayuiOpenView = window[layero.find('iframe')[0]['name']];
                }
            });
        }).on('click', '#addMenu', function(data) {
            layer.open({
                title:'添加菜单',
                type:2,
                shadeClose: true,
                closeBtn:1,
                area:['60%','60%'],
                content:"/admin/admin_access/page_add_menu.html",
                success:function (layero,index){
                    LayuiOpenView = window[layero.find('iframe')[0]['name']];
                }
            });
        });
        


    });

    function callback(){
        loadTpl()
        layer.closeAll();
    }
 
</script>

</body>
</html>