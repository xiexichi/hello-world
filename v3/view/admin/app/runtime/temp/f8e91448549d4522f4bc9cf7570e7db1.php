<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:82:"D:\project\v3\view\admin\app\public/../application//article/view/article/list.html";i:1553853809;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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
    .layui-table-body .layui-table-cell{
        height:105px;
        line-height: 105px;
    }
</style>

<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <form class="layui-form" action="" id="form" style="margin:10px">
                <div class="layui-form-item">
                    <label class="layui-form-label">文章名</label>
                    <div class="layui-input-inline">
                        <input type="text" name="title" value="<?=isset($_GET['title']) ? $_GET['title'] : '';?>" placeholder="文章名" autocomplete="off" class="layui-input">
                    </div>
                    
                    <label class="layui-form-label">分类</label>
                    <div class="layui-input-inline">
                        <select name="categorys_id" id="cate_box" lay-search >
                        </select>
                    </div>

                    <div class="layui-input-inline">
                        <button class="layui-btn" lay-submit lay-filter="formList">确认</button>
                        <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <table id="article" lay-filter="article"></table>
        </div>
    </div>
</div>


<!-- 操作模板 -->
<script type="text/html" id="toolbar">
    <div>
        <button class="layui-btn layui-btn-sm" lay-event="add">添加文章</button>
    </div>
</script>

<script type="text/html" id="ctrlTpl" >
    <button class="layui-btn layui-btn-xs" style="margin:0;" lay-event="edit" >编辑</button>
    <button class="layui-btn layui-btn-danger layui-btn-xs" style="margin:0;"  lay-event="del">删除</button>
</script>

<script src="/static/js/article/article/article_common.js"></script>
<script src="/static/js/article/article/article_list.js"></script>



</body>
</html>