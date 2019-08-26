<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:88:"D:\project\v3\view\shop\shop-o2o\public/../application//depot/view/shop_return/list.html";i:1556004449;s:72:"D:\project\v3\view\shop\shop-o2o\application\depot\view\base\search.html";i:1556004449;s:79:"D:\project\v3\view\shop\shop-o2o\application\depot\view\base\collapse_ctrl.html";i:1556004449;}*/ ?>
<!-- 引入查找条件 -->
<div class="layui-collapse" lay-filter="search-filter">
  <div class="layui-colla-item">
    <h2 class="layui-colla-title">
      <strong>查找条件</strong>
    </h2>
    <div class="layui-colla-content layui-show">
    
      <div class="site-text site-block">

        <form class="layui-form" action="">
          <div class="layui-form-item fled-space-between">
            <div class="layui-input-inline">
              <button class="layui-btn" lay-submit lay-filter="formDemo">查找</button>
              <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>

            <div class="layui-form-item" id="save-cloud-div" style="display: none;">
              <label class="layui-form-label">同步云端</label>
              <div class="layui-input-inline" style="width: 70px">
                <input type="checkbox" name="switch" lay-text="开启|关闭" lay-skin="switch" lay-filter="save-cloud">
              </div>
              <div class="layui-form-mid layui-word-aux">
                <span id="pre_select_tag"></span>
              </div>
            </div>
            
          </div>

          <div class="layui-form-item">
  
            <label class="layui-form-label">单号</label>
            <div class="layui-input-inline">
              <input type="text" name="order_sn" placeholder="" autocomplete="off" class="layui-input">
            </div>

            <label class="layui-form-label">SKU</label>
            <div class="layui-input-inline" style="width: 100px;">
              <input type="text" name="sku_sn" placeholder="SKU_SN" autocomplete="off" class="layui-input">
            </div>

            <label class="layui-form-label">规格</label>
            <div class="layui-input-inline">
              <input type="text" name="sku_code" placeholder="支持多个规格同时查找" autocomplete="off" class="layui-input">
            </div>
  
            <label class="layui-form-label" id="depot_type_select_label">库存类型</label>
            <div class="layui-input-inline">
              <select name="type" id="depot_type_select" lay-filter="depot_type_select">
                  <option value="">全部</option>
                  <option value="1">商品</option>
                  <option value="2">物料</option>
              </select>
            </div>

          </div>
          
          <!-- 时间日期 -->
          <div class="layui-form-item">
              <div class="layui-inline">
                <label class="layui-form-label">日期范围</label>
                <div class="layui-input-inline" >
                  <input id="start_date" type="text" name="start_date" placeholder="开始时间" autocomplete="off" class="layui-input">
                </div>
                <div class="layui-form-mid">-</div>
                <div class="layui-input-inline" >
                  <input id="end_date" type="text" name="end_date" placeholder="结束时间" autocomplete="off" class="layui-input">
                </div>
              </div>

              <div class="layui-inline" id="shop_depot_change_div" style="display: none;">
                <label class="layui-form-label">变动类型</label>
                <div class="layui-input-inline">
                  <select name="shop_depot_change_type_id" id="change_types">
                      <option value="">全部</option>
                  </select>
                </div>
              </div>

          </div>
  
          <div class="layui-form-item" id="category_id_div">
            <label class="layui-form-label">分类</label>
            <div class="layui-input-inline">
              <select name="category_ids" xm-select="categorys">
                  <option value="">请选择, 此处是联动多选</option>
              </select>
            </div>


            <label class="layui-form-label">品牌</label>
            <div class="layui-input-inline">
              <select name="brand_ids" xm-select="brands">
                  <option value="">请选择, 此处是联动多选</option>
              </select>
            </div>
          </div>
          
          <!-- <div class="layui-form-item" id="brand_id_div">
            <label class="layui-form-label">品牌</label>
            <div class="layui-input-inline">
              <select name="brand_ids" xm-select="brands">
                  <option value="">请选择, 此处是联动多选</option>
              </select>
            </div>
          </div> -->

          <!-- <div class="layui-form-item">
            <label class="layui-form-label">品牌</label>
            <div class="layui-input-inline">
              <select id="brand_id" name="brand_id">
                <option value=""></option>
              </select>
            </div>
          </div> -->

        </form>

      </div>
    
    </div>

  </div>
