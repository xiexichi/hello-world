<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:78:"D:\project\v3\view\admin\app\public/../application//goods/view/goods/list.html";i:1554084379;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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

<!--导航-->
<style>
    .layui-table-body .layui-table-cell{
        height:105px;
        line-height: 105px;
    }
</style>
<ul class="layui-nav" >
    <li class="layui-nav-item nav_list"><a href="/goods/goods/list.html">全部</a></li>
    <li class="layui-nav-item nav_list"><a href="/goods/goods/list.html?state=1">上架中</a></li>
    <li class="layui-nav-item nav_list"><a href="/goods/goods/list.html?state=0">已下架</a></li>
    <li class="layui-nav-item nav_list"><a href="/goods/goods/list.html?delete=1">已删除</a></li>
</ul>
<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <form class="layui-form" action="" id="form" >
                <input type="hidden" name="state" value="<?=isset($_GET['state']) ? $_GET['state'] : '';?>">
                <input type="hidden" name="delete" value="<?=isset($_GET['delete']) ? $_GET['delete'] : '';?>">
                <div class="layui-form-item">
                    <label class="layui-form-label">关键词</label>
                    <div class="layui-input-inline">
                        <input type="text" name="keyword" value="<?=isset($_GET['keyword']) ? $_GET['keyword'] : '';?>" placeholder="商品名关键词" autocomplete="off" class="layui-input">
                    </div>
                    <label class="layui-form-label">货号</label>
                    <div class="layui-input-inline">
                        <input type="text" name="erp_code" value="<?=isset($_GET['erp_code']) ? $_GET['erp_code'] : '';?>" placeholder="商品货号" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">分类</label>
                    <div class="layui-input-inline">
                        <select name="cate_id" id="cate_box" lay-search >
                        </select>
                    </div>
                    <label class="layui-form-label">品牌</label>
                    <div class="layui-input-inline">
                        <select name="brand_id" id="brand_box" lay-search >
                        </select>
                    </div>
                    <div class="layui-input-inline">
                        <button class="layui-btn" lay-submit lay-filter="formDemo">确认</button>
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
            <table id="goods" lay-filter="test"></table>
        </div>
    </div>
</div>
<!-- 操作模板 -->
<script type="text/html" id="toolbar" >
    <button class="layui-btn layui-btn-sm" lay-event="add" >添加商品</button>
</script>
<script type="text/html" id="ctrlTpl">
    <button class="layui-btn layui-btn-xs" lay-event="sales_detail" >销售管理</button>
    <button class="layui-btn layui-btn-xs" style="margin:0;" lay-event="set_tag" >设置标签</button>
    <button class="layui-btn layui-btn-xs" style="margin:0;{{d.sales_status ? 'display:none;' : ''}}" lay-event="detail"  >详情</button>
    <button class="layui-btn layui-btn-danger layui-btn-xs" style="margin:0;" lay-event="sales" >{{ d.sales_status ? '下架' : '上架' }}</button>
    <button class="layui-btn layui-btn-danger layui-btn-xs" style="margin:0;" lay-event="deleted" >{{ d.is_deleted ? '恢复' : '删除' }}</button>
</script>
<script type="text/javascript" charset="utf-8" src="/static/js/goods/goods.js"></script>

</body>
</html>