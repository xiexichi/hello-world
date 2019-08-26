<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:78:"D:\project\v3\view\admin\app\public/../application//system/view/logs/list.html";i:1548208035;}*/ ?>
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