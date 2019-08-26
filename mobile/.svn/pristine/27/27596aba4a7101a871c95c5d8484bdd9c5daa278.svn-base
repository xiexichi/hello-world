// 请求url
var url = "/ajax/share.comment.php";
// 回复评论id
var share_comment_id;

// 加载数据页面（默认从2开始）
var page = 2;

var zaning = false;

$(function(){

   	// 隐藏底部菜单栏
	$('#menu').hide();

	try{
		// 点击input
		$('#comment-input').on('focus',function(){
			showCommentArea();
		})		
	}catch(err){
		alert(err);
	}

	// 隐藏评论输入框
	$('#cancle').on('click',function(){
		hideCommentArea();
	})

	// 提交数据
	$('#sure').on('click',function(){
		var comment = $('#comment-area').val();
		// 1.数据检查
		if(comment == ''){
			// alert('请求填写评论内容');
			 //提示
			tips('请求填写评论内容');
			return;
		}
		// 提交数据
		var data = {};
		data.a = 'add'; //执行方法
		data.share_id = getQueryString('id');
		data.comment = comment;
		// 回复评论id
		if(share_comment_id){
			data.share_comment_id = share_comment_id;
		}

		// 显示loading
		//$('#comment-loading').show();

		//loading带文字
	    layer.open({
	        type: 2
	        ,content: '评论中'
	    });


		// 提交数据
		$.post(url,data,function(res){
			res = eval('('+res+')');
			if(res.ms_code){
				// 关闭提示框
				layer.closeAll();
				// alert(res.ms_msg)
				// layerConfirm()
				if(res.ms_code == 'nologin'){
					// 登陆提示
					loginTips();
				}else{
					//提示
					tips(res.ms_msg);
				}

			}else{
				hideCommentArea();
				// 情况评论
				$('#comment-area').val("");

				// 评论数据
				var commentData = res.data;

				if(share_comment_id){
					// 回复评论
					var commentAuthor = $($('#clone-div').find('.reply-author')[0]).clone(true);
					commentAuthor.attr('share_comment_id',commentData.id);
					commentAuthor.find('.name').html(commentData.nickname+':');
					commentAuthor.find('.reply-comment').html(commentData.comment);
					commentAuthor.find('.reply-time').html(commentData.create_time);

					// 插入评论列表
					var $comments = $($('.comment-list')[0]).find('.comment');

					for(var i=0;i<$comments.length;i++){
						if($($comments[i]).attr('share_comment_id') == commentData.share_comment_id){
							//如果comment没有回复
							if($($comments[i]).find('.replys').length == 0){
								// 创建一个replys
								var $replys = $('<div class="replys"></div>');
								// 插入回复
								$replys.append(commentAuthor);
								$($comments[i]).append($replys);
							}else{
								// 插入回复
								$($comments[i]).find('.replys').append(commentAuthor);
							}
							break;
						}
					}

					// 清空share_comment_id
					share_comment_id = null;

				}else{
					// 评论晒图
					// 克隆评论item
					var commentItem = $($('#clone-div').find('.comment-item')[0]).clone(true);
					commentItem.find('.avatar').attr('src',commentData.image_url);
					commentItem.find('.name').html(commentData.nickname);
					commentItem.find('.zan').attr('share_comment_id',commentData.id);
					commentItem.find('.comment').attr({share_comment_id:commentData.id});
					commentItem.find('.comment').find('p').html(commentData.comment);
					commentItem.find('.time-div').find('.time').html(commentData.create_time);
					commentItem.find('.time-div').find('.reply').attr({uname:commentData.nickname,share_comment_id:commentData.id});

					// 添加评论
					$('.comment-list').append(commentItem);
				}

				// 评论总数+1
				$('.scc').html(parseInt($($('.scc')[0]).html())+1);

				// 关闭提示框
				layer.closeAll();
				// alert('评论成功')
			}

			// 隐藏loading
			$('#comment-loading').hide();

		}).error(function(){
			// 服务器错误
			tips('服务器忙~请稍后重试');
			// 隐藏loading
			$('#comment-loading').hide();
		})
	})



	try{
		// 回复
		$('.reply').on('click',function(){
			// 保存回复评论id
			share_comment_id = $(this).attr('share_comment_id');
			// 添加placeholder
			$('#comment-area').attr('placeholder','@'+$(this).attr('uname'));

			showCommentArea();
		})		
	}catch(err){
		tips(err);
	}


	/**
	 * [点赞 只能赞一次]
	 */
	// var zaning = false
	$('.zan').on('click',function(){
		// 判断是否在点赞中
		// if(zaning){
		// 	return;
		// }
		// zaning = true;

		var $zan = $(this).next('span');
		var share_comment_id = $(this).attr('share_comment_id');
		var data = {a:'zan',share_id:getQueryString('id'),share_comment_id:share_comment_id};
		$.post(url,data,function(res){
			res = eval('('+res+')')
			if(res.ms_code){
				// 重置可赞状态
				// zaning = false;
				if(res.ms_code == 'nologin'){
					// 登陆提示
					loginTips();
				}else{
					tips(res.ms_msg);
				}
			}else{
				$zan.html(parseInt($zan.html())+1);
			}
		});
	})

	// 滚动加载
	var isLoadingMore = false;
	$(window).on('scroll',function(){
	    var t = document.documentElement.scrollTop || document.body.scrollTop;  //离上方的距离
	    var h =window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight; //可见宽度
	    if( t >= document.documentElement.scrollHeight -h ) {

	    	// 判断是否在加载中
	    	if(isLoadingMore)return;
	    	isLoadingMore = true;
	    	$('#loading-more').show();

	    	// 提交数据
	    	var data = {
	    		a:"get_more",
	    		page:page,
	    		share_id:getQueryString('id'),
	    		order:$('#comment-order').attr('order')
	    	}

	        // 到底了
	        $.post(url,data,function(res){
	        	res = eval('('+res+')')
	        	// console.info(res)
	        	if(res.ms_code == '' && res.data.length > 0){

	        		// 解除加载
	        		isLoadingMore = false;
	        		$('#loading-more').hide();

	        		// 处理数据
	        		handleMore(res.data);

	        		// 页码+1
	        		page++;
	        	}else{
	        		//alert(res.ms_msg)
	        		// 到底了（不允许继续加载）
	        		isLoadingMore = true;
	        		$('#loading-more').hide();
	        	}
	        })
	    }
	})

	// 排序
	$('#comment-order').on('click',function(){
		var order = $(this).attr('order')
		// 1等于热度，2=时间，倒叙
		if(order == 1){
			order = 2;
		}else{
			order = 1;
		}
		// 页面转跳
		location.href = "/?m=share&a=view&id="+getQueryString('id')+"&order="+order;
	})


})



