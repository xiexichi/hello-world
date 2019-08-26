$(function(){
    get_ship_fee(); // 取得运费,这里为影响'确认并付款'

  $('input[name=delivery]').change(function() {
    $('#delivery_id').val($(this).val());
    if($('input[name=delivery]:checked').size() == 1 && $('input[name=delivery]:checked').data('there') == 0) {
      get_ship_fee();
    }else {
      var coupon_id = $('#user_coupon_list li.selected').data('val');
      var coupon = 0;
      if(coupon_id > 0){
        coupon = $('#voucher').text();
      }
      var total_price = Number(price.cart_total) - Number(price.event_total)- Number(price.prize_total) - Number(coupon);
      $('#shiji').data('total',total_price);
      $('#shiji').html(Number(total_price).toFixed(2));
      $('#ship_fee').html($(this).data('name'));
    }

  });

    $(".order_belance .checkbox").click(function(){
        //余额：1  余额+：2
        var balance = 1;
        if($(this).attr('ismerge') == "1") {
            balance = 2;
        }
        if($(this).attr("enabledcheck")=="1"){
            if($(".order_belance .checkbox").hasClass("checkbox_checked")){
                //取消余额支付
                $(".order_belance .checkbox").removeClass("checkbox_checked")
                $("form#payform input#balance").val(0)
            }else{
                //余额支付
                if($(".order_belance .checkbox").attr("authentication")==1){
                    $(".order_belance .checkbox").addClass("checkbox_checked")
                    $("form#payform input#balance").val(balance)
                }else{
                    user.show("reenterpassword",function(){
                        $(".order_belance .checkbox").addClass("checkbox_checked")
                        $("form#payform input#balance").val(balance);
                        // console.log("_callback")
                        $(".order_belance .checkbox").attr("authentication",1);
                    });
                }
            }
        }
    });

    $("#btn_add_address").click(function(){
        user.show("address_add",function(json){
            update_address(json)
        });
    });

    user.by_btn("#address_review","address_list",false,null);
    user.by_btn("#coupon_review","coupon_list",false,null);
});

function update_address(json){
    $("#default_address_id").val(json.address_id)
    var html = '<div class="title">收货地址<span>></span></div>'
    html += '<div class="row"><strong>'+json.receiver_name+'</strong></div>'
    html += '<div class="row">'+json.receiver_phone+'</div>'
    html += '<div class="row">'+json.state_name+' '+json.city_name+' '+json.district_name+' '+json.address+'</div>'
    $("#address_review").html(html)
    user.by_btn("#address_review .title","address_list",false,null);
    if($('input[name=delivery]:checked').size() == 1 && $('input[name=delivery]:checked').data('there') == 0) {
      get_ship_fee();
    }
}

