
$(function(){

	// 加操作
	$('.add').on('click', function(event){
		// 阻止事件冒泡
		event.stopPropagation();

		var $input = $(this).prev('input');
		var max = $input.attr('max');
		if (max == $input.val()) {
			return;
		}
		// 当前数量
		var quantity = parseInt($input.val()) + 1;
		// 加1
		$input.val(quantity);
	});

	// 减
	$('.reduce').on('click', function(event){
		// 阻止事件冒泡
		event.stopPropagation();

		var $input = $(this).next('input');
		if (1 == $input.val()) {
			return;
		}
		// 当前数量
		var quantity = parseInt($input.val()) - 1;
		// 减1
		$input.val(quantity);
	});

	// 输入框点击事件
	$('.input-min').on('click',function(event){
		// 阻止事件冒泡
		event.stopPropagation();
	});


	// 选择理由
	$('.reason').on('click', function(){

		if ($(this).attr('reason') == '1') {
			// 如果是质量问题
			$('#problem-div-reason').show();
		} else {
			// 不是质量问题
			$('#problem-div-reason').hide();
		}

		// 清除所有选中的
		$('.checkbox').removeClass('checkbox_checked');

		// 设置当前选中的
		$(this).find('.checkbox').addClass('checkbox_checked');
	});

	// 点击商品项
	$('.itemsummary').on('click',function(){
		// 设置当前选中的
		$(this).find('.product-checkbox').toggleClass('product-checkbox-checked');
	});


	// 上传图片
	$('#add-img').on('click',function(){
		// 限制上传图片数量最多3张
		if ($('.upload-files').find('input[type="file"]').length >= 3) {
			layerTips('最多上传3张图片');
			return;
		}

		// 复制文件域
		var fileInput = $('#clone-div').find('input[type="file"]').clone(true);

		// 获取上传图片文件域的id
		var number = $('.upload-files').attr('number');

		// 文件名称
		var filename = 'file'+number;

		// 添加文件域id属性， file为前缀
		fileInput.attr({'id':filename, 'name': filename});

		// 文件域+1
		$('.upload-files').attr('number', parseInt(number) + 1 );

		// 添加到删除图片中
		$('.upload-files').append(fileInput)

		return fileInput.click();
	});


	// 选择图片事件
	$('.images').on('change', function(){
		
		// 文件域id
		var fileId = $(this).attr('id');

		// 显示问题图片div
		var problemDiv = $($('#clone-div').find('.problem-div')[0]).clone(true);

		// 显示问题图片
		problemDiv.find('.problem-pic').attr({fileid:fileId,'src' : getFileUrl(this)});

		// 删除按钮添加文件域id
		problemDiv.find('.del-problem-pic').attr('fileid', fileId);

		// 添加问题图片
		$('.imgs-div').append(problemDiv);
	});

	// 删除图片
	$('.del-problem-pic').on('click', function(){
		// 删除对应的文件域
		var fileid = $(this).attr('fileid');
		$('.upload-files').find('#'+fileid).remove();
		// 删除图片
		$(this).parent().remove();
	});


	// 提交申请
	$('#sure-apply').on('click', function(){
		// 表单数据检测
		if (!checkFormData()) {
			return;
		}

		// 退货理由
		var reason = $('.checkbox_checked').attr('reason');

		// 获取申请数据
		if (reason == 1 ) {
			// 直接获取上传文件表单，初始化
			var form = new FormData($('.upload-files')[0]);
			// 添加问题描述
			form.append('note', $('#note').val());
		} else {
			var form = new FormData();
		}	

		// 添加理由
		form.append('reason', reason);

		// 订单id
		form.append('order_id', $(this).attr('order_id'));

		var requantitys = {};
		var requantityInputs = $('.input-min');
		for (var i = 0; i < requantityInputs.length; i++) {
			var itemid = $(requantityInputs[i]).attr('itemid');
			var quality = $(requantityInputs[i]).val();
			// 如果有选中
			if($('#product-checkbox-'+itemid).hasClass('product-checkbox-checked')){
				requantitys[itemid] = quality;
			}
		}

		// 添加退货数量
		form.append('requantitys', JSON.stringify(requantitys));

		// 显示loading
		showLoading();

		// 'content-type': 'multipart/form-data; charset=UTF-8'
		// 异步提交数据
		axios.post('/ajax/o2o/order.refundApply.php', form, {
	      headers: {
	        'content-type': 'multipart/form-data; charset=UTF-8'
	      }
	    }).then(function(res) {

	    	// 关闭loading
	    	closeLoading();

	    	if (res.status == 200) {
	    		if (res.data.code == 0) {
	    			// 成功
					layerInfo('申请退货成功，请等待审核结果', function(){
						// 跳转到订单列表
						window.location.href = "/?m=account&a=o2o_order";
					});
					
	    		} else {
	    			// 失败
	    			if (res,data.code >= 1007) {
	    				layerTips('申请错误，请联系客服');
	    			} else {
	    				layerTips(res.data.msg);
	    			}
	    		}
	    	} else {
	    		layerTips('服务器正忙！请稍后重试~');
	    	}
	    })

	})

	// 取消申请
	$('#cancle-apply').on('click', function(){
		// 显示loading
		showLoading();
		// 回到上一个页面
		window.history.go(-1);
	})

})


/**
 * [getFormData 获取表单数据]
 * @return {[type]} [description]
 */
function checkFormData(){
	// 退货理由
	var reason = $('.checkbox_checked').attr('reason');

	var requantitys = [];
	var requantityInputs = $('.input-min');
	for (var i = 0; i < requantityInputs.length; i++) {
		var itemid = $(requantityInputs[i]).attr('itemid');
		var quality = $(requantityInputs[i]).val();

		// 如果有选中
		if($('#product-checkbox-'+itemid).hasClass('product-checkbox-checked')){
			requantitys.push(itemid);
		}

		if (quality == '' || quality < 1) {
			tips('退货数量不能为空');

			$(requantityInputs[i]).focus();

			// 数量报错
			return false;
		}
	}

	// 验证是否有选择退货商品
	if(requantitys.length == 0){
		tips('请选择退货商品');
		return false;
	}


	// 质量问题
	if (reason == 1) {
		if ($('#note').val() == ''){
			$('#note').focus();
			tips('请填写留言/说明');
			// 备注为空
			return false;
		}

		var files = $($('.upload-files')[0]).find('input[type="file"]');
		if (files.length < 1) {
			// 质量问题图片报错
			tips('请上传商品问题图片');
			return false;
		}
	}

	return true;
}


/** 
* 从 file 域获取 本地图片 url 
*/ 
function getFileUrl(obj) { 
  var url; 
  if (navigator.userAgent.indexOf("MSIE")>=1) { // IE 
    url = obj.value; 
  } else if(navigator.userAgent.indexOf("Firefox")>0) { // Firefox 
    url = window.URL.createObjectURL(obj.files.item(0)); 
  } else if(navigator.userAgent.indexOf("Chrome")>0) { // Chrome 
    url = window.URL.createObjectURL(obj.files.item(0)); 
  } else {
  	url = window.URL.createObjectURL(obj.files.item(0));
  }
  return url; 
}