<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:82:"D:\project\v3\view\admin\app\public/../application//system/view/delivery/list.html";i:1551692780;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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
				<div class="layui-form-item layui-inline">
					<label class="layui-form-label">所属店铺</label>
					<div class="layui-input-inline">
						<select name="shop_id" id="shopId">
							<option value="">选择店铺</option>
						</select>
					</div>
				</div>
				<div class="layui-form-item layui-inline">
					<label class="layui-form-label">配送标题</label>
					<div class="layui-input-inline">
						<input type="text" name="delivery_name" placeholder="模糊匹配" class="layui-input" value="<?php echo \think\Request::instance()->param('delivery_name'); ?>">
					</div>
				</div>
				<div class="layui-form-item layui-inline">
					<label class="layui-form-label">配送代码</label>
					<div class="layui-input-inline">
						<input type="text" name="delivery_code" placeholder="" class="layui-input" value="<?php echo \think\Request::instance()->param('delivery_code'); ?>">
					</div>
				</div>
			</div>
		</form>

		<div class="layui-card-body">
			<table id="deliveryDataTable" lay-filter="deliveryDataTable"></table>
		</div>
	</div>
</div>

<!-- 操作模板 -->
<script type="text/html" id="ctrlTpl">
	<button class="layui-btn layui-btn-xs" lay-event="edit">修改</button>
	<a class="layui-btn layui-btn-xs" href="/system/delivery_area/list?id={{d.id}}">区域设置</a>
	<button class="layui-btn layui-btn-xs layui-btn-danger" lay-event="del">删除</button>
</script>
<script type="text/html" id="toolbarTpl">
	<button class="layui-btn layui-btn-sm" lay-event="add">添加模板</button>
</script>
<script type="text/javascript">
const query = <?php echo json_encode(\think\Request::instance()->param()); ?>
</script>

<script type="text/javascript" charset="utf-8" src="/static/js/system/delivery.js"></script>
</body>
</html>