function check_order(){
    $("#maskdiv").remove()
    var use_balance = $("form#payform input#balance").val()
    if(use_balance == "2") {
        messagetitle = "正在使用余额+支付";
    }else if(use_balance == "1"){
        messagetitle = "正在使用余额支付";
    }else{
        if(iswx){
            messagetitle = "正在跳转到微信支付";
        }else{
            messagetitle = "正在跳转到支付宝付款";
        }
    }
    $("body").append('<div class="realcenter"  id="maskdiv" style="text-align: center"><img src="/statics/img/loader.gif" /><br/>正在处理订单，请稍候...</div>')

    var address_id=$("#default_address_id").val(),
        cart_id=$("#cart_id").val(),
        balance=$("#balance").val(),
        // coupon=$('#coupon_use').data('val'),
        coupon=$('#user_coupon_list ul li.selected').data('val'),
        item_event = $("select.item_event").serialize(),
        errorcode="";
        buyer_note = $("#buyer_note").val();
        delivery_id = $("#delivery_id").val();
    if(address_id==0||address_id==""){
        errorcode += "没有选择收货地址；<br/>"
    }

    if(errorcode!=""){
        $("#maskdiv").remove()
        shownotice({
            "icon":"notice",
            "title":"订单提交有误，错误如下",
            "remark":errorcode
        },[])
        return;
    }
    var postdata = {
        "isCombomeal":true,
        "combomeal_id":combomealId,
        "address_id":address_id,
        "balance":balance,
        "buyer_note":buyer_note,
        "delivery_id":delivery_id,
        "ccp":ccp
    };


    // 打印提交数据
    // console.info(postdata);
    // return;
 
    var options = [{ "url": "/ajax/order.add.php", "data":postdata, "type":"POST", "dataType":"json"}]
    Load(options, function(json){
        if(json.status=="success"){
            $('#maskdiv').html('<img src="/statics/img/loader.gif" /><br/>'+messagetitle+'，请稍候...');
            //余额支付
            if(json.balance==1){
                $("#maskdiv").remove()
                shownotice({
                    "icon":"success",
                    "title":"购买成功",
                    "remark":"恭喜您，您已经成功支付。订单号："+json.order_sn+" ;我们将尽快为您发货配送！系统将在3秒后返回订单列表。<br>感谢您的支持与信赖！"
                },[],function(){
                    window.setTimeout(function(){
                        window.location.href = "/?m=account&a=order"
                    },3000)
                });
            }else if(json.balance==2){
                //余额+支付
                $("#maskdiv").remove()
                shownotice({
                    "icon":"success",
                    "title":"创建订单成功",
                    "remark":"请稍候，正在跳转付款...."
                },[],function(){
                    window.setTimeout(function(){
                        if(iswx){
                            window.location.href="/wxpay_charge.php?sn="+json.order_sn+"&salt="+json.salt;
                        }else{
                            window.location.href="/alipay_charge.php?sn="+json.order_sn+"&salt="+json.salt;
                        }
                    },500)
                },'hide');
            }else{
                //第三方支付
                if(iswx){
                    window.location.href="/pay.php?method=weixin&sn="+json.order_sn+"&salt="+json.salt;
                }else{
                    window.location.href="/pay.php?method=alipay&sn="+json.order_sn+"&salt="+json.salt;
                }
            }

        }else{
            var errorsummary = "",
                errortitle = '未能生成订单，错误如下';
            $("#maskdiv").remove()
            switch(json.status){
                case "no_cart_id":
                    errorsummary = "订单中没有产品，或购物车选定的产品被删除";
                    break;
                case "no_address_id":
                    errorsummary = "没有填写收货信息";
                    break;
                case "no_address_area":
                    errorsummary = "请完善收货地址：省-市-区县-详细地址";
                    break;
                case "no_login":
                    errorsummary = "用户没有登录，或用户数据过期，请重新绑定登录";
                    break;
                case "paramerror":
                    errorsummary = "订单数据有误，请重新选择产品";
                    break;
                case "no_balance":
                    errorsummary = "账户余额不足，请先充值，或取消余额支付采用微信支付。";
                    break;
                case "no_phone":
                    errorsummary = "联系电话不正确，如果是固定电话，必须形如(xxxx-xxxxxxxx)";
                    break;
                case "prizes_limit":
                    errortitle = '领取礼品数量超出限制';
                    errorsummary = "购物车符合领取条件的数量超出了限制，如需要请分成两张订单拍下，您不必担心运费，绑定微信号是免运费的。";
                    break;
                case "seller_not_buy":
                    errortitle = "分销不能购买套餐商品";
                    break;
                case "error":
                    errortitle = "订单数据有误，请重新选择产品";
                    break;
            }
            shownotice({
                "icon":"notice",
                "title":errortitle,
                "remark":errorsummary
            },[])
        }
    },function(){})
}

