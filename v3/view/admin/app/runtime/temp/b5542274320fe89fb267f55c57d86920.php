<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:78:"D:\project\v3\view\admin\app\public/../application//user/view/user/detail.html";i:1547373211;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:70:"D:\project\v3\view\admin\app\application\common\view\common\layui.html";i:1547373211;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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

<div class="layui-fluid">
	<div class="layui-card">
	  <div class="layui-card-header">会员详情</div>
	  <div class="layui-card-body">
			<div class="layui-row user-detail">
		  	<div class="layui-col-md6 layui-col-sm6 layui-col-xs6">
		  		<b>昵称</b><span class="user_name"></span>
				</div>
		  	<div class="layui-col-md6 layui-col-sm6 layui-col-xs6">
		  		<b>状态</b><span class="status"></span>
				</div>
				<div class="layui-col-md6 layui-col-sm6 layui-col-xs6">
		  		<b>手机号码</b><span class="phone"></span>
				</div>
				<div class="layui-col-md6 layui-col-sm6 layui-col-xs6">
		  		<b>会员身份</b><span class="phone"></span>
				</div>
				<div class="layui-col-md6 layui-col-sm6 layui-col-xs6">
		  		<b>注册时间</b><span class="create_time"></span>
				</div>
				<div class="layui-col-md6 layui-col-sm6 layui-col-xs6">
		  		<b>最近登录时间</b><span class="login_time"></span>
				</div>
				<div class="layui-col-md6 layui-col-sm6 layui-col-xs6">
		  		<b>备注</b><span class="remark"></span>
				</div>
				<div class="layui-col-md6 layui-col-sm6 layui-col-xs6">
		  		<b>绑定第三方</b><span></span>
				</div>
			</div>

	  </div>
	</div>

	<div class="layui-card">
	  <div class="layui-card-header">
	  	最近几条流水记录
	  	<a href="javascript:void(0)" class="header-right">更多</a>
	  </div>
	  <div class="layui-card-body">
			<table id="flow-list" lay-filter="flow-list"></table>
	  </div>
	</div>

	<div class="layui-card">
	  <div class="layui-card-header">
	  	最近几条积分记录
	  	<a href="javascript:void(0)" class="header-right">更多</a>
	  </div>
	  <div class="layui-card-body">
			<table id="integral-list" lay-filter="integral-list"></table>
	  </div>
	</div>

</div>

<script type="text/html" id="barDemo">
  <a class="layui-btn layui-btn-sm" lay-event="detail">查看</a>
  <a class="layui-btn layui-btn-sm layui-btn-danger" lay-event="edit">充值</a>
  <a class="layui-btn layui-btn-sm" lay-event="del">二五合作</a>
</script>

<script>
	//所获得的 tableIns 即为当前容器的实例
	// var tableIns = undefined
	// var tableLoading = undefined

	var id = getUrlParam('id')
</script>

<script>
	function main() {
		// 初始化
		init();
	}	

	function init() {
		request.setHost(DC).get('/user/user/one', {id: id},function (json) {

			// 会员资料
			for(let key in json.data) {
				let value = json.data[key]
				switch(key) {
					case 'status':
						$('.'+key).text(value ? '正常' : '封号');
						break
					default:
						$('.'+key).text(value);
						break
				}
			}

			//方法渲染：
			table.render({
				id: 'flow-list',
			  elem: '#flow-list',
				data: json.data.with_flows,
			  cols:  [[ //标题栏
			    {field: 'id', title: '#'},
			    {field: 'money', title: '交易金额'},
			    {field: 'pay_method_cn', title: '付款方式', templet: d => {
			    	return d.with_pay_method.method
			    }},
			    {field: 'type_cn', title: '类型', templet: d => {
			    	return d.with_flow_type.type
			    }},
			    {field: 'status_cn', title: '状态', templet: d => {
			    	return d.status ? '已支付' : '未支付'
			    }},
			    {field: 'pay_time', title: '支付时间', width: 180},
			  ]]
			});

			//方法渲染：
			table.render({
				id: 'integral-list',
			  elem: '#integral-list',
				data: json.data.with_integrals,
			  cols:  [[ //标题栏
			    {field: 'id', title: '#'},
			    {field: 'type', title: '类型', templet: d => {
			    	return d.with_integral_type.name
			    }},
			    {field: 'integral', title: '积分'},
			    {field: 'integral_total', title: '积分余额', templet: d => {
			    	return '<b>'+ d.integral_total +'</b>'
			    }},
			    {field: 'pay_sn', title: '消费单号', width: 200},
			    {field: 'create_time', title: '创建时间', width: 180},
			  ]]
			});
		})
	}
</script>

<style scoped>
	.layui-fluid {background: #f5f5f5}
	.user-detail div {height: 30px;line-height: 30px;}
	.user-detail div b {width: 100px;text-align: right;display: inline-block;margin-right: 25px;}
	.header-right {float:right;}
</style>	

<!-- 包含layui的基本初始化文件，请放在最下面 -->
<!-- 进行初始化 -->

<!-- 路径常量ctrl -->
<script>
  // 数据
  var DC = 'center_data';
</script>

<!-- layui -->
<script>
  // 弹层组件文档
  var layer   = undefined;
  // 图片懒加载
  var flow    = undefined;
  // 回到top
  var util    = undefined;
  // 分页组件
  var laypage = undefined;
  // 日期
  var laydate = undefined;
  // 使用表单组件
  var form    = undefined;
  // 使用表格
  var table   = undefined;
  // 常用元素
  var element = undefined;
  // 搜索数据
  var searchData  = undefined;
  // 当前页数
  var currentPage = 1;
  // iframes窗口
  var iframes     = {};
  // layui组件使用初始化
  var laymods = ['layer','flow','util','laypage','laydate','form','table','element'];

  // 加载模块
  layui.use(laymods, function () {
    layer   = layui.layer,
    flow    = layui.flow,
    util    = layui.util,
    laypage = layui.laypage,
    laydate = layui.laydate,
    form    = layui.form,
    table   = layui.table,
    element = layui.element

    //执行top
    util.fixbar({})
    // 更新渲染
    form.render()
    // 当你执行这样一个方法时，即对页面中的全部带有lay-src的img元素开启了懒加载（当然你也可以指定相关img）
    flow.lazyimg()

    // 执行主函数
    if(typeof main === "function") main()
  })

/* 样例一：表格 */
/*
  function onTable() {
    var tableLoading = layer.load(2)
    tableIns = table.render({
      id: 'table',
      elem: '#table',
      url: '/index/index/getList',
      page: true,
      cols: [[
        {field: 'id', title: '#', width: 50},
      ]],
      parseData: function (res) {
        return {
          "code": res.code, //解析接口状态
          "msg": res.msg, //解析提示文本
          "count": res.data.page.total, //解析数据长度
          "data": res.data.items //解析数据列表
        };
      },
      done: function () {
        layer.close(tableLoading)
      }
    });
  }

*/
</script>
</body>
</html>