<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:81:"D:\project\v3\view\admin\app\public/../application//system/view/express/list.html";i:1551692780;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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

		<form class="layui-form" id="searchForm" lay-filter="searchForm">
			<div class="layui-card-header p-1">
				<button class="layui-btn" lay-submit>搜索</button> 
				<a href="<?php echo url(); ?>" class="layui-btn layui-btn-primary">重置</a>
			</div>
			<div class="pt-1 pb-1">
				<div class="layui-form-item layui-inline">
					<label class="layui-form-label">名称</label>
					<div class="layui-input-inline">
						<input type="text" name="name" placeholder="物流公司名称" class="layui-input" value="<?php echo \think\Request::instance()->param('name'); ?>">
					</div>
				</div>
				<div class="layui-form-item layui-inline">
					<label class="layui-form-label">代码</label>
					<div class="layui-input-inline">
						<input type="text" name="code" placeholder="物流公司代码" class="layui-input" value="<?php echo \think\Request::instance()->param('code'); ?>">
					</div>
				</div>
				<div class="layui-form-item layui-inline">
					<label class="layui-form-label">第三方代码</label>
					<div class="layui-input-inline">
						<input type="text" name="third_code" placeholder="第三方对应物流代码" class="layui-input" value="<?php echo \think\Request::instance()->param('third_code'); ?>">
					</div>
				</div>
				<div class="layui-form-item layui-inline">
					<label class="layui-form-label">状态</label>
					<div class="layui-input-inline">
						<input type="checkbox" name="status" lay-skin="primary" title="禁用" <?php echo \think\Request::instance()->param('status')?'checked' : ''; ?>>
					</div>
				</div>
			</div>
		</form>

		<div class="layui-card-body">
			<table id="dataTable" lay-filter="dataTable"></table>
		</div>
	</div>
</div>


<!-- 操作模板 -->
<script type="text/html" id="toolbarTpl">
	<button class="layui-btn layui-btn-sm" lay-event="add">添加</button>
</script>
<script type="text/html" id="ctrlTpl">
	<button class="layui-btn layui-btn-xs" lay-event="edit">修改</button>
	{{#  if(d.status == 1){ }}
	<button class="layui-btn layui-btn-primary layui-btn-xs" lay-event="disable">禁用</button>
	{{#  } else { }}
	<button class="layui-btn layui-btn-primary layui-btn-xs" lay-event="enable">启用</button>
	{{#  } }} 
</script>

<script type="text/javascript">
const query = <?php echo json_encode(\think\Request::instance()->param()); ?>
</script>
<script type="text/javascript" charset="utf-8" src="/static/js/system/express.js"></script>
</body>
</html>