function get_ship_fee(){
  $('.btn_buynow').unbind('click');
  $('.btn_buynow').css('background-color','#777777');
  if(price.event_total > 0){
    $('.youhui_box').show();
  }
  var sub_total = (Number(price.cart_total)-Number(price.event_total)-Number(price.prize_total));
  var product_ids = new Array();
  var address_id = $('#default_address_id').val();
  var total_balance = $('#total_balance').html();
  $('#product_review').find('.productitembox').each(function(){
    product_ids.push($(this).data('goods'));
  })

  // 如果没有收货地址，则获取
  if (!address_id) {
    address_id = parseInt($('span .address_id').html())
    $('#default_address_id').val(address_id)
  }

  var coupon_id = $('#user_coupon_list li.selected').data('val');
  var coupon = 0;
  if(coupon_id > 0){
    coupon = $('#voucher').text();
  }

  $.get('/ajax/get.ship.fee.php',{product_ids:product_ids.join(),address_id:address_id,price:sub_total},function(fee) {
    price.ship_fee = fee;
    $('#shiji').html((Number(sub_total)+Number(fee)-Number(coupon)).toFixed(2))
    $('input[name=delivery]:disabled').removeAttr('disabled');
    var pay_total = (Number(sub_total)+Number(fee)-Number(coupon)).toFixed(2);
    if(fee < 0) {
      $('#ship_fee').html('--');
      return;
    }
    if(fee=="0"){
        $('#ship_fee').html('免运费');
    }else{
        $('#ship_fee').html('<sup>￥</sup>'+fee);
    }
    // 余额不足-合并支付
    if(Number(total_balance) > 0 && Number(total_balance) < pay_total) {
        $('#user_balance').attr('ismerge',1);
        $('.order_belance .balancebox').html('使用余额：¥<span id="total_balance">'+total_balance+'</span>');
    }
    //余额为零
    if(Number(total_balance) == 0){
        $('#user_balance').attr('enabledcheck',"0");
        $('#not_balance').show();
    }else{
        $('#user_balance').attr('enabledcheck',"1");
        $('#not_balance').hide();
        $('.order_belance').slideDown();
        // $('.pagemain').append('<span class="blank20"></span><span class="blank20"></span>');
    }
    $('.btn_buynow').css('background-color','#da3335');
    var consume_line = $('#consume_line').data('val');
    if(consume_line != -1 && Number(pay_total) < consume_line) {
        $('.btn_buynow').css('background-color','rgb(119, 119, 119)');
    }

    // 确认支付
    $('.btn_buynow').bind('click',function(){

        if(consume_line != -1 && Number(pay_total) < consume_line) {
            layer.open({content:'温馨提示：您的最低消费为：¥'+consume_line});
            return false;
        }
       check_order();   //确认并付款
    });
  })
}

function use_coupon(coupon_id){
    // $('#coupon_use').data('val',coupon_id);
    //$('#coupon_use').attr('data-val',coupon_id);
    var obj = coupon_json[coupon_id],
        sub_total = Number(price.cart_total)-Number(price.event_total)-Number(price.prize_total);

    var postdata = {
        "coupon_id":coupon_id,
        "cart_ids":cart_ids,
        "sub_total":price.cart_total-price.event_total
    };

    //配送方式
    var is_there = ($('input[name=delivery]:checked').data('there') == undefined) ? 0 :$('input[name=delivery]:checked').data('there');

    $('#user_coupon_list .loader').show();
    var options = [{ "url": "/ajax/order.coupon.php", "data":postdata, "type":"POST", "dataType":"json"}]
    Load(options, function(data){
        /*$('#user_coupon_list .loader').hide();
        user.exit();
        $('#coupon_use').html(obj['coupon_title']);*/
        $('#voucher').html(data['cut_price']);
        if (data['cut_price'] > 0) {
            $('.youhui_box, .voucher_div').show();
            $('#user_coupon_list ul li').removeClass('selected');
            $('#user_coupon_list #coupon_item_'+coupon_id).addClass('selected');
        }else{
            $('#user_coupon_list ul li').removeClass('selected');
            $('#user_coupon_list #coupon_item_'+coupon_id).addClass('unavailable');
        }
        var cp_price = sub_total-Number(data['cut_price'])+Number(price.ship_fee);
        if(is_there) cp_price -= Number(price.ship_fee);
        $('#shiji').html(cp_price.toFixed(2));
    });

}

var  price  = {
  cart_total : $('#sub_total').data('val'),
  ship_fee : 0,
  event_total : $('#youhui').data('val'),
  prize_total : $('#prizes').data('val'),
  change_price : function() {

      var total_price = Number(this.cart_total) + Number(this.ship_fee) - Number(this.event_total)- Number(this.prize_total);
      $('#shiji').data('total',total_price);
      var coupon_id = $('#user_coupon_list li.selected').data('val');
      if(coupon_id > 0){
        use_coupon(coupon_id);
      }else{
        $('#shiji').html(Number(total_price).toFixed(2)); 
      }
  }
};