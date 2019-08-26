<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:87:"D:\project\v3\view\shop\shop-o2o\public/../application//depot/view/shop_depot/list.html";i:1556004449;s:72:"D:\project\v3\view\shop\shop-o2o\application\depot\view\base\search.html";i:1556004449;s:76:"D:\project\v3\view\shop\shop-o2o\application\depot\view\base\pre_select.html";i:1556004449;s:79:"D:\project\v3\view\shop\shop-o2o\application\depot\view\base\collapse_ctrl.html";i:1556004449;}*/ ?>
<style type="text/css">
  .embed-table{
    background: #e6e6e6;
  }
  .embed-table th{
    text-align:center;
  }
</style>


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


<!-- 商品表格 -->
<table id="demo" lay-filter="test">
</table>

<!-- 库存模板 -->
<script type="text/html" id="stockTpl">
  <div class="p-1 text-center embed-table">
    
    <table class="layui-table" lay-skin="line">
    <colgroup>
      <col width="250">
      <col width="300">
      <col width="400">
      <col width="400">
    </colgroup>
    <thead class="text-center">
      <tr>
        <th>规格</th>
        <th class="depot_type_name">店铺库存</th>
        <th>总店库存</th>
        <th class="ctrl_name">进货数量</th>
      </tr> 
    </thead>
    <tbody>

    {{#  layui.each(d.stocks, function(index, item){ }}
      <tr>
        <td>{{ item.sku_code }}</td>
        <td>{{ item.shop_quantity }}</td>
        <td>{{ item.salable_qty }}</td>
        <td>
          <div class="p-0-5">
            <input type="number" class="layui-input purchase_quantity pre-select" min="0" 
            max="{{  isShopStock ? item.shop_quantity : item.salable_qty }}"
             shop_quantity="{{ item.shop_quantity }}" boy_quantity="{{ item.salable_qty }}" stock_id="{{ item.stock_id }}" value="{{ item.pre_select_quantity }}" depot_id="{{ item.depot_id }}" 
            {{#  if(item.salable_qty <= 0){ }}
              disabled="true" placeholder="缺货"
            {{# } }}
            >
          </div>
        </td>
      </tr>
    {{#  }); }}
    {{#  if(d.stocks.length === 0){ }}
      无数据
    {{#  } }} 
      </tbody>
    </table>
  </div>

</script>

<!-- 图片模板 -->
<script type="text/html" id="photoTpl">
  <div>
    <img src="{{ d.image }}">
  </div>
</script>

<!-- 工具栏 -->
<script type="text/html" id="toolbar">

  <div class="layui-form-item">
    
    <div class="layui-inline" title="查看商品" lay-event="" id="see-select">
      <i class="layui-icon layui-icon-cart"></i>
    </div>
      
  </div>
</script>

<!-- <label class="layui-form-label">SKU</label>
<div class="layui-input-inline">
  <select id="sku-select" ></select>
</div> -->

<!-- 分类选择样式 -->
<style type="text/css">
.layui-form-pane .layui-form-label{width:150px;}
.layui-form-pane .layui-input-block{margin-left:150px;}
.selectTable{overflow:auto;}
.selectTable table{margin:0;}
.selectTable .layui-icon-close{position:absolute;top:7px;right:10px;border-radius:50%;border:1px solid #ddd;font-size:14px;width:20px;height:20px;display:block;text-align:center;line-height:20px;cursor:pointer;}
</style>
<link rel="stylesheet" href="/static/layui/module/formSelects/formSelects-v4.css" />

<!-- 引入公共模块js -->
<script type="text/javascript">
// 预选操作

console.info('pre-select');

// 库存预选操作常量
const PURCHASE = 'purchase';
const RETURN = 'return';
const ADJUST = 'adjust';
const DIFFER = 'differ';
const TRANSFER = 'transfer';


// 选择保存到云端的标签
var preSelectTag;

// 库存预选对象
const preSelectCtrl = {}

/**
 * [add 添加方法]
 * @param {[type]} type   [库存预选类型]
 * @param {[type]} $input [input]
 */
preSelectCtrl.add = function (type, $input){
	// 判断是否达到最大值
	let max = $input.attr('max') ? $input.attr('max') : 0;
	// 限制最大值
	if ( parseInt($input.val()) > max) {
		$input.val(max);
	}

	let data = {};

	// 组合保存数据
	data['quantity'] = $input.val();
	data['stock_id'] = $input.attr('stock_id');

	console.info(preSelectTag);

	// 判断是否开启
	if (preSelectTag) {
		console.info('开启云端保存');
		// 店铺预选标记id
		data['shop_depot_pre_select_id'] = preSelectTag['id'];

		request.setHost(SHOP_DATA).post('/depot/shop_depot_pre_select_item/add', data, function(res){
			console.info(res);
		})
	}

	console.info(data);

	// 1.检测本地储存中是否有对应类型的，库存预选数据
	if (localStorage.getItem(type)) {
		// 没有则创建
		data = JSON.parse(localStorage.getItem(type));
	}

	
}


/**
 * [del 删除方法]
 * @param {[type]} id                       [预选项id]
 * @param {[type]} shop_depot_pre_select_id [预选标记id]
 * @param {[type]} callback [回调函数]
 */
preSelectCtrl.del = function (id, shop_depot_pre_select_id, callback){
	let data = {
		ids: id,
		shop_depot_pre_select_id: shop_depot_pre_select_id
	};

	request.setHost(SHOP_DATA).post('/depot/shop_depot_pre_select_item/delete', data, function(res){
		if ( undefined != callback ) {
			callback(res);
		}
	});
}



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

<script>

// 显示保存到云端
$('#save-cloud-div').show();

// 店铺库存类型
var shopDepotType;

// 操作类型
var type = getUrlParam('type');
if (type) {
  // 转正数
  type = parseInt(type);

  // 改变标签文字颜色
  $('#depot_type_select_label').css('color', 'red');

  // 删除库存类型中的全部选项
  $('#depot_type_select').children().first().remove();

  // 设置店铺库存类型
  shopDepotType = $('#depot_type_select').val();
}

// 最大库存限制标记
var isShopStock = false;

// 退货或调整
if (type == 6 || type == 8) {
  // 设置最大限制库存数量标记不是总店库存 
  isShopStock = true;
}

//注意：折叠面板 依赖 element 模块，否则无法进行功能性操作
var element = layui.element;

//Demo
// 预选标签
var preSelectTagLayer;

var form = layui.form;

//监听提交
form.on('submit(formDemo)', function(data){
  // layer.msg(JSON.stringify(data.field));
  // 重新初始化表格
  initTable(data.field);
  return false;
});

// 渲染表单
form.render();

// 监听选择店铺库存类型
form.on('select(depot_type_select)', function(data){
  console.log($(data.elem).html()); //得到select原始DOM对象
  console.log(data.value); //得到被选中的值

  // 设置店铺库存类型
  shopDepotType = data.value;

  // 如果是不是进货单，并且有保存云端的数据
  if (type != 1 && preSelectTag) {

    // 弹出确认窗口
    layer.confirm('确认切换库存类型吗,切换后会清除所有已选择的产品?', function(index){
      // 关闭弹窗
      layer.close(index);
      // 提交数据
      var preSelect = {
        id : preSelectTag.id,
        shop_depot_type: data.value
      };
      // 修改库存类型
      request.setHost(SHOP_DATA).post('/depot/shop_depot_pre_select/edit', preSelect, function(res){
        console.info(res);
        // 重新获取数据
        initTable();
      })
    }, function(index){
      console.info('取消确认');
      // 回退类型
      let backType;

      // 库存类型就2种： 1和2
      if (data.value == 1) {
        backType = 2;
      } else {
        backType = 1;
      }

      console.info("backType : ", backType);

      // 设置回退
      $('#depot_type_select').val(backType);
      shopDepotType = backType;
      // 刷新表单
      form.render();

      // 关闭弹窗
      layer.close(index);
    });

    
  } else {

    // 重新获取数据
    initTable();    
  
  }

});



// 监听开启云端保存选择商品
form.on('switch(save-cloud)', function(data){

  if ($(data.elem).prop('checked')) {

    // 选择页面url
    let url = '/depot/depot_pre_select/list.html?';
    
    switch (type){
      case 6:
        // 退货类型 
        url += "type=2";
        break;
      case 7:
        // 调货（调拨）类型 
        url += "type=4";
        break;
      case 8:
        // 调整类型
        url += "type=3";
        break;
    }

    // 打开弹窗
    //通过这种方式弹出的层，每当它被选择，就会置顶。
    preSelectTagLayer = layer.open({
      type: 2,
      shade: false,
      title: '编辑库存预选标签',
      moveOut: true,
      area: ['60%', '65%'],
      maxmin: true,
      content: url,
      zIndex: layer.zIndex, //重点1
      success: function(layero){
        layer.setTop(layero); //重点2
      }
    });
  } else {
    // 关闭云端储存
    // 将云端储存标记关闭
    preSelectTag = null

    // 清空预选标签
    $('#pre_select_tag').html();

    // 关闭弹窗
    layer.close(preSelectTagLayer);
  }
});


// 配置分类选择
setCateAndBrand();

// 初始化表格
initTable();

// 初始化表格
function initTable(params){

  var table = layui.table;

  // 链接url
  let url = '/depot/shop_depot/index?';

  // 如果有选中的标记
  if (preSelectTag) {
    url += 'shop_depot_pre_select_id='+preSelectTag.id;
  }

  // 如果有库存类型
  if (type) {
    // 获取库存选择类型
    url += '&type=' + $('#depot_type_select').val();
    console.info('type:',$('#depot_type_select').val());
  }

  // 拼接参数
  if (params) {
    for (let i in params) {
      if (params[i]) {
        url += '&' + i + '=' + params[i];
      }      
    }
  } 

  //第一个实例
  table.render({
    elem: '#demo'
    ,height: tableNormalHeight + 'px'
    ,url: url //数据接口
    ,toolbar: '#toolbar'
    // ,toolbar: true
    // ,defaultToolbar: ['filter', 'print', 'exports']
    ,page: true //开启分页
    ,headers: {
      ctrl: SHOP_DATA
    }
    ,done: function (res, curr, count) {
      $('.layui-table-cell').css('height', 'auto');
      $('.embed-table .layui-input').height('25px');
      $('th').css('text-align', 'center');

      // 查看选择商品
      $('#see-select').on('click', function(){
        
        // 跳转页面
        location.href = '/depot/depot_pre_select_item/list.html?shop_depot_pre_select_id='+preSelectTag.id;

      })

      // 颜色
      let colors = ['', 'layui-btn-primary', 'layui-btn-normal', 'layui-btn-warm', 'layui-btn-danger', 'layui-btn-disabled'];

      // 
      for (let i = 0; i < res.data.length; i++) {
        let skuSn = res.data[i].sku_sn;
        let btn = "<a href='#"+skuSn+"' class='layui-btn layui-btn-sm layui-btn-radius "+colors[random(0, colors.length - 1)]+"'>"+skuSn+'-'+res.data[i].color+"</a>";
        let option = "<option>"+btn+"</option>";
        $('#sku-select').append(option);
      }

      // 重新渲染表单
      form.render();

      let ctrlName = '进货数量';

      // 判断是否退货操作
      if (type == 6) {
        // 退货类型
        ctrlName = '退货数量';
      }       

      // 判断是否调货（调拨）操作
      if (type == 7) {
        // 退货类型
        ctrlName = '调货数量';
      }      

      if (type == 8) {
        // 退货类型
        ctrlName = '调整后数量';
      }

      // 设置操作名称
      $('.ctrl_name').html(ctrlName);

      // 判断当前选择的库存类型
      if (shopDepotType == 1) {
        $('.depot_type_name').html('商品库存');
      } else if (shopDepotType == 2) {
        $('.depot_type_name').html('物料库存');
      } else {
        $('.depot_type_name').html('全部库存');
      }

      // 选择进货数量
      selectPurchaseQuantity();
    }
    ,cols: [[ //表头
      {field: 'goods_name', title: '商品', width:180, sort: true}
      ,{field: 'sell_price', title: '单价', width:100}
      ,{field: 'color', title: '颜色', width:80}
      ,{field: 'erp_code', title: 'SKU', width:120, templet: function(row){
        return '<span id="'+row.sku_sn+'">'+row.sku_sn+'</span>';
        // return row.sku_sn;
      }}
      ,{field: 'image', title: '图片', width:120, templet: '#photoTpl'} 
      ,{field: 'stocks', title: '库存', width: 385, templet: '#stockTpl', style: 'height:100px'}
    ]]
  });
}


/**
 * [setPreSelect 设置预选标签]
 */
function setPreSelect(data){
  // 保存数据
  preSelectTag = data;

  // 设置保存标记名称
  $('#pre_select_tag').html('云端标记：' + preSelectTag.tag);

  // 关闭弹窗
  layer.close(preSelectTagLayer);

  // 设置库存类型
  $('#depot_type_select').val(data.shop_depot_type);

  // 重新渲染表单
  form.render();

  // 重新初始化工作
  initTable();
}


/**
 * [selectPurchaseQuantity 选择进货数量]
 * @return {[type]} [description]
 */
function selectPurchaseQuantity(){
  $('.purchase_quantity').bind('input propertychange', function()
  {

    console.info('selectPurchaseQuantity');

    // 默认最大操作数量
    var maxQuantity = parseInt($(this).attr('boy_quantity'));

    // 默认为进货类型
    var selectType = PURCHASE;

    // 判断操作类型
    switch (type) {
      case 6:
        // 退货类型
        selectType = RETURN;
        // 店铺库存为最大操作数量
        var maxQuantity = parseInt($(this).attr('shop_quantity'));
        break;
      case 8:
        // 调整类型
        selectType = ADJUST;
        // 店铺库存为最大操作数量
        var maxQuantity = parseInt($(this).attr('shop_quantity'));
        break;
    }

    console.info('selectType:', selectType);

    // 检测是否超出最大数
    if ( parseInt($(this).val()) > maxQuantity ) {
      // 强制设置最大值
      $(this).val(maxQuantity);
    }

    // 添加
    preSelectCtrl.add(selectType, $(this));
  })
}


</script>