<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:88:"D:\project\v3\view\shop\shop-o2o\public/../application//depot/view/shop_adjust/list.html";i:1556004449;s:77:"D:\project\v3\view\shop\shop-o2o\application\depot\view\base\order_where.html";i:1556004449;s:79:"D:\project\v3\view\shop\shop-o2o\application\depot\view\base\collapse_ctrl.html";i:1556004449;}*/ ?>

<!-- 订单查找条件 -->
<div class="layui-collapse" lay-filter="search-filter">
  <div class="layui-colla-item">
    <h2 class="layui-colla-title">查找条件</h2>
    <div class="layui-colla-content layui-show">
    		
    	<form class="layui-form" action="">
		  <div class="layui-form-item fled-flex-start">
		    <div class="layui-input-inline">
		      <button class="layui-btn" lay-submit lay-filter="formDemo">查找</button>
		      <button type="reset" class="layui-btn layui-btn-primary">重置</button>
		    </div>

		    <div class="layui-input-inline">
		    	<p style="color: red;">
		    		<i class="layui-icon layui-icon-tips"></i>
		    		<span>双击行可以编辑商品项</span>
		    	</p>
		    </div>
		  </div>

		  <div class="layui-form-item">
			<div class="layui-inline">
	            <label class="layui-form-label">日期</label>
	            <div class="layui-input-inline" style="width: 150px;">
	              <input id="start-date" type="text" name="start_date" autocomplete="off" placeholder="开始日期" class="layui-input">
	            </div>
	            <div class="layui-form-mid">-</div>
	            <div class="layui-input-inline" style="width: 150px;">
	              <input id="end-date" type="text" name="end_date" autocomplete="off" placeholder="结束日期" class="layui-input">
	            </div>
	          </div>

		    <label class="layui-form-label">单号</label>
		    <div class="layui-input-inline">
		      <input type="text" name="order_sn" placeholder="请输单号" autocomplete="off" class="layui-input">
		    </div>
		  </div>
		  <div class="layui-form-item">
		    <label class="layui-form-label">进货单类型</label>
		    <div class="layui-input-inline" style="width:120px;">
		      <select name="type">
		        <option value=""></option>
		        <option value="1">商品</option>
		        <option value="2">物料</option>
		      </select>
		    </div>

		    <label class="layui-form-label">订单状态</label>
		    <div class="layui-input-inline" style="width:120px;">
		      <select name="status" id="status">
		        <option value=""></option>
		        
		      </select>
		    </div>
			
			<label class="layui-form-label">商品货号</label>
		    <div class="layui-input-inline">
		      <input type="text" name="item_code" placeholder="" autocomplete="off" class="layui-input">
		    </div>
				
			<label class="layui-form-label">商品规格</label>
		    <div class="layui-input-inline">
		      <input type="text" name="sku_code" placeholder="" autocomplete="off" class="layui-input">
		    </div>

		  </div>	

		</form>

    </div>
  </div>
</div>

<!-- 控制器名称 -->
<div style="display: none;">
	<span id="controller_name"><?php echo request()->controller(); ?></span>
</div>


<!--  -->
<script type="text/javascript">
	
	var form = layui.form;

	// 获取控制器名称
	var controllerName = $('#controller_name').html();

	console.info(controllerName);

	// 数据控制器名称
	var cname;

	switch (controllerName) {
		case 'Purchase':
			cname = 'shop_purchase_order';
			break;		

		case 'ShopAdjust':
			cname = 'shop_adjust_order';
			break;		

		case 'ShopDiffer':
			cname = 'shop_differ_order';
			break;
	}

	// 组合获取订单状态url
	let statusUrl = '/depot/' + cname + '/getStatus';

	// 获取
	request.get(statusUrl, function(res){
		if (res.code == 0) {
			for (let i in res.data) {
				$('#status').append('<option value="'+i+'">'+res.data[i]+'</option>');
			}

			form.render();
		}
	});


</script>

<!-- 工具栏 -->
<script type="text/html" id="toolbar">
  <div class="layui-btn-group">
    <button class="layui-btn layui-btn-primary layui-btn-sm" id="add-btn" title="新增进货单">
      <i class="layui-icon">&#xe654;</i>
    </button>
    <button class="layui-btn layui-btn-primary layui-btn-sm" id="edit-btn" title="编辑进货单">
      <i class="layui-icon">&#xe642;</i>
    </button>
    <button class="layui-btn layui-btn-primary layui-btn-sm" id="cancel-btn" title="作废进货单">
      <i class="layui-icon">&#xe640;</i>
    </button>
  </div>
</script> 

<!-- 進貨單表格 -->
<table id="list" lay-filter="list"></table>