</div>

<script type="text/javascript">
  // 默认的开始日期
  var defaultFirstDay = getMonthFirstAndLast().firstDay;
  
  var laydate = layui.laydate;
  
  //执行一个laydate实例
  laydate.render({
    elem: '#start_date', //指定元素
    value: defaultFirstDay
  });  

  laydate.render({
    elem: '#end_date' //指定元素
  });

  /**
   * [setCateAndBrand 设置分类和品牌]
   */
  function setCateAndBrand(){

    // 配置分类选择
    layui.config({
        base: '/static/layui/module/formSelects/'
    }).extend({
        formSelects: 'formSelects-v4'
    });

    layui.use(['form', 'formSelects'], function () {
      // 获取分类数据
      request.get('/goods/category/getCateAll?showType=tree', function(res){
        console.info(res);
        // 分类数据设置
        layui.formSelects.data('categorys', 'local', {
            arr:res.data,
            tree: {
              //在点击节点的时候, 如果没有子级数据, 会触发此事件
              nextClick: function(id, item, callback){
                  //需要在callback中给定一个数组结构, 用来展示子级数据
              },
            }
        });
      });

      // 商品品牌
      request.setHost(SHOP_DATA).get('/goods/goods_brands/all', function(res) {
        for (let i in res.data) {
          res.data[i]['value'] = res.data[i]['id'];
          res.data[i]['name'] = res.data[i]['brand_name'];
        }

        // 分类数据设置
        layui.formSelects.data('brands', 'local', {
            arr:res.data,
            tree: {
              //在点击节点的时候, 如果没有子级数据, 会触发此事件
              nextClick: function(id, item, callback){
                  //需要在callback中给定一个数组结构, 用来展示子级数据
              },
            }
        });
      })

    });
  }

  /**
   * [setShopChangeTypes 设置库存变动类型]
   */
  function setShopChangeTypes(){
    // 显示变动类型div
    $('#shop_depot_change_div').show();

    request.setHost(SHOP_DATA).get('/depot/shop_depot_change/getChangeTypes', function(res) {
      console.info('change_types:',res.data);
      for (let i = 0; i < res.data.length; i++) {
        let item = res.data[i];
        $('#change_types').append('<option value="'+item.id+'">'+item.type+'</option>');
      }
      form.render();
    })
  }

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

<!-- 数据表格 -->
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
    let url = '/depot/shop_return_order/index?';

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
        ,{field: 'type_name', title: '退货类型', width:100}
        ,{field: 'status_name', title: '状态', width:80}
        ,{field: 'create_time', title: '提交时间', width:170}
        ,{field: 'confirm_time', title: '收货时间', width:180}
        ,{field: 'apply_quantity', title: '退货数量', width:100}
        ,{field: 'real_quantity', title: '实发数量', width:100}
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
      location.href = '/depot/shop_return/item_list?return_id='+obj.data.id;
    });

  }


  /**
   * [tableDoneCallBack 表格初始化完成后，回调方法]
   * @return {[type]} [description]
   */
  function tableDoneCallBack(){
    // 添加进货单
    $('#add-btn').on('click', function(){
      location.href = '/depot/shop_depot/list?type=6';
    });

    // 作废进货单
    $('#cancel-btn').on('click', function(){
      // 如果有选中行
      if (selectRow) {
        // 弹出确认框
        layer.confirm('确认要作废吗?作废后不能恢复的', function(index){
          layer.close(index);

          // 提交数据
          request.post('/depot/shop_return_order/cancel', {id: selectRow.id}, function(res){
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
      location.href = '/depot/shop_return/item_list?purchase_id='+id;
    })

    // 修改订单
    $('#edit-btn').on('click', function(){
      // 打开进货单商品列表
      location.href = '/depot/shop_return/detail?id='+selectRow.id;
    })

  }


</script>