<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:75:"D:\project\v3\view\admin\app\public/../application//user/view/user/bag.html";i:1547373211;s:70:"D:\project\v3\view\admin\app\application\common\view\common\layui.html";i:1547373211;}*/ ?>
<div class="layui-fluid">
	<div class="layui-card">
	  <div class="layui-card-header">
	  	会员：<b>小生有礼</b>
	  	<span class="header-item">基本帐户余额：¥ <b class="base">250.00</b></span>
	  	<span class="header-item">赠送帐户余额：¥ <b class="plus">250.00</b></span>
	  </div>
	  <div class="layui-card-body">
			<form class="layui-form" action="">
			  <div class="layui-form-item form-item-type form-item-type-1">
			    <label class="layui-form-label">业务类型</label>
			    <div class="layui-input-inline layui-label-short">
			      <input type="radio" lay-filter="type" name="type" value="1" title="自动充值赠送" checked>
			    </div>
			    <div class="layui-input-inline layui-form-text">
			    	系统自动计算赠送金额！
			    </div>
			    <div class="layui-input-inline layui-input-short layui-form-input">
			      <input type="text" name="money_1" placeholder="充值金额" autocomplete="off" class="layui-input">
			    </div>
			    <div class="layui-input-inline layui-input-short layui-form-input layui-input-green">
			      <input type="text" placeholder="系统自动赠送金额" autocomplete="off" class="layui-input" disabled>
			    </div>
			    <div class="layui-input-inline layui-input-desc layui-form-input">
			    	= <b>0.00</b>
			    </div>
			  </div>
			  <div class="layui-form-item form-item-type form-item-type-2">
			    <label class="layui-form-label"></label>
			    <div class="layui-input-inline layui-label-short">
			      <input type="radio" lay-filter="type" name="type" value="2" title="充值不赠送" >
			    </div>
			    <div class="layui-input-inline layui-form-text">
			    	系统不会赠送任何金额！
			    </div>
			    <div class="layui-input-inline layui-input-short layui-form-input">
			      <input type="text" name="money_2" placeholder="充值金额" autocomplete="off" class="layui-input">
			    </div>
			  </div>
			  <div class="layui-form-item form-item-type form-item-type-3">
			    <label class="layui-form-label"></label>
			    <div class="layui-input-inline layui-label-short">
			      <input type="radio" lay-filter="type" name="type" value="3" title="只赠送" >
			    </div>
			    <div class="layui-input-inline layui-input-short layui-form-input">
			      <input type="text" name="plus_price" placeholder="赠送金额" autocomplete="off" class="layui-input">
			    </div>
			    <div class="layui-input-inline layui-form-text">
			    	充值进赠送帐户余额！
			    </div>
			  </div>
			  <div class="layui-form-item form-item-type form-item-type-4">
			    <label class="layui-form-label"></label>
			    <div class="layui-input-inline layui-label-short">
			      <input type="radio" lay-filter="type" name="type" value="4" title="退货退款" >
			    </div>
			    <div class="layui-input-inline layui-input-short layui-form-input">
			      <input type="text" name="money_4" placeholder="退款金额" autocomplete="off" class="layui-input">
			    </div>
			    <div class="layui-input-inline layui-input-short layui-form-input">
			      <input type="text" name="pay_sn" placeholder="订单号" autocomplete="off" class="layui-input">
			    </div>
			    <div class="layui-input-inline layui-form-text">
			    	系统会自动计算基本余额与赠送余额的比例！
			    </div>
			  </div>
			  <div class="layui-form-item form-item-type form-item-type-5">
			    <label class="layui-form-label"></label>
			    <div class="layui-input-inline layui-label-short">
			      <input type="radio" lay-filter="type" name="type" value="5" title="扣款" >
			    </div>
			    <div class="layui-input-inline layui-input-short layui-form-input">
			      <input type="text" name="base_price" placeholder="扣除基本余额" autocomplete="off" class="layui-input">
			    </div>
			    <div class="layui-input-inline layui-input-short layui-form-input">
			      <input type="text" name="plus_price" placeholder="扣除赠送余额" autocomplete="off" class="layui-input">
			    </div>
			    <div class="layui-input-inline layui-form-text">
			    	按比例扣除基本余额和赠送余额
			    </div>
			  </div>
			  <div class="layui-form-item layui-form-text">
			    <label class="layui-form-label">流水备注</label>
			    <div class="layui-input-block">
			      <input type="text" name="remark" placeholder="选填" autocomplete="off" class="layui-input">
			    </div>
			  </div>

			  <div class="layui-form-item">
			    <label class="layui-form-label">充值密码</label>
			    <div class="layui-input-block">
			      <input type="password" name="password" required lay-verify="required" placeholder="请输入密码" autocomplete="off" class="layui-input">
			    </div>
			  </div>
			  <div class="layui-form-item">
			    <div class="layui-input-block">
			      <button class="layui-btn" lay-submit lay-filter="formDemo">立即提交</button>
			      <button type="reset" class="layui-btn layui-btn-primary">重置</button>
			    </div>
			  </div>
			</form>
	  </div>
	</div>

</div>

<script>
	// radio初始值
	var type = 1

	function main() {
		form.on('radio(type)', function(data){
			let newType = data.value
			// 重复点击不予理会
			if (type == newType) {
				return false
			}

			type = newType
			// 输入框
			$('.form-item-type .layui-form-input').hide()
			$('.form-item-type-'+ newType +' .layui-form-input').show()

			// 文本
			$('.form-item-type .layui-form-text').show()
			$('.form-item-type-'+ newType +' .layui-form-text').hide()
		});

		form.on('submit', function (data) {
			c(data.field)
			return false;
		})
	}
</script>

<style scoped>
	.layui-fluid {background: #f5f5f5}
	.header-item {margin-left: 10px;}
	.header-item b {font-style: italic;font-size: 15px;}
	.header-item b.base {color: #f60;}
	.layui-label-short {width: 140px !important;height: 38px;line-height: 38px;}
	.layui-input-short layui-form-input {width: 200px !important;height: 38px;line-height: 38px;}
	.layui-input-desc {width: 110px !important;height: 38px;line-height: 38px;}
	.layui-input-green input {color: green !important;}
	.form-item-type:not(.form-item-type-1) .layui-form-input {display: none;}
	.layui-form-text {width: 400px !important;color: #bbb;height: 38px;line-height: 38px;}
	.form-item-type-1 .layui-form-text {display: none}
	.layui-input {font-weight: bold;}
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