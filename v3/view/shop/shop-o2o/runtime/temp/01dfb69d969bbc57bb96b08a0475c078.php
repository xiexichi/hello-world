<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:86:"D:\project\v3\view\shop\shop-o2o\public/../application//checkout/view/index/index.html";i:1556004449;s:79:"D:\project\v3\view\shop\shop-o2o\application\depot\view\base\collapse_ctrl.html";i:1556004449;}*/ ?>

<style type="text/css">
	
	.user-info{
		padding: 10px;
		text-align: center;
	}
	
	.user-info .title{
		color: #000;
		font-weight: bold;
	}

	.total-price{
		text-indent: 45px;
	}
		
	.pay-method{
		height: 50px;
		text-align: center;
		margin: 10px;
		cursor: pointer;
	}

	.pay-method img{
		height: 30px;
	}

	.goods-info p{
		padding: 10px;
		font-weight: bold;
	}
	
	.fb-labal{
		font-weight: bold;
	}
	
	.loading-user{
		position:absolute;
		width: 100%;
		height: 100%;
		top: 52px;
		background: white;
		text-align: center;
		line-height: 280px;
		display: none;
	}
	
	.search-product{
		height: 60px;
	}

</style>

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

	  <button class="layui-btn layui-btn-primary layui-btn-sm" id="differ-btn" title="申请差异单">
	    <i class="layui-icon">&#xe6b2;</i>
	  </button>
	</div>
</script> 

<!-- <div class="layui-container">   -->
  <div class="layui-row">
    <div class="layui-col-md8">
    	<div>
    		<!-- 進貨單表格 -->
			<table id="list" lay-filter="list"></table>

			<!-- 商品搜索框 -->
			<div class="p-1">				
				<input type="text" name="" class="layui-input search-product">
			</div>
    	</div>
    </div>
    <div class="layui-col-md4" style="padding: 10px;">
        <div class="layui-card" style="border: 1px solid #eee;">
		  <div class="layui-card-header">结算</div>
		  <div class="layui-card-body">
		  	<!-- 会员信息 -->
			<div style="position: relative;">

				<!-- loading层 -->
				<div class="loading-user">
					<i style="font-size: 2rem;" class="layui-icon layui-icon-loading layui-icon layui-anim layui-anim-rotate layui-anim-loop"></i>
				</div>

				<div class="layui-form-item layui-form-text">
				    <label class="layui-form-label" style="padding: 10px 0px 0px 0px;text-align: left;">查找会员：</label>
				    <div class="layui-input-block">
				      <input type="number" name="" class="layui-input search-user" placeholder="会员电话">
				    </div>
				  </div>
				<div class="w-100p text-center">
					<img class="avatar" style="height: 150px;" src="https://img.25miao.com/114/ca2e8436a75448e7767512b31a723c45.jpg">
				</div>
				<div class="fled-start-flex-start">
					
					<div class="user-info">
						<div class="title">会员</div>
						<div>
							<span class="user_name">林书豪</span>
						</div>
					</div>
					<div class="user-info">
						<div class="title">电话</div>
						<div>
							<span class="phone">13888888888</span>
						</div>
					</div>
					<div class="user-info">
						<div class="title">余额</div>
						<div>
							<span class="balance">10000</span>
						</div>
					</div>
					<div class="user-info">
						<div class="title">积分</div>
						<div>
							<span class="integral_total">9999</span>
						</div>
					</div>
				</div>
			</div>			
			
			<!-- 分割线 -->
			<div class="divider mt-1 mb-1"></div>

			<!-- 业务员 -->
			<div>
				<div style="margin-left: 10px">
					<span class="fb-labal">业务员：</span>
					<span>小林子</span>
				</div>
			</div>
			
			<!-- 分割线 -->
			<div class="divider mt-1 mb-1"></div>

			<!-- 商品信息 -->
			<div class="goods-info fled-start-flex-start">
				<p>
					<span>共 <span class="total_num">3</span> 件</span>
				</p>
				<div class="split"></div>
				<p>
					<span>件单价：<span class="average_price">158</span></span>
				</p>
				<div class="split"></div>
				<p class="fled-start-flex-start">
					<span>总价：</span>
					<div style="width: 100px;">
						<input type="number" name="" value="474" class="total_price layui-input">
					</div>
				</p>
			</div>
			
			<!-- 分割线 -->
			<div class="divider mt-1 mb-1"></div>
				
			<div class="fled-start-flex-start">
				<!-- 支付方式 -->
				<div class="pay-method">
					<p class="fled-flex-center"><img src="/static/images/wxpay.png"></p>
					<p>微信</p>
				</div>

				<div class="pay-method">
					<p class="fled-flex-center"><img src="/static/images/alipay.png"></p>
					<p>支付宝</p>
				</div>
				<div class="pay-method">
					<p class="fled-flex-center"><img src="/static/images/cash.png"></p>
					<p>现金</p>
				</div>
				<div class="pay-method">
					<p class="fled-flex-center"><img src="/static/images/phone-swipe.png"></p>
					<p>手机刷卡</p>
				</div>
				<div class="fled-flex-center ml-2 w-20p">
					<button id="consume-btn" class="layui-btn layui-btn-normal mt-1">结算</button>
				</div>
			</div>


		  </div>
		</div>
    </div>
  </div>
