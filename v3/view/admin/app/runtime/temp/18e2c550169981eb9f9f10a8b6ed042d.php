<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:81:"D:\project\v3\view\admin\app\public/../application//apps/view/app_third/list.html";i:1547373211;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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

<div class="layui-fluid" id="app">
	<div class="layui-card">
		<div class="layui-card-body">
			<table id="appsDataTable" lay-filter="appsDataTable"></table>
		</div>
	</div>
</div>

<!-- 操作模板 -->
<script type="text/html" id="toolbar">
	<div class="layui-btn-container">
		<button class="layui-btn layui-btn-sm" lay-event="add">添加应用</button>
	</div>
</script>
<script type="text/html" id="statusTpl">
	<input type="checkbox" name="status" lay-skin="switch" lay-filter="switch-status" lay-text="启用|禁用" data-id="{{d.id}}" data-value="{{d.status}}" data-json="{{ encodeURIComponent(JSON.stringify(d)) }}" {{ d.status == 1 ? 'checked' : '' }}>
</script>
<script type="text/html" id="ctrlTpl">
  <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
  <a class="layui-btn layui-btn-xs layui-btn-danger" lay-event="del">删除</a>
</script>

<script type="text/javascript" charset="utf-8" src="/static/js/apps/app_third.js"></script>
</body>
</html>