<!-- 操作栏 -->
<script type="text/html" id="ctrlTpl">
  <div class="">
    {{#  if(d.status == 0){ }}
      <button class="layui-btn layui-btn-xs edit-order" order_id="{{ d.id }}">编辑商品项</button>
  {{#  } }} 

  {{#  if(d.status != 0){ }}
      <button class="layui-btn layui-btn-disabled layui-btn-xs">编辑商品项</button>
  {{#  } }}
  </div>
</script>

<!-- 引入查找条件操作的js -->

<script src="/static/layui/layui.all.js"></script>
<script type="text/javascript">
	//注意：折叠面板 依赖 element 模块，否则无法进行功能性操作
	var element = layui.element;
		
	// 获取页面的高度
	var wh = $(window).height();

	// 筛选条件容器高度
	var searchHeight = $('.layui-collapse').height();
	// 筛选条件标题的高度
	var titleHeight = $('.layui-colla-title').height();

	// 表格正常高度
	var tableNormalHeight = wh - searchHeight - 30;

	// 面板折叠操作
	element.on('collapse(search-filter)', function(data){
	  console.log(data.show); //得到当前面板的展开状态，true或者false

	  if (!data.show) {
	    let tableViewHeight = wh - titleHeight - 30;
	    $('.layui-table-view').height(tableViewHeight);
	    $('.layui-table-box').height(tableViewHeight - 120);
	    $('.layui-table-main').height(tableViewHeight - 155);

	  } else {
	    let tableViewHeight = wh - searchHeight - 30;
	    $('.layui-table-view').height(tableViewHeight);
	    $('.layui-table-box').height(tableViewHeight - 120);
	    $('.layui-table-main').height(tableViewHeight - 155);
	  }
	});
</script>

<script type="text/javascript">

  // 选中的行
  var selectRow;

  // 时间对象
  var laydate = layui.laydate;

  // 开始时间
    laydate.render({
      elem: '#start-date', //指定元素
    });

    // 结束时间
    laydate.render({
      elem: '#end-date', //指定元素
    });


    // 表单提交
    var form = layui.form;
    form.on('submit(formDemo)', function(data){
    // 重新获取表格数据
    initTable(data.field);
    return false;
  });

    
  // 初始化表格
  initTable();

  // 初始化表格
  function initTable(params){

    var table = layui.table;

    // 链接url
    let url = '/depot/shop_adjust_order/index?';

    // 拼接参数
    if (undefined !== params) {
      for (let i in params) {
        if (params[i] !== '') {
          url += i + '=' + params[i] + '&';
        }
      }     
    }

    //第一个实例
    table.render({
      elem: '#list'
      ,height: tableNormalHeight + 'px'
      ,url: url //数据接口
      ,toolbar: '#toolbar'
      // ,defaultToolbar: ['filter', 'print', 'exports']
      ,page: true //开启分页
      ,headers: {
        ctrl: SHOP_DATA
      }
      ,done: function (res, curr, count) {
        // 表格初始化回调
      tableDoneCallBack();
      }
      ,cols: [[ //表头
        {type: 'radio', fixed: 'left'}
        ,{field: 'order_sn', title: '单号', width:185, sort: true}
        ,{field: 'type_name', title: '调整类型', width:100}
        ,{field: 'status_name', title: '状态', width:80}
        ,{field: 'create_time', title: '提交时间', width:170}
        ,{field: 'confirm_time', title: '调整时间', width:180}
        ,{field: 'apply_quantity', title: '申请数量', width:100}
        ,{field: 'real_quantity', title: '调整数量', width:100}
        ,{field: 'shop_name', title: '制单店铺', width:160}
        ,{field: 'staff_name', title: '制单店员', width:120}
        ,{field: 'shop_remark', title: '店铺备注', width:120} 
        ,{field: 'platform_remark', title: '平台备注', width: 120}
        ,{field: 'id', title: '操作', width: 120, templet: '#ctrlTpl'}
      ]]
    });

    // 选中单选框
    table.on('radio(list)', function(obj){

      if (obj.checked) {
        // 保存选中的行
        selectRow = obj.data;
      } else {
        selectRow = null; 
      }

    });

    //监听行双击事件
    table.on('rowDouble(list)', function(obj){
      // 打开进货单商品列表
      location.href = '/depot/shop_adjust/item_list?adjust_id='+obj.data.id;
    });

  }


  /**
   * [tableDoneCallBack 表格初始化完成后，回调方法]
   * @return {[type]} [description]
   */
  function tableDoneCallBack(){
    // 添加进货单
    $('#add-btn').on('click', function(){
      location.href = '/depot/shop_depot/list?type=8';
    });

    // 作废进货单
    $('#cancel-btn').on('click', function(){
      // 如果有选中行
      if (selectRow) {
        // 弹出确认框
        layer.confirm('确认要作废吗?作废后不能恢复的', function(index){
          layer.close(index);

          // 提交数据
          request.post('/depot/shop_adjust_order/cancel', {id: selectRow.id}, function(res){
            // 判断返回结果
            if (res.code == 0) {
              layer.alert('作废成功', function(index){
                // 关闭弹窗
                layer.close(index);
                // 刷新列表
                initTable();
              });
            } else {
              layer.alert(res.msg);
            }

          });

        }); 
      }
    });

      // 修改订单
      $('.edit-order').on('click', function(){
        let id = $(this).attr('order_id');
        // 打开进货单商品列表
        // openLayer('/depot/purchase/detail?id='+id, '进货单详情', {area: ['80%', '80%']});

        // 打开进货单商品列表
        location.href = '/depot/shop_adjust/item_list?purchase_id='+id;
      })

      // 修改订单
      $('#edit-btn').on('click', function(){
        // 打开进货单商品列表
        location.href = '/depot/shop_adjust/detail?id='+selectRow.id;
      })

  }


</script>