/**
 * [showCommentArea 显示评论区域]
 * @return {[type]} [description]
 */
function showCommentArea(){
	// 隐藏input框
	$('.input-row').hide();
	// 显示评论框
	$('.textarea-row').show();
	// 获取焦点
	$('#comment-area').focus();

}

/**
 * [hideCommentArea 隐藏评论区域]
 * @return {[type]} [description]
 */
function hideCommentArea(){
	// 显示父级
	$('.input-row').show();
	// 隐藏评论框
	$('.textarea-row').hide();

	// 清除placeholder
	$('#comment-area').attr('placeholder','');
}

/**
 * [getQueryString 获取url参数]
 * @param  {[type]} name [description]
 * @return {[type]}      [description]
 */
function getQueryString(name){
	var urlArr = document.URL.split('?');
	if (urlArr.length < 2) {
	  	return null;
	}
	var params = urlArr[1].split('&');
	if (params.length < 1) {
	  	return null;
	}
	for (var i = 0; i < params.length; i++) {
		  var paramArr = params[i].split('=');
		  if (paramArr[0] === name) {
		    return paramArr[1];
		  }
	}
}


//提示
function tips(tips){
  layer.open({
    content: tips
    ,skin: 'msg'
    ,time: 2 //2秒后自动关闭
  });
}


/**
 * [loginTips 登陆提示]
 * @return {[type]} [description]
 */
function loginTips(){
	layer.open({
        content: '请登陆后再操作！'
        ,btn: ['马上登录']
        ,shadeClose:false
        ,yes: function(){
            if(iswx){
                var b = new Base64();
                var gourl = b.encode(window.location.href);
                window.location.href='/?m=login&a=weixin.bind&gourl='+gourl;
            }else{
                layer.closeAll();
                user.action('by',false);
                user.createpannel();
            }
            return false;
        }
    });
	// 登陆提示
	// layer.open({
	//     content: '请登陆后再操作！'
	//     ,btn: ['登陆', '取消']
	//     ,yes: function(index){
	//         // 跳到登陆页面
	//     	location.href = '/?m=account';
	//     }
	// });
}


/**
 * [handleMore 处理更多数据]
 * @param  {[type]} data [description]
 * @return {[type]}      [description]
 */
function handleMore(data){
	console.info("handleMore "+data.length)
	for(var i=0;i<data.length;i++){
		// 评论数据
		var commentData = data[i];

		// 克隆评论item
		var commentItem = $($('#clone-div').find('.comment-item')[0]).clone(true);
		commentItem.find('.avatar').attr('src',commentData.image_url);
		commentItem.find('.name').html(commentData.nickname);
		commentItem.find('.zan').attr('share_comment_id',commentData.id);
		commentItem.find('.comment').attr({share_comment_id:commentData.id});
		commentItem.find('.comment').find('p').html(commentData.comment);
		commentItem.find('.time-div').find('.time').html(commentData.create_time);
		commentItem.find('.time-div').find('.reply').attr({uname:commentData.nickname,share_comment_id:commentData.id});

		// 如果有评论回复
		if(commentData.replys.length > 0){
			// 创建回复div
			var replys = $('<div class="replys"></div>');
			for(var j=0;j<commentData.replys.length;j++){
				var replyData = commentData.replys[j];
				// 回复评论
				var commentAuthor = $($('#clone-div').find('.reply-author')[0]).clone(true);
				commentAuthor.attr('share_comment_id',replyData.id);
				commentAuthor.find('.name').html(replyData.nickname);
				commentAuthor.find('.reply-comment').html(replyData.comment);
				commentAuthor.find('.reply-time').html(replyData.create_time);

				// 添加到回复div
				replys.append(commentAuthor);
			}
			// 添加回复到评论中
			commentItem.find('.comment').append(replys);
		}
		
		// 添加评论
		$('.comment-list').append(commentItem);

	}
}