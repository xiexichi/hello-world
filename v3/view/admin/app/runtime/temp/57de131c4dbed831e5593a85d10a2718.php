<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:82:"D:\project\v3\view\admin\app\public/../application//system/view/delivery/list.html";i:1547634234;}*/ ?>
<div class="layui-fluid" id="app">
	<div class="layui-card">
		<div class="layui-card-body">
			<table id="deliveryDataTable" lay-filter="deliveryDataTable"></table>
		</div>
	</div>
</div>

<!-- 操作模板 -->
<script type="text/html" id="ctrlTpl">
	<a class="layui-btn layui-btn-xs" href="/system/delivery_area/list?id={{d.id}}">区域设置</a>
	<button class="layui-btn layui-btn-xs" lay-event="edit">修改</button>
	<button class="layui-btn layui-btn-xs layui-btn-danger" lay-event="del">删除</button>
</script>
<script type="text/html" id="toolbarTpl">
	<button class="layui-btn layui-btn-sm" lay-event="add">添加模板</button>
</script>

<script type="text/javascript" charset="utf-8" src="/static/js/system/delivery.js"></script>