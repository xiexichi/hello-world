<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:82:"D:\project\v3\view\shop\shop-o2o\public/../application//order/view/index/list.html";i:1555997870;}*/ ?>
<div class="orders" id="app">
	<div class="layui-card">
		<div class="layui-card-header layuiadmin-card-header-auto layui-form layui-form-item layui-row">
			<div class="layui-form-item pl-2">
				<button class="layui-btn layui-btn-normal">查询</button>
				<button class="layui-btn layui-btn-primary">重置</button>
			</div>
			<div class="layui-inline layui-col-md3 layui-col-sm4 layui-col-xs6">
				<label class="layui-form-label">时间范围</label>
				<div class="layui-input-block">
					<select name="city">
						<option value="0">最近30天</option>
						<option value="1">30天以前</option>
						<option value="2">不限时间</option>
					</select>
				</div>
			</div>
			<div class="layui-inline layui-col-md3 layui-col-sm4 layui-col-xs6">
				<label class="layui-form-label">下单时间</label>
				<div class="flex-row">
					<div class="layui-input-inline">
						<input type="text" name="order_time" id="start_order_time" placeholder="年-月-日" autocomplete="off" class="layui-input">
					</div>
					<div class="layui-form-mid">-</div>
					<div class="layui-input-inline">
						<input type="text" name="order_time" id="end_order_time" placeholder="年-月-日" autocomplete="off" class="layui-input">
					</div>
				</div>
			</div>
			<div class="layui-inline layui-col-md3 layui-col-sm4 layui-col-xs6">
				<label class="layui-form-label">订单类型</label>
				<div class="layui-input-block">
					<select name="city">
						<option value="0">全部</option>
						<option value="1">门店自提</option>
						<option value="1">代发订单</option>
						<option value="2">线上自提</option>
					</select>
				</div>
			</div>
			<div class="layui-inline layui-col-md3 layui-col-sm4 layui-col-xs6">
				<label class="layui-form-label">订单状态</label>
				<div class="layui-input-block">
					<select name="city">
						<option value="0">全部</option>
						<option value="1">未付款</option>
						<option value="1">待发货</option>
						<option value="2">已发货</option>
						<option value="2">交易完成</option>
						<option value="2">已作废</option>
					</select>
				</div>
			</div>
			<div class="layui-inline layui-col-md3 layui-col-sm4 layui-col-xs6">
				<label class="layui-form-label">支付方式</label>
				<div class="layui-input-block">
					<select name="city">
						<option value="0">全部</option>
						<option value="1">25boy钱包</option>
						<option value="1">微信</option>
						<option value="2">支付宝</option>
						<option value="2">现金</option>
						<option value="2">刷卡</option>
					</select>
				</div>
			</div>
			<div class="layui-inline layui-col-md3 layui-col-sm4 layui-col-xs6">
				<label class="layui-form-label">订单编号</label>
				<div class="layui-input-block">
					<input type="text" name="order_time" placeholder="订单编号" autocomplete="off" class="layui-input">
				</div>
			</div>
			<div class="layui-inline layui-col-md3 layui-col-sm4 layui-col-xs6">
				<label class="layui-form-label">会员名称</label>
				<div class="layui-input-block">
					<input type="text" name="order_time" placeholder="会员名称" autocomplete="off" class="layui-input">
				</div>
			</div>
			<div class="layui-inline layui-col-md3 layui-col-sm4 layui-col-xs6">
				<label class="layui-form-label">手机号码</label>
				<div class="layui-input-block">
					<input type="text" name="order_time" placeholder="手机号码" autocomplete="off" class="layui-input">
				</div>
			</div>
			<div class="layui-inline layui-col-md3 layui-col-sm4 layui-col-xs6">
				<label class="layui-form-label">商品编码</label>
				<div class="layui-input-block">
					<input type="text" name="order_time" placeholder="商品编码" autocomplete="off" class="layui-input">
				</div>
			</div>
			<div class="layui-inline layui-col-md3 layui-col-sm4 layui-col-xs6">
				<label class="layui-form-label">商品规格</label>
				<div class="layui-input-block">
					<input type="text" name="order_time" placeholder="商品规格" autocomplete="off" class="layui-input">
				</div>
			</div>
			<div class="layui-inline layui-col-md3 layui-col-sm4 layui-col-xs6">
				<label class="layui-form-label">收货人</label>
				<div class="layui-input-block">
					<input type="text" name="order_time" placeholder="收货人" autocomplete="off" class="layui-input">
				</div>
			</div>
		</div>
		<div class="layui-card-body">
			<table id="myDataTable" lay-filter="myDataTable"></table>
		</div>
	</div>
</div>

<!-- 表格工具栏模板 -->
<script id="toolbarTpl" type="text/html">
	<div class="layui-btn-group">
		<button class="layui-btn layui-btn-primary layui-btn-sm" lay-event="cancel"><i class="layui-icon layui-icon-close"></i>作废</button>
		<button class="layui-btn layui-btn-primary layui-btn-sm" lay-event="pay"><i class="layui-icon layui-icon-rmb"></i>支付</button>
		<button class="layui-btn layui-btn-primary layui-btn-sm" lay-event="shipping"><i class="layui-icon layui-icon-release"></i>发货</button>
		<button class="layui-btn layui-btn-primary layui-btn-sm" lay-event="erp"><i class="layui-icon layui-icon-upload-drag"></i>同步ERP</button>
		<button class="layui-btn layui-btn-primary layui-btn-sm" lay-event="remark"><i class="layui-icon layui-icon-edit"></i>备注</button>
		<button class="layui-btn layui-btn-primary layui-btn-sm" lay-event="print"><i class="layui-icon layui-icon-file"></i>打印小票</button>
	</div>
</script>
<script type="text/html" id="ctrlTpl">
	<button class="layui-btn layui-btn-xs" lay-event="detail">订单详情</button>
</script>


<style type="text/css">
.flex-row{display:flex;}
.layui-form-item .layui-inline{margin-right:0;}
</style>
<!-- checkout -->
<script type="text/javascript" charset="utf-8" src="/static/js/order/index.js"></script>
