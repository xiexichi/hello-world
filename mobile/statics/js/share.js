var upload_btn = $('.addimg');
var save_btn = $('.plus_button .add_btn');
var plus_bg = $('.plus_bg_add');
save_btn.on('click',function(){
	save_share();
});
upload_btn.on('click',function(){
    var upbox = $(this).parents('.up-image-box');
    if(upbox.find('.uped-image-list li').length < 5){
        upbox.find('.upload_input').click();
    }else{
        upbox.find('.alert').show().html('最多上传5张图片哦！');
        return false;
    }
});
plus_bg.css({'height':plus_bg.height(),'line-height':plus_bg.height()+'px'});
plus_bg.on('click',function(){
  upload_btn.click();
});

$('.upload_input').change(function(event){
  plus_bg.html('上传中...').addClass('bgadding');
	var upbox = $(this).parents('.up-image-box'),
		fs = event.target.files,
		pid = upbox.data('id');
    // 判断是否图片
    if(!fs){
        return ;
    }
    var fd = new FormData();
    $.each(fs, function(key, value) {
      fd.append(key, value);
    });
    fd.append('act', 'up');
    upbox.find('.loadinger').show();
    $.ajax({
      url: '/ajax/upfile.php',
      type: 'POST',
      data: fd,
      cache: false,
      dataType: 'json',
      processData: false,
      contentType: false,
      success: function(data, textStatus, jqXHR)
      {
        upbox.find('.upload_input').val('');
        upbox.find('.loadinger').hide();
      	if(data['ms_code'] == 'nologin'){
      		upbox.find('.alert').show().html('你还没有登录，请登录后操作<br><br><a id="back_login" href="javascript:;">点击这里登录 >></a>');
      	}else{
	        $.each(data,function(i,res){
	            if(typeof res.error === undefined || typeof res.error === 'undefined') {
	              	if (res.ms_code == 0){
	          			upbox.find('.alert').show().html(res.ms_msg);
		            }else{
		                upbox.find('.uped-image-list').append('<li title="删除图片" data-id="'+res.file_id+'"><i class="iconfont close" onclick="rmImg(\''+res.file_id+'\','+pid+')">&#xe601;</i><img src="http://img.25miao.com/'+res.dir+'/'+res.filename+'" /></li>');
	        			    countUpImg(upbox);
		            }
		            upbox.find(".loadinger").hide();
	            }
	        });
      	}
      },
      error: function(jqXHR, textStatus, errorThrown)
      {
        upbox.find('.loadinger').hide();
        upbox.find('.alert').show().html('服务器出错，请稍候重试：'+textStatus);
      }
    });
});


function rmImg(file_id,pid){
    var upbox = $('#up-image-box_'+pid);
    upbox.find('.loadinger').show();
    $.ajax({
      url: '/ajax/upfile.php',
      type: 'POST',
      data: {file_id:file_id,'act':'rm'},
      cache: false,
      dataType: 'json',
      success: function(data, textStatus, jqXHR)
      {
      	upbox.find('.loadinger').hide();
        if(typeof data.error === undefined || typeof data.error === 'undefined') {
          if (data.ms_code == 0) {
            upbox.find('.alert').show().html(data.ms_msg);
          }else{
            upbox.find('.uped-image-list').find('li[data-id="'+file_id+'"]').remove();
            countUpImg(upbox);
          }
        }
      },
      error: function(jqXHR, textStatus, errorThrown)
      {
        upbox.find('.loadinger').hide();
      }
    });
}
function countUpImg(upbox){
    var max = 5;
    var shareimg='';
    var pid = upbox.data('id');
    plus_bg.html('<img src="/statics/img/plus_bg_add.png" alt="添加晒图" />');
    upbox.find('.uped-image-list li').each(function(i){
        if(i < max){
           var src = $(this).find('img').attr('src');
           shareimg += ('<input type="hidden" name="simg[]" value="'+src+'" />'); 
        }else{
            rmImg($(this).attr('data-id'),pid);
            $(this).remove();
        }
    });
    upbox.find('.photos-box').html(shareimg);
    upbox.find('.alert').hide();
    var num = upbox.find('.uped-image-list li').length;
    upbox.find('.uptip').html(num+'/'+max);
    if(num > 0){
      $('.plus_bg_add').hide();
      $('.share_hide').show();
    }else{
      $('.share_hide').hide();
      $('.plus_bg_add').show();
    }
}
function save_share(){
	var upbox = $('.up-image-box').last();
	var content = $('#share_content').val();
	var simg = $('.photos-box input').serialize();
	if(simg=='' || simg==undefined){
		upbox.find('.alert').show().html('请添加图片，最多5张！');
		return ;
	}
  if(content==''){
    upbox.find('.alert').show().html('跟大家说点什么吧！');
    $('#share_content').focus();
    return ;
  }
  var fd = new FormData();
    fd.append('content',content);
    fd.append('simg',simg);
	upbox.find('.alert').hide();
	save_btn.val('saveing..');
	save_btn.unbind('click');
	$.ajax({
      url: '/ajax/share.save.php',
      type: 'POST',
      data: fd,
      cache: false,
      dataType: 'json',
      processData: false,
      contentType: false,
      success: function(data, textStatus, jqXHR) {
      	if(data['ms_code'] == 'nologin'){
      		upbox.find('.alert').show().html('你还没有登录，请登录后操作<br><br><a id="back_login" href="javascript:;">点击这里登录 >></a>');
      	}else if(data['ms_code']==0){
      		upbox.find('.alert').show().html(data['ms_msg']);
      		save_btn.val('添加晒图');
      		save_btn.bind('click',function(){
      			save_share();
      		});
      	}else{
          save_btn.addClass('btn_secondary').val('晒图成功');
          // upbox.find('.alert').removeClass('alert-warning').addClass('alert-info').show().html(data['ms_msg']);
         
          // 发放抽奖码
          $.getJSON('/ajax/share.callback.php?id='+data['lastid'],function(res){
            var ms_msg = data['ms_msg'];
            if(res['ms_code'] == 'success'){
              ms_msg = res['ms_msg'];
            }
            shownotice({
                "icon":"success",
                "title":"晒图成功",
                "remark":ms_msg
            }, [{"title":"我知道了","url":"/?m=share&a=view&id="+data['lastid']}],null,'hide');
          });
    		  // window.location.href="/?m=share&a=view&id="+data['lastid'];
      	}
      }
    });
}