<!-- </div> -->




<!-- 操作栏 -->
<script type="text/html" id="ctrlTpl">
  <div class="">
	<i class="layui-icon fs-1-5 del-item" cart_id="{{ d.cart_id }}" goods_item_id="{{ d.goods_item_id }}" style="color: red ;">&#xe640;</i>
  </div>
</script>


<!-- 底部 -->
<div class="checkout-footer">
	<div role="alert" class="el-alert el-alert--warning alert-activitys">
		<div class="el-alert__content">
			<ul class="el-alert__title">
				<li>双12狂欢节，全场8折，2018-12-12 至 2018-12-12</li>
				<li>满2件送HEAB9417，满4件送HEAF9542</li>
			</ul>
		</div>
	</div>
	<div class="checkout-footer-bar">
  <div class="checkout-footer-bartotal">共 2 件商品</div>
  <div class="checkout-footer-baraction">
    <el-switch v-model="no_user" active-color="#13ce66" active-text="非会员订单" class="mr-1"></el-switch>
    <!-- <button class="layui-btn layui-btn layui-btn-xs" @click="setGift">设置赠品</button>
    <button class="layui-btn layui-btn layui-btn-xs" @click="deleteRow">删除</button> -->
  </div>
</div>


<!-- vue -->
<script type="text/javascript" charset="utf-8" src="/static/js/vue.js"></script>
<script type="text/javascript" charset="utf-8" src="/static/js/element-ui.js"></script>
<link rel="stylesheet" href="/static/style/element-ui.css" />
<!-- checkout -->
<script type="text/javascript" charset="utf-8" src="/static/js/checkout/index.js"></script>
<link rel="stylesheet/less" href="/static/style/checkout/index.less" />

<!-- 图片模板 -->
<script type="text/html" id="photoTpl">
  <div>
    <img style="height: 100%;cursor: pointer;" src="{{ d.item_img }}">
  </div>
</script>



<!-- 选择赠品模板 -->
<script type="text/html" id="giftTpl">
  <div class="fled-flex-center">
  	<input class="is_gift" type="checkbox" name="" title="" lay-skin="primary" lay-filter="is_gift" goods_item_id="{{ d.goods_item_id }}" 
	{{#  if(d.is_gift){ }}
	    checked
	{{#  } }} 
  	>
  </div>
</script>

<!-- 邮寄模板 -->
<script type="text/html" id="postTpl">
  <div class="fled-flex-center">
  	<input class="is_post" type="checkbox" name="" title="" lay-skin="primary" lay-filter="is_post" goods_item_id="{{ d.goods_item_id }}" 
	{{#  if(d.is_post){ }}
	    checked
	{{#  } }}
  	>
  </div>
</script>

<!-- 数量模板 -->
<script type="text/html" id="numTpl">
  <div class="fled-flex-center">
    <input style="text-align: center;" stock_id="{{ d.stock_id }}" goods_item_id="{{ d.goods_item_id }}" type="number" name="" value="{{ d.num }}" class="layui-input sale-quantity" max="{{ d.salable_qty }}" min="1">
  </div>
</script>


<!-- 实付价模板 -->
<script type="text/html" id="actuPriceTpl">
  <div class="fled-flex-center">
    <input style="text-align: center;" stock_id="{{ d.stock_id }}" goods_item_id="{{ d.goods_item_id }}" type="number" name="" value="{{ d.actu_price }}" class="layui-input actu_price" max="{{ d.price }}" min="1">
  </div>
</script>




<!-- 结算界面 -->
<div id="consume-panel" style="display: none;">
	<div class="layui-card">
	  <div class="layui-card-body">
		
		<!-- 优惠信息 -->
		<div></div>
		
		<!-- 业务员 -->
		<div></div>
		
		<!-- 支方式 -->
		<div></div>
		
		<!-- 支付金额 -->
		<div></div>
		
		<!-- 确认按钮 -->
		<div>
		  <button class="layui-btn layui-btn-primary">取消</button>
		  <button onclick="createOrder()" class="layui-btn layui-btn-normal ml-5">确认</button>
		</div>

	  </div>
	</div>	
</div>



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
	// 店铺id
	var shopId = "<?php echo $shop_id; ?>";

	// 会员信息
	var user;

	// 输入的电话号码
	var inputPhone;

	// 选择商品
	var selectGoods = {};

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


    // 搜索会员
    $('.search-user').bind("input propertychange",function(event){

    	// 保存输入内容
    	inputPhone = $(this).val();
    })


    // 监听按下键盘事件
	$(document).keydown(function(event){
		// 提交数据
    	let data = {
    		phone: inputPhone
    	}

		// 按下回车键
	　　　if (event.keyCode == 13) {
			// 验证手机号码
			var patrn = /^(0|86|17951)?(13[0-9]|15[012356789]|166|17[3678]|18[0-9]|14[57])[0-9]{8}$/;
		    if(!patrn.test(inputPhone)) 
		    { 
		        layer.alert('请输入正确的手机号码！'); 
		        return false; 
		    }

		    // 显示loading
		    $('.loading-user').show();

		    // 获取会员数据
			request.setHost(CENTER_DATA).post('/user/user/one',data,function
	    		(res){
	    		console.info(res);

	    		// 隐藏loading
		    	$('.loading-user').hide();

	    		// 设置会员信息
	    		if (res.code == 0) {
					// 保存会员信息	    			
	    			user = res.data;

	    			// 余额
	    			let balance = (parseFloat(user.base_total) + parseFloat(user.plus_total)).toFixed(2);
 
	    			// 设置界面会员信息
	    			$('.user_name').html(user.user_name);
	    			$('.integral_total').html(user.integral_total);
	    			$('.balance').html(balance);
	    			$('.avatar').attr('src', user.avatar);

	    			// 获取会员购物车
	    			getUserCart();
	    		} else {
	    			layer.alert('会员不存在');
	    		}
	    	})
		}

	});


	// 点击结算按钮
	$('#consume-btn').on('click', function(){
		// 弹出结算面板
		layer.open({
		  type: 1,
		  shade: 0,
		  title: '结算',
		  content: $('#consume-panel') //这里content是一个DOM，注意：最好该元素要存放在body最外层，否则可能被其它的相对元素所影响
		});
	})


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

	  // 设置选择商品数组
	  let selectGoodsArr = [];
	  for (let i in selectGoods) {
	  	selectGoodsArr[selectGoodsArr.length] = selectGoods[i];
	  }


	  //第一个实例
	  table.render({
	    elem: '#list'
	    ,height: 500
	    ,data: selectGoodsArr //选择的商品数据
	    ,toolbar: '#toolbar'
	    ,headers: {
	      ctrl: SHOP_DATA
	    }
	    ,done: function (res, curr, count) {
	    	// 表格初始化回调
			tableDoneCallBack();

			// 设置tr高度，适应图片
			$('tr').height(70);
	    	$('tr').find('div').height(70).css('line-height', '70px');
	    	$('td').css('vertical-align', 'middle');

	    }
	    ,cols: [[ //表头
	      // {type: 'checkbox', fixed: 'left'}
	      {field: 'item_img', title: '图片', width:90, templet: '#photoTpl'}
	      ,{field: 'item_code', title: '商品编码', width:100}
	      ,{field: 'sku_code', title: '规格', width:60}
	      // ,{field: 'bar_code', title: '条码', width:150}
	      ,{field: 'num', title: '数量', width:70, templet: '#numTpl'}
	      ,{field: 'item_price', title: '单价', width:80}
	      ,{field: 'actu_price', title: '实付价', width:100, templet: '#actuPriceTpl'}
	      ,{field: 'availavle_quantity', title: '店铺库存', width:90}
	      ,{field: 'salable_qty', title: '总店库存', width:90}
	      ,{field: 'is_gift', title: '赠品', width:60, templet: '#giftTpl'}
	      ,{field: 'is_post', title: '邮寄', width:60, templet: '#postTpl'}
	      ,{field: 'id', title: '操作', width: 70, templet: '#ctrlTpl'}
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
		//obj 同上
		// 打开进货单商品列表
		// location.href = '/depot/purchase/item_list?purchase_id='+obj.data.id;
	  });

	}


	/**
	 * [tableDoneCallBack 表格初始化完成后，回调方法]
	 * @return {[type]} [description]
	 */
	function tableDoneCallBack(){

		// 1.设置界面商品信息
       	setGoodsInfo();

		// 添加进货单
		$('#add-btn').on('click', function(){

			// 选择商品
			// alert('选择商品');
			openLayer('/checkout/index/product_list', '选择商品', {'area':['85%','80%']});
		});


		// 修改销售数量
		$(".sale-quantity").bind("input propertychange",function(event){
       		// 最数监听
       		let max = parseInt($(this).attr('max'));

       		// 库存id
       		let stockId = $(this).attr('stock_id');

       		// 商品项id
       		let goodsItemId = $(this).attr('goods_item_id');

       		console.info('stockId:', stockId);

       		// 输入数量
       		let num = parseInt($(this).val());

       		// 最大与最少值限制
       		if (num > max) {
       			$(this).val(max);
       		}

       		if (num < 1) {
       			$(this).val(1);
       		}

       		// 更新选择数量
       		updateSelectGoods(goodsItemId, 'num', $(this).val());
		});

		// 修改实付价
		$('.actu_price').bind("input propertychange",function(event){
			// 商品项id
       		let goodsItemId = $(this).attr('goods_item_id');
       		// 实付价
			let actuPrice = $(this).val();
			// 更新实付金额
       		updateSelectGoods(goodsItemId, 'actu_price', actuPrice);
		})


		// 删除商品项
		$('.del-item').on('click', function(){
			let goodsItemId = $(this).attr('goods_item_id');

			// 1.更新商品项数据
	       	delete(selectGoods[goodsItemId]);

	       	// 提交参数
	       	let params = {
	       		user_id: user.id,
	       		item_id: goodsItemId,
	       		num: 0,
	       		channel: 4
	       	};

	       	// 删除购物车商品
	       	request.setHost(SHOP_DATA).post('/cart/cart/updateCart', params, function(res){
	       		console.info(res);
	       		if (res.code == 0) {
	       			// 2.设置界面商品信息
			       	setGoodsInfo();
			       	// 3.刷新商品表格
			       	initTable();
	       		} else {
	       			// 错误提示
	       			layer.alert(res.msg);
	       		}
	       	})
	       	
		})


	    // 隐藏左侧菜单栏
	    $('.layui-table-tool-self').hide();

	    // 设置标题剧中并
	    $('.layui-table-cell').css('text-align','center');

	    // 选择赠品
	    form.on('checkbox(is_gift)', function(data){
		  // 商品项id
	      let goodsItemId = $(data.elem).attr('goods_item_id')
	      // 更新赠品状态
	      updateSelectGoods(goodsItemId, 'is_gift', data.elem.checked);

	      // 刷新表格
       	  initTable();
		}); 	    

		// 选择邮寄
	    form.on('checkbox(is_post)', function(data){
		  // 商品项id
	      let goodsItemId = $(data.elem).attr('goods_item_id')
	      // 更新赠品状态
	      updateSelectGoods(goodsItemId, 'is_post', data.elem.checked);
		}); 

	}

	/**
	 * [getUserCart 获取会员购物车数据]
	 * @return {[type]} [description]
	 */
	function getUserCart(){
		if (!user) {
			return;
		}

		// 请求参数
		let params = {
			user_id: user.id,
			channel: 2
		};

		// 获会员购物车数据
		request.setHost(SHOP_DATA).post('/cart/cart/getCartList', params, function(res){
			
			console.info('getCartList:', res);

			if (res.code == 0) {
				// 整理页面显示数据

				// 商品列表
				let goodsList = res.data[0].goods_list;

				for (let i = 0; i < goodsList.length; i++) {
					// console.info(goodsList[i]);	

					let item = goodsList[i];

					let itemInfo = item.item_info;

					let erpCodeArr = itemInfo.erp_code.split(',');

					// 店铺库存
					let availavleQuantity = item.o2o_data.availavle_quantity;
					// 总店库存
					let salableQty = item.o2o_data.head_shop_quantity;

					// 是否邮寄
					let isPost = false;

					// 判断是否邮寄
					if (availavleQuantity < item.num && salableQty > item.num) {
						isPost = true;
					}

					// 保存到选择商品项
					selectGoods[itemInfo.id] = {
					   cart_id: item.cart_id,
					   item_img: itemInfo.item_img,
					   item_code: erpCodeArr[0],
					   sku_code: erpCodeArr[1],
					   goods_item_id: itemInfo.id,
					   num: item.num,
					   item_price: itemInfo.item_price,
					   actu_price: itemInfo.item_price,
					   before_gift_actu_price: itemInfo.item_price, // 设置为赠品前的金额
					   availavle_quantity: availavleQuantity,	// 店铺库存
					   salable_qty: salableQty,	// 总店库存
					   is_gift: item.has_gift,
					   is_post: isPost
					};

				}

				// console.info('selectGoods:', selectGoods);

				// 刷新页面
				initTable();
			}	

		});

	}


	/**
	 * [addCart 添加购物车]
	 * @param {[type]} data [description]
	 */
	function addCart(data){

		// 请求参数
		let params = {
			stock_id: data.stock_id,
			type: 1		// 固定值为1
		};

		// 在这位置获取商品信息
		request.setHost(SHOP_DATA).get('/depot/shop_depot/getStockInfo', params, function(res){
			// console.info(res);
			if (res.code == 0) {
				// 添加选择商品
				let item = res.data;
				for (let i in data) {
					item[i] = data[i];
				}

				console.info('addCart:', item);

				// 将商品单价设置为实付单价
				item['actu_price'] = item.item_price;
				// 设置赠送前金额
				item['before_gift_actu_price'] = item['actu_price'];

				// 设置到选择商品对象中
				selectGoods[item.goods_item_id] = item;

				console.info('selectGoods:', selectGoods);

				// 刷新表格
				initTable();
			}
		})

		// 保存购物车数据
		saveCart(data);
	}


	/**
	 * [saveCart 保存购物车数据]
	 * @return {[type]} [description]
	 */
	function saveCart(data){

		// 如果有会员信息，则上传到云端
		if (user) {
			console.info('则上传到云端');
			// 组合数据
			let cartItem = {
				user_id: user.id,
				item_id: data.goods_item_id,
				num: data.num,
				buy_now: 1
			};

			// 添加到货物车中
			request.setHost(SHOP_DATA).post('/cart/cart/addCart', cartItem, function(res){
				console.info('添加购物车结果：',res);
			})
		}
	}


	/**
	 * [updateSelectGoods 更新选择商品]
	 * @param  {[type]} goodsItemId [商品项id]
	 * @param  {[type]} key         []
	 * @param  {[type]} value       [description]
	 * @return {[type]}             [description]
	 */
	function updateSelectGoods(goodsItemId, key, value) {
		// 1.更新商品项数据
       	selectGoods[goodsItemId][key] = value;

       	console.info('updateSelectGoods:', selectGoods[goodsItemId]);

       	// 如果 key=is_gift 并且 value=true
       	if (key == 'is_gift' && value) {
       		// 因为是赠品，设置实付金额为0
       		selectGoods[goodsItemId]['actu_price'] = 0;
       	} else {
       		// 还原实付金额
       		selectGoods[goodsItemId]['actu_price'] = selectGoods[goodsItemId]['before_gift_actu_price'];
       	}

       	// 如果是实付金额
       	if (key == 'actu_price') {
       		// 记录赠送前金额
       		selectGoods[goodsItemId]['before_gift_actu_price'] = value;
       	}


		// 修改商品数量才更新购物车数据
       	if (key == 'num') {
			// 保存购物车数据
       		saveCart(selectGoods[goodsItemId]);
       	}

       	// 2.设置界面商品信息
       	setGoodsInfo();
	}

	/**
	 * [setGoodsInfo 设置商品信息]
	 */
	function setGoodsInfo(){
		let totalNum = 0;	// 总件数
       	let totalPrice = 0;	// 总金额

       	console.info(selectGoods);

       	// 2.从新计算商品数量、支付金额
       	for (let i in selectGoods) {
       		let num =  parseInt(selectGoods[i]['num']);
       		let actuPrice =  parseFloat(selectGoods[i]['actu_price']);

       		console.info(actuPrice);

       		// 总金额
       		totalPrice += actuPrice * num;
       		// 总件数
       		totalNum += num;
       	}

       	console.info('totalPrice:', totalPrice);
       	console.info('totalNum:', totalNum);

       	// 总件数
       	$('.total_num').html(totalNum);

       	// 总金额
       	$('.total_price').val(totalPrice);

       	// 设置件单价
       	if (totalPrice == 0 || totalNum == 0) {
			//件单价
       		$('.average_price').html(0);
       	} else {
       		//件单价
       		$('.average_price').html((totalPrice / totalNum).toFixed(2));
       	}
       	
	}


	/**
	 * [createOrder 创建订单]
	 * @return {[type]} [description]
	 */
	function createOrder(){
		
		if (!user) {
			layer.alert('缺失会员信息');
			return;
		}
		
		// 提交参数
		let params = {
			user_id: user.id,
			// address_id: ,	// 收货地址id： 如果是店铺自提，则填写店铺id，如果订单商品有邮寄的，则填写用户收货地址id
			// shop_id: shopId,	// 下单店铺id
			//shop_delivery：{},	// 店铺个性选择的物流方式 数组 array('商家id'=>"物流方式id",'商家id'=>"物流方式id")
			channel: 4,	// 值为4，代表下单聚渠道为O2O
			self_take_shop: [shopId], // 选择自提的商家订单
			remark: '下单备注'
			// coupon_list: []	// 下单使用优惠券 使用方式以店为单位 array('商家id'=>array('用户优惠券id'，‘用户优惠券id’))
		};

		console.info(params);
	}


</script>

