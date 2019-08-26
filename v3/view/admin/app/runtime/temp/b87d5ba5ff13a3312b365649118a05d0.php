<?php if (!defined('THINK_PATH')) exit(); /*a:5:{s:77:"D:\project\v3\view\admin\app\public/../application//user/view/user/index.html";i:1551679886;s:82:"D:\project\v3\view\admin\app\public/../application//common/view/common/layout.html";i:1551405380;s:45:"../application/common/view/common/header.html";i:1551405380;s:70:"D:\project\v3\view\admin\app\application\common\view\common\layui.html";i:1547373211;s:45:"../application/common/view/common/footer.html";i:1546909578;}*/ ?>
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
		<form class="layui-form searchForm">
			  <div class="layui-card-header" style="padding: 10px 20px">
					<button class="layui-btn" lay-submit>搜索</button> 
					<button type="reset" class="layui-btn layui-btn-primary">重置</button>
					<!-- <button type="button" class="layui-btn layui-btn-normal" onClick="excel();">导出表格</button>  -->
			  </div>
			  <div class="layui-card-body">
			  	<div class="layui-form-item layui-inline">
			  	  <label class="layui-form-label">会员ID</label>
			  	  <div class="layui-input-inline">
			  	    <input type="text" name="id" placeholder="" autocomplete="off" class="layui-input">
			  	  </div>
			  	</div>

			  	 <div class="layui-form-item layui-inline">
			  	    <label class="layui-form-label">会员名称</label>
			  	    <div class="layui-input-inline">
			  	      <input type="text" name="user_name" placeholder="" autocomplete="off" class="layui-input">
			  	    </div>
			  	</div>

			  	  <div class="layui-form-item layui-inline">
			  	    <label class="layui-form-label">会员手机</label>
			  	    <div class="layui-input-inline">
			  	      <input type="text" name="phone" placeholder="" autocomplete="off" class="layui-input">
			  	    </div>
			  	   </div>

			  	  <div class="layui-form-item layui-inline">
			  	    <label class="layui-form-label">注册时间</label>
			  	    <div class="layui-input-inline">
			  	      <input type="text" name="start_date" class="layui-input" id="start_date" autocomplete="off" placeholder="开始日期">
			  	    </div>
			  	    <div class="layui-form-mid">至</div>
			  	    <div class="layui-input-inline">
			  	      <input type="text" name="end_date" class="layui-input" id="end_date" autocomplete="off" placeholder="结束日期">
			  	    </div>
			  	  </div>
			  </div>
		</form>
	</div>
	<div class="layui-card">
		 <div class="layui-card-body">
				<table id="list" lay-filter="list"></table>
		 </div>
	</div>
</div>

<script type="text/html" id="barDemo">
  <a class="layui-btn layui-btn-sm" lay-event="detail">查看</a>
  <a class="layui-btn layui-btn-sm layui-btn-danger" lay-event="bag">钱包</a>
  <a class="layui-btn layui-btn-sm" lay-event="del">二五合作</a>
</script>

<script>
	//所获得的 tableIns 即为当前容器的实例
	var tableIns = undefined
	var tableLoading = undefined
</script>

