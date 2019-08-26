/*栏目点击链接*/
$(".div_a").click(function(){
    var url = $(this).attr("rel");
    window.location.href = url;
});

/*余额数字效果*/
var decimal_places = 2;
var decimal_factor = decimal_places === 0 ? 1 : decimal_places * 10;
$(function(){
    var total_bag = $('#total_bag').html();
    $('#total_bag').animateNumber({
        number: total_bag * decimal_factor,
        numberStep: function(now, tween) {
            var floored_number = Math.floor(now) / decimal_factor,
                    target = $(tween.elem);
            if (decimal_places > 0) {
                floored_number = floored_number.toFixed(decimal_places);
                /*floored_number = floored_number.toString().replace('.', ',');*/
            }
            target.text(floored_number);
        }
    },1000);
    var total_integral = $('#total_integral').html();
    $('#total_integral').animateNumber({ number: total_integral });
});


/*=============================O2O交易=============================*/
var ajaxTime;
var QRlayer;
$('#btn_qrcode').on('click',function(e){
    // 阻止屏幕拖动行为
    $(document).on('touchmove', function(e) {
        e.stopPropagation();
        e.preventDefault();
    });
    var _html = '<p><img src="/statics/img/loader.gif" /></p><p style="color:#999;font-size:12px;">正在生成二唯码</p>';

    // 弹出层
    QRlayer = layer.open({
        className : 'qrcodebox',
        content : _html,
        end: function(){
            // 取消阻止屏幕拖动行为
            $(document).off('touchmove');
            stopTime();
        }
    });

    // 生成二唯码
    var options = [{"url": $(this).attr('href'), "type":"GET", "dataType":"json"}];
    Load(options, function(json){
        // layer内容
        var userimg = '<img src="'+$('#user_icon_img').attr('src')+'" width="80" height="80" class="qrlogo" />';
        if(json.ms_code=='success'){
            _html = '<div class="code">'+json.br+'</div><div class="code">'+userimg+json.qr+'</div><p class="txt">使用钱包支付 / 充值余额</p>';
            // 开始计时器
            ajaxTime = window.setInterval("sync_post('"+json.code+"')", 3000);
        }else{
            _html = json.ms_msg;
        }
        $('.qrcodebox .layermcont').html(_html);
    },function(){});

    return false;
});
/*ajax发起调用*/
function sync_post(code){
    var options = [{"url": "/ajax/sync.o2o.php", "data":{code:code}, "type":"GET", "dataType":"jsonp"}];
    Load(options, function(data){
        sync_callback(data);
    },function(){});
}
/*ajax回调处理*/
function sync_callback(json){
    if(json.status=='ok'){
        // 返回成功，停止计时器
        stopTime();
        layer.close(QRlayer);
        switch(json.type){
            case 'order':
                //只有会员余额消费才需要输入密码，确认支付
                if(json.pay_method != 2) return;
                var pay_total = json.pay_total;
                var order_id  = json.order_id;
                var bag_total = json.bag_total;
                var _html = '<div class="consume_paid_box"><p style="padding:1em 0;">支付金额：<b>¥'+pay_total+'</b><span style="float:right;margin-right:0.5em;font-size:1em;color:#999;">余额：¥'+bag_total+'</span></p><p class="relative"><i class="iconfont icon-font">&#xe643</i><input type="password" name="business_order_paid_pass" id="business_order_paid_pass" class="consume_paid_pass" placeholder="请输入密码"/><i class="iconfont icon-close" style="display:inline;font-size:20px;right:14px;">&#xe649;</i></p><p style="margin:0.5em 0 0 0;"><button class="btn_consume_paid" >确定支付</button></p></div>';
                var layer_pay_order = layer.open({
                    type: 1,
                    title : "输入密码，确认支付",
                    content: _html,
                    anim: 'up',
                    shadeClose : false,
                    style: 'position:fixed; bottom:0; left:0; width: 100%; height: 200px; padding:10px 0; border:none;background:#fff;color:#000;',
                    success : function() {
                        $('#business_order_paid_pass').focus();
                        $('.icon-close').on('click',function() {
                                $('#business_order_paid_pass').val('').focus();
                        });
                        $('.btn_consume_paid').on('click',function() {
                            var $pass = $('#business_order_paid_pass');
                            var pass  = $pass.val();
                            var _this = this;
                            $(this).attr('disabled','disabled').css('opacity','0.5').text('支付中...');
                            if(isEmpty(pass)) {
                              layer.open({
                                content: '密码不可为空！',
                                skin: 'msg',
                                time: 2
                              });
                              $pass.focus();
                              $(this).removeAttr('disabled').css('opacity','1').text('确定支付');
                              return false;
                            }

                            //支付流程
                            var options = [{"url": "/ajax/business.consume.order.pay.php","data":{order_id:order_id,pass:pass}, "type":"GET", "dataType":"json"}];
                            Load(options, function(json){
                                if(json.status=='success'){
                                    layer.close(layer_pay_order);
                                    layer.open({
                                        content: '订单支付成功！',
                                        skin: 'msg',
                                        time: 1 //2秒后自动关闭
                                    });
                                    setTimeout(function() {
                                        window.location.href="/?m=account&a=order_detail&order_id="+order_id;
                                    },1000);
                                }else{
                                    layer.open({
                                    content: json.msg,
                                    skin: 'msg',
                                    time: 2
                                    });
                                    if(json.status == 'cancel') {
                                        layer.close(layer_pay_order);
                                        return false;
                                    }
                                    $(_this).removeAttr('disabled').css('opacity','1').text('确定支付');
                                }
                            },function(){});

                        });
                    }
                  });
                break;
            case 'prepaid':
                layer.open({
                    content: '充值成功！',
                    skin: 'msg',
                    time: 1 //2秒后自动关闭
                });
                setTimeout(function() {
                    window.location.href="/?m=account&a=balance&c=cz";
                },1000);
                break;
            default:
                // code...
                break;
        }
    }else{
        console.log(json);
    }
}
/*停止计时器*/
function stopTime(){
    window.clearInterval(ajaxTime);
}

function isEmpty(data){ 
  return (data == "" || data == undefined || data == null || data == 0) ? true : false; 
}



