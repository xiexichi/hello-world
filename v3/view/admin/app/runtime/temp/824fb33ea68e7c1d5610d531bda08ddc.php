<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:78:"D:\project\v3\view\admin\app\public/../application//system/view/logs/list.html";i:1548208035;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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

		<form class="layui-form" id="logsForm" lay-filter="logsForm">
			<div class="layui-card-header p-1">
				<button class="layui-btn" lay-submit>搜索</button> 
				<a href="<?php echo url(); ?>" class="layui-btn layui-btn-primary">重置</a>
			</div>
			<div class="layui-card-header layuiadmin-card-header-auto">
				<div class="layui-form-item layui-inline">
					<label class="layui-form-label">模块</label>
					<div class="layui-input-inline">
						<input type="text" name="module" placeholder="可选" class="layui-input" value="<?php echo \think\Request::instance()->param('module'); ?>">
					</div>
				</div>
				<div class="layui-form-item layui-inline">
					<label class="layui-form-label">控制器</label>
					<div class="layui-input-inline">
						<input type="text" name="controller" placeholder="可选" class="layui-input" value="<?php echo \think\Request::instance()->param('controller'); ?>">
					</div>
				</div>
				<div class="layui-form-item layui-inline">
					<label class="layui-form-label">方法</label>
					<div class="layui-input-inline">
						<input type="text" name="action" placeholder="可选" class="layui-input" value="<?php echo \think\Request::instance()->param('action'); ?>">
					</div>
				</div>
				<div class="layui-form-item layui-inline">
					<label class="layui-form-label">操作人</label>
					<div class="layui-input-inline">
						<select name="admin_id">
							<option value="">选择操作人</option>
							<option value="1">张三</option>
							<option value="2">李四</option>
						</select>
					</div>
				</div>
				<div class="layui-form-item layui-inline">
					<label class="layui-form-label">操作时间</label>
					<div class="layui-input-inline">
						<input type="text" name="start_date" class="layui-input" id="start_date" autocomplete="off" placeholder="开始日期" value="<?php echo \think\Request::instance()->param('start_date'); ?>">
					</div>
					<div class="layui-form-mid">至</div>
					<div class="layui-input-inline">
						<input type="text" name="end_date" class="layui-input" id="end_date" autocomplete="off" placeholder="结束日期" value="<?php echo \think\Request::instance()->param('end_date'); ?>">
					</div>
				</div>
			</div>
		</form>

		<div class="layui-card-body">
			<table id="logsDataTable" lay-filter="logsDataTable"></table>
		</div>
	</div>
</div>


<!-- 操作模板 -->
<script type="text/html" id="ctrlTpl">
	{{#  if(d.is_edit){ }}
	<button class="layui-btn layui-btn-primary layui-btn-xs" lay-event="detail">查看修改内容</button>
	{{#  } }} 
</script>

<script type="text/javascript">
const query = <?php echo json_encode(\think\Request::instance()->param()); ?>
</script>
<script type="text/javascript" charset="utf-8" src="/static/js/system/logs.js"></script>
</body>
</html>