<script>
	function main() {
		//执行一个laydate实例
		laydate.render({
		  elem: '#start_date' //指定元素
		});

		//执行一个laydate实例
		laydate.render({
		  elem: '#end_date' //指定元素
		});

		// 监听工具
		table.on('toolbar', data => {
			onClickTool(data)
		})
		table.on('tool', data => {
			onClickTool(data)
		})

		onTable();
		onSubmit();
	}

	function onSubmit() {
		form.on('submit', function(data){
			searchData = data.field 

			//上述方法等价于
			table.reload('list', {
			  where: searchData,
			  page: {
			    curr: 1 //重新从第 1 页开始
			  }
			});
		  return false; //阻止表单跳转。如果需要表单跳转，去掉这段即可。
		});
	}

	function onTable() {
		tableLoading = layer.load(2)
		tableIns = table.render({
			id: 'list',
		  elem: '#list',
		  //数据接口
		  url: '/user/user/getList',
		  headers: {ctrl: DC},
		  //开启分页
		  page: true,
		  // limit: 2,
		  toolbar: '#table-toolbar',
		  // defaultToolbar: [],
		  cols: [[ //表头
		  	{field:'id', type: 'checkbox',fixed: 'left', templet: d => {
		  		return 0
		  	}},
		    {field: 'id', title: 'ID', width: 50},
		    {field: 'avatar', title: '头像', width: 80, templet: d => {
		    	return '<img src="'+ d.avatar +'" alt="" width="50">'
		    }},
		    {field: 'user_name', title: '会员名称', width: 130, templet: d => {
		    	let str = d.user_name
		    	str += '<div class="icons">'
		    	str += '<img src="/static/images/seller.gif" title="二五客" style="display:inline-block;margin-right:5px;">'
		    	str += '<img src="/static/images/promote.jpg" title="二五客" style="display:inline-block;margin-right:5px">'
		    	str += '<img src="/static/images/vip.gif" title="二五客" style="display:inline-block;margin-left:5px;">'
		    	str += '</div>'
		    	return str
		    }},
		    {field: 'name', title: '会员等级', width: 130},
		    {field: 'seller_name', title: '分销等级', width: 130},
		    {field: 'phone', title: '手机号码', width: 130},
		    // {field: 'phone', title: '绑定', width: 130},
		    {field: 'create_time', title: '创建时间', width: 150},
		    {field: 'base_total', title: '钱包余额', width: 150, templet: d => {
		    	bag_total =  parseFloat (d.base_total) + parseFloat (d.plus_total)
		    	return '¥ ' + bag_total.toFixed(2)
		    }},
		    {field: 'integral_total', title: '积分余额', width: 150},
		    {field: 'consume_total', title: '消费总额', width: 150},
		    {field: 'remark', title: '备注', width: 150},
		    {fixed: 'right', width:220, align:'center', toolbar: '#barDemo', style: 'vertical-align:center'}
		  ]],
			done: function (res, curr, count){
				if ( ! empty(tableLoading)) {
					layer.close(tableLoading)
				}
				
				currentPage = curr
				if (count <= 0) return false

				// for(let i in res.data) {
				// 	let v = res.data[i]

				// 	if (v.status == -1 || v.status == 2) {
				// 		$('.layui-table-fixed .layui-table-body tr[data-index="'+ i +'"]').find('td:first .laytable-cell-checkbox').empty().append('-');
				// 	}
				// }
			}
		});
	}

	/**
	 * 点击工具栏触发
	 */
	function onClickTool(data) {
		switch(data.event) {
			case 'detail':
				iframes[data.event] = layer.open({
				  type: 2, 
				  title: '用户详情',
				  // shadeClose: true,
				  shade: [.3, '#000'],
				  maxmin: true,
				  area: ['900px', '90%'],
				  //这里content是一个URL，如果你不想让iframe出现滚动条，你还可以content: ['http://sentsin.com', 'no']
				  content: '/user/user/detail?id='+ data.data.id
				})
				break
			case 'bag':
				iframes[data.event] = layer.open({
				  type: 2, 
				  title: '钱包业务',
				  // shadeClose: true,
				  shade: [.3, '#000'],
				  maxmin: true,
				  area: ['900px', '90%'],
				  //这里content是一个URL，如果你不想让iframe出现滚动条，你还可以content: ['http://sentsin.com', 'no']
				  content: '/user/user/bag?id='+ data.data.id
				})
				break
			default:
				if ($.inArray(data.event, ['LAYTABLE_COLS','LAYTABLE_EXPORT','LAYTABLE_PRINT']) >= 0) {
					return false;
				}
				break
		}
	}

</script>

<style scoped>
	.layui-fluid {background: #f5f5f5;}
	.layui-table-cell {height: 50px;line-height: 50px;}
	td[data-field="user_name"] .layui-table-cell {line-height: 25px;}
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