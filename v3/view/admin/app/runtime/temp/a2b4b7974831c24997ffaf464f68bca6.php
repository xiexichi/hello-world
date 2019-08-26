<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:80:"D:\project\v3\view\admin\app\public/../application//picshow/view/index/list.html";i:1551405380;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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
			<div class="layui-card-header layuiadmin-card-header-auto">
				<div>
					<div class="layui-form-item layui-inline">
						<label class="layui-form-label">广告位</label>
						<div class="layui-input-inline">
							<select name="position_id" id="positionId">
								<option value="">选择广告位</option>
							</select>
						</div>
					</div>
					<div class="layui-form-item layui-inline">
						<label class="layui-form-label">内容模块</label>
						<div class="layui-input-inline">
							<select name="module_id" id="moduleId">
								<option value="">选择模块</option>
							</select>
						</div>
					</div>
				</div>

				<div class="layui-form-item layui-inline">
					<label class="layui-form-label">广告标题</label>
					<div class="layui-input-inline">
						<input type="text" name="title" placeholder="模糊匹配" class="layui-input" value="<?php echo \think\Request::instance()->param('title'); ?>">
					</div>
				</div>
				<div class="layui-form-item layui-inline">
					<label class="layui-form-label">有效时间</label>
					<div class="layui-input-inline">
						<input type="text" name="start_time" class="layui-input" id="start_time" autocomplete="off" placeholder="开始日期" value="<?php echo \think\Request::instance()->param('start_time'); ?>">
					</div>
					<div class="layui-form-mid">至</div>
					<div class="layui-input-inline">
						<input type="text" name="end_time" class="layui-input" id="end_time" autocomplete="off" placeholder="结束日期" value="<?php echo \think\Request::instance()->param('end_time'); ?>">
					</div>
				</div>
			</div>
		</form>

		<div class="layui-card-body">
			<table id="myDataTable" lay-filter="myDataTable"></table>
		</div>
	</div>
</div>

<!-- 操作模板 -->
<script type="text/html" id="ctrlTpl">
	<button class="layui-btn layui-btn-xs" lay-event="edit">修改</button>
	<button class="layui-btn layui-btn-xs layui-btn-danger" lay-event="del">删除</button>
</script>
<script type="text/html" id="toolbarTpl">
	<button class="layui-btn layui-btn-sm" lay-event="add">添加广告</button>
</script>
<script type="text/html" id="statusTpl">
	<input type="checkbox" name="status" lay-skin="switch" lay-filter="switch-status" lay-text="启用|禁用" data-id="{{d.id}}" data-value="{{d.status}}" data-json="{{ encodeURIComponent(JSON.stringify(d)) }}" {{ d.status == 1 ? 'checked' : '' }}>
</script>
<script type="text/javascript">
const query = <?php echo json_encode(\think\Request::instance()->param()); ?>
</script>

<style type="text/css">
.layui-table-cell{max-height:100%;height:100%;}
.laytable-cell-1-0-5{line-height:20px;}
</style>
<script type="text/javascript" charset="utf-8" src="/static/js/picshow/index.js"></script>
</body>
</html>