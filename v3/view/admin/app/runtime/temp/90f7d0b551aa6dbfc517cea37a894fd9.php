<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:92:"D:\project\v3\view\admin\app\public/../application//article/view/special_categorys/list.html";i:1553749955;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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


<ul class="layui-nav" lay-filter="">
    <li class="layui-nav-item "><a href="/article/categorys/list.html">文章分类</a></li>
    <li class="layui-nav-item layui-this"><a href="/article/special_categorys/list.html">专题分类</a></li>
</ul>

<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <div>
                <button class="layui-btn layui-btn-sm layui-btn-primary" id="btn-refresh" style="display:none;">刷新表格</button>
                <table id="cateTable" class="layui-table" lay-filter="cateTable"></table>
            </div>
        </div>
    </div>
</div>
<!-- <script type="text/javascript" charset="utf-8" src="/static/js/article/special_categorys.js"></script> -->
<script src="/static/js/article/special_cate/special_cate_list.js"></script>

<!-- 操作模板 -->
<script type="text/html" id="toolbar">
    <div class="layui-btn-container">
        <button class="layui-btn layui-btn-sm" lay-event="add">添加分类</button>
        <button class="layui-btn layui-btn-sm" lay-event="btn-expand" >全部展开</button>
        <button class="layui-btn layui-btn-sm" lay-event="btn-fold">全部折叠</button>
    </div>
</script>

<script type="text/html" id="ctrlTpl" >
    <button class="layui-btn layui-btn-xs" style="margin:0;" lay-event="edit" >编辑</button>
    <button class="layui-btn layui-btn-danger layui-btn-xs" style="margin:0;"  lay-event="del">删除</button>
</script>

</body>
</html>