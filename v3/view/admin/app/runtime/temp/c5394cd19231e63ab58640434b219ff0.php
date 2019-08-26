<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:84:"D:\project\v3\view\admin\app\public/../application//merchant/view/merchant/list.html";i:1556335087;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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



<table id="demo" lay-filter="test"></table>
<script type="text/html" id="toolbar">
  <div class="layui-btn-container">
    <div id="tool-add" class="layui-inline" lay-event="add"><i class="layui-icon layui-icon-add-1"></i></div>
    <div id="tool-del" class="layui-inline" lay-event="delete"><i class="layui-icon layui-icon-delete"></i></div>
  </div>
</script>




<script type="text/javascript">

	console.info("abc");

	var table = layui.table;

	//执行渲染
	table.render({
	  elem: '#demo' //指定原始表格元素选择器（推荐id选择器）
	  ,height: 'auto' //容器高度
	  ,url: '/merchant/merchant/index?is_detail=1'
	  ,page: true
      // ,toolbar: '#barDemo'
      ,toolbar: '#toolbar'
      ,headers: {
      	ctrl: CENTER_DATA
      }
      ,done: function(res, curr, count){
      	// 添加
      	$('#tool-add').on('click', function(){
      		openWin('/merchant/merchant/add_merchant');
      	})
      }
	  ,cols: [[
	  	{field: 'id', title: 'ID', width:80}
	  	,{field: 'name', title: '商户名称', width:180}
	  	,{field: 'account', title: '账号', width:180}
	  	,{field: 'merchant_type', title: '类型', width:80}
	  	,{field: 'status', title: '状态', width:100}
	  	,{field: 'add_time', title: '创建时间', width:180}
	  	,{field: 'city', title: '地区', width:200, templet: function(row){
	  		// 省市区拼接
	  		return row.province + row.city + row.region;
	  	}}
	  	,{field: 'id', title: '操作', width:200, templet: '#ctrlTpl'}
	  ]] //设置表头
	  //,…… //更多参数参考右侧目录：基本参数选项
	});


</script>


<!-- 操作模板 -->
<script type="text/html" id="ctrlTpl">
    <a href="/merchant/merchant/detail.html?id={{d.id}}" class="layui-table-link">详情</a>
</script>


</body>
</html>