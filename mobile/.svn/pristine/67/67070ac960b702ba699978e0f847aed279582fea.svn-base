$(function() {
    init();
    if($.trim($('.order_detail_bottom').html())==''){
        $('.order_detail_bottom').hide();
    }
});
var re_pay_url = "";
function init(){
    $(".btn_paynow").click(function(){
        var data = {
                order_sn : $(this).data('sn')
            };
        show_pop($(this).attr("order_id"),"repay",data);
    });
    $(".btn_cancle").click(function(){
        show_pop($(this).attr("order_id"),"cancel_order");
    });
    $(".btn_tuikuan").click(function(){
        show_pop($(this).attr("order_id"),"tuikuan_order");
    });
    $(".btn_checkwuliu").click(function(){
        show_pop($(this).attr("order_id"),"get_wuliu");
    });
    $(".btnconfirm").click(function(){
        show_pop($(this).attr("order_id"),"confirm_order");
    });
    $(".btn_tuihuan").click(function(){
        show_pop($(this).attr("order_id"),"tuihuan_order");
    });
    $(".btn_shipreturn").click(function(){
        show_pop($(this).attr("order_id"),"ship_return_order");
    });
    $(".btn_cancle2").click(function(){
        show_pop($(this).attr("order_id"),"cancel_order2");
    });

    user.by_btn(".btn_address","address_list",false,null);
}


// 选择地址后操作
function update_address(json){
    var receiver_name = json.receiver_name;
    var receiver_phone = json.receiver_phone;
    var location = json.state_name+','+json.city_name+','+json.district_name+','+json.address;

    // 询问
    layer.open({
        content: "收货人：" + receiver_name + "<br>联系电话：" + receiver_phone + "<br>收货地址：" + location,
        btn: ['确认无误', '重填'],
        yes: function(){
            // ajax
            var ll = layer.open({type: 2});
            var options = [{ "url": "/ajax/order.setAddress.php", "data":{order_id:order_id,receiver_name:receiver_name,receiver_phone:receiver_phone,location:location}, "type":"POST", "dataType":"json"}]
            Load(options, function(json){
                layer.close(ll);
                if(json.status=="success"){
                    layer.open({
                        content:'设置地址成功！',
                        btn:['好的'],
                        end:function(){
                            user.exit();
                            window.location.reload();
                        }
                    });
                    return ;
                }else{
                    _working = false
                    var errorsummary = ""
                    switch(json.status){
                        case "no_order_id":
                            errorsummary = "不存在的订单号"
                            break;
                        case "no_login":
                            errorsummary = "用户没有登录，或用户数据过期，请重新登录"
                            break;
                        case "error":
                            errorsummary = "未知原因，请刷新重试。";
                            break;
                    }
                    shownotice({
                        "icon":"notice",
                        "title":"操作失败，原因如下",
                        "remark":errorsummary
                    },[],function(){
                        $('.btn_secondary').click(function(){
                            user.exit();
                            window.location.reload();
                        });
                    });
                }
            });
        },
        no: function(){
            // 重新打开地址面板
            user.action('address_list',false);
            user.createpannel();
        }
    });



}


var _working = false;
function show_pop(order_id,type,data) {
    var data = arguments[2] ? arguments[2] : {};
    _html =  "<section id='user_pannel'>";
    _html += '<span id="btn_close"><img src="/statics/img/btn.close.png" width="50" /></span>';
    _html += pophtml(type,data);
    _html += '</section>';

    if($("#user_pannel").length>0){
        $("#user_pannel").remove();
    }
    $("body").append(_html).css("overflow","hidden");
    $(".main").removeClass("zoom_out").addClass("zoom_in");
    $("#user_pannel").removeClass("left_to_right").addClass("right_to_left");

    if(type=="repay"){
        check_balance(order_id);
    }

    // $("#user_pannel #btn_close").unbind("click");
    $("#btn_confirm_cancel").unbind("click");
    $("#btn_confirm_delete").unbind("click");
    $("#btn_confirm_tuikuan").unbind("click");
    $("#btn_confirm_think").unbind("click");
    $("#btn_confirm_order").unbind("click");
    // $(".btn_confirm_pay").unbind("click")

    $("#user_pannel #btn_close").click(function(){
        if(!_working){
            $(".main").removeClass("zoom_in").addClass("zoom_out");
            $("#user_pannel").removeClass("right_to_left").addClass("left_to_right");
            $("body").attr("style","display:block;");
            _working = false;
        }
    });
    $("#btn_confirm_cancel").click(function(){
        if(!_working){
            $(this).html("<img src='/statics/img/loader2.gif' width='30' style='display:block' />");
            _working = true;
            cancel_order(order_id);
        }

    });
    $("#btn_confirm_delete").click(function(){
        if(!_working){
            $(this).html("<img src='/statics/img/loader2.gif' width='30' style='display:block' />");
            _working = true;
            delete_order(order_id);
        }
    });
    $("#btn_confirm_tuikuan").click(function(){
        if(!_working){
            $(this).html("<img src='/statics/img/loader2.gif' width='30' style='display:block' />");
            _working = true;
            tuikuan_order(order_id);
        }
    });
    $("#btn_confirm_think").click(function(){
        if(!_working){
            $(".main").removeClass("zoom_in").addClass("zoom_out");
            $("#user_pannel").removeClass("right_to_left").addClass("left_to_right");
            $("body").attr("style","display:block;");
            _working = false;
        }
    });
    $("#btn_confirm_order").click(function(){
        if(!_working){
            $(this).html("<img src='/statics/img/loader2.gif' width='30' style='display:block' />");
            _working = true;
            confirm_order(order_id);
        }
    });
    $("#btn_tuihuan_order").click(function(){
        if(!_working){
            $(".main").removeClass("zoom_in").addClass("zoom_out");
            $("#user_pannel").removeClass("right_to_left").addClass("left_to_right");
            $("body").attr("style","display:block;");
            _working = false;
        }
    });
    $("#btn_shipreturn_order").click(function(){
        var ship_com = $('#ship_com').val(),
            ship_nu = $('#ship_nu').val();

        if(ship_com=="" || ship_nu==""){
            layer.open({content:'请输入快递公司名称和快递单号'});
            return false;
        }
        $(this).html("<img src='/statics/img/loader2.gif' width='30' style='display:block' />");
        var options = [{ "url": "/ajax/order.shipreturn.php", "data":{order_id:order_id,com:ship_com,nu:ship_nu}, "type":"POST", "dataType":"json"}]
        Load(options, function(json){
            layer.open({
                content:json['ms_msg'],
                end:function(){
                    user.exit();
                    window.location.reload();
                }
            });
        });
    });

    $(".btn_confirm_pay").bind('click',function(){
        if(!_working){
            var method_type = $(this).data('type');
            var sn = $(this).data('sn');
            var merge_payment = $('#merge_payment').prop('checked');
            var salt = 'C'+Math.floor(Math.random()*99999999+1);
            setCookie('salt',salt);

            if(merge_payment==true){
                switch(method_type){
                    case 'weixin':
                        window.location.href = '/wxpay_charge.php?sn='+sn+'&salt='+salt;
                        break;
                    case 'alipay':
                        window.location.href = '/alipay_charge.php?sn='+sn+'&salt='+salt;
                        break;
                    default:
                        break;
                }
            }else{
                window.location.href = '/pay.php?method='+method_type+'&sn='+sn+'&salt='+salt;
            }
        }
    })
    $("#btn_confirm_balance").click(function(){
        if(!_working){
            if(!$(this).hasClass("btn_other")){
                $(this).html("<img src='/statics/img/loader2.gif' style='height:25px;vertical-align:middle;' />");
                _working = false;
                pay_balance(order_id);
            }
        }
    })
}


function pophtml(type,data){
    var __html =""
    if(type=="cancel_order") {
        __html += '<div id="popwin">'
        __html += '<div class="inbox">'
        __html += '<div class="status_icon" style="padding:0 0 30px 0;"><i class="iconfont rotateZ360">&#xe61e;</i></div>'
        __html += '<div class="action_title">取消订单</div>'
        __html += '<div class="subject pb20">'
        __html += '<div class="checkboxitem"><label><input type="radio" name="title" id="cancel_title1" value="拍错了，不想买" checked><span style="position: relative;top: -2px;">拍错了，不想买</span></label></div>'
        __html += '<div class="checkboxitem"><label><input type="radio" name="title" id="cancel_title2" value="尺寸不合适"><span style="position: relative;top: -2px;">尺寸不合适</span></label></div>'
        __html += '</div>'
        __html += '<div class="subject pb20">'
        __html += '<textarea class="content" id="cancelcontent" name="content" rows="3" placeholder="想说更多"></textarea>'
        __html += '<input type="hidden" id="order_id" />'
        __html += '</div>'
        __html += '<div class="subject pb20">'
        __html += '<button id="btn_confirm_cancel" class="btn_normal">确定取消</button>'
        __html += '</div>'
        __html += '<br/>'
        __html += '</div>'
        __html += '</div>'
    }
    if(type=="cancel_order2") {
        __html += '<div id="popwin">'
        __html += '<div class="inbox">'
        __html += '<div class="status_icon" style="padding:0 0 30px 0;"><i class="iconfont rotateZ360">&#xe61e;</i></div>'
        __html += '<div class="action_title">确定取消退换货申请？</div>'
        __html += '<input type="hidden" id="order_id" />'
        __html += '<div class="subject pb20">'
        __html += '<button id="btn_confirm_think" style="margin-right: 10px;" class="btn_normal btn_other">按错了</button>'
        __html += '<button id="btn_confirm_cancel" class="btn_normal">确定取消</button>'
        __html += '</div>'
        __html += '<br/>'
        __html += '</div>'
        __html += '</div>'
    }
    if(type=="delete_order") {
        __html += '<div id="popwin">'
        __html += '<div class="inbox">'
        __html += '<div class="status_icon" style="padding:0 0 30px 0;"><i class="iconfont rotateZ360">&#xe61f;</i></div>'
        __html += '<div class="action_title">删除订单</div>'
        __html += '<div class="subject pb30">您确定要删除该订单吗？</div>'
        __html += '<div class="subject pb20">'
        __html += '<button id="btn_confirm_delete" class="btn_normal">确定删除</button>'
        __html += '</div>'
        __html += '<br/>'
        __html += '</div>'
        __html += '</div>'
    }

    if(type=="tuikuan_order") {
        __html += '<div id="popwin">'
        __html += '<div class="inbox">'
        __html += '<div class="status_icon" style="padding:0 0 30px 0;"><i class="iconfont rotateZ360">&#xe621;</i></div>'
        __html += '<div class="action_title">退款申请</div>'
        __html += '<div class="subject pb30">您确定不购买了吗？</div>'
        __html += '<div class="subject pb20">'
        __html += '<button id="btn_confirm_think" style="margin-right: 10px;" class="btn_normal btn_other">我再想想</button>'
        __html += '<button id="btn_confirm_tuikuan" class="btn_normal">确定不买了</button>'
        __html += '</div>'
        __html += '<br/>'
        __html += '</div>'
        __html += '</div>'
    }

    if(type=="repay") {
        __html += '<div id="popwin">'
        __html += '<div class="inbox repay">'
        __html += '<div class="status_icon pb20"><i class="iconfont rotateZ360">&#xe620;</i></div>'
        __html += '<div class="action_title">重新付款</div>'
        __html += '<div class="subject pb20">请选择付款方式:</div>'
        __html += '<div class="subject pb20">'
        __html += '<button data-sn="'+data.order_sn+'" class="btn_normal btn_confirm_pay" data-type="weixin"><i class="iconfont weixin">&#xe659;</i> 微信支付</button>'
        __html += '<button data-sn="'+data.order_sn+'" class="btn_normal btn_confirm_pay" data-type="alipay"><i class="iconfont alipay">&#xe65a;</i> 支付宝支付</button>'
        if(account_balance=='show'){
            __html += '<button data-sn="'+data.order_sn+'" id="btn_confirm_balance" class="btn_normal btn_other" data-type="" disabled="disabled"><i class="iconfont">&#xe65b;</i> 钱包支付 <img src="/statics/img/loader.gif" style="height:30px;vertical-align:middle;" /></button>'
        }
        __html += '</div>'
        __html += '<br/>'
        __html += '</div>'
        __html += '</div>'
    }

    if(type=="confirm_order") {
        __html += '<div id="popwin">';
        __html += '<div class="inbox">';
        __html += '<div class="status_icon" style="padding:0 0 30px 0;"><i class="iconfont rotateZ360">&#xe62c;</i></div>';
        __html += '<div class="action_title pb10">确定完成该订单的交易?</div>';
        __html += '<div class="subject pb30">确定后将无法申请退换货</div>';
        __html += '<div class="subject pb20">';
        __html += '<button id="btn_confirm_order" class="btn_normal">确定</button>';
        __html += '</div>';
        __html += '<br/>';
        __html += '</div>';
        __html += '</div>';
    }

    if(type=="tuihuan_order") {
        __html += '<div id="popwin">'
        __html += '<div class="inbox">'
        __html += '<div class="status_icon" style="padding:0 0 30px 0;"><i class="iconfont rotateZ360">&#xe61d;</i></div>'
        __html += '<div class="action_title pb30">产品退换请登录电脑版网页进行操作<br/>不便之处，敬请谅解！</div>'
        __html += '<div class="subject pb20">'
        __html += '<button id="btn_tuihuan_order" class="btn_normal">确定</button>'
        __html += '</div>'
        __html += '<br/>'
        __html += '</div>'
        __html += '</div>'
    }

    if(type=="ship_return_order") {
        __html += '<div id="popwin">'
        __html += '<div class="inbox">'
        __html += '<div class="status_icon" style="padding:0 0 10px 0;"><i class="iconfont rotateZ360">&#xe63c;</i></div>'
        __html += '<div class="action_title">请把需要退换的商品寄回<br/>并填写寄回快递单号</div>'

        __html += '<div class="subject pb10">'
        __html += '<input type="text" id="ship_com" class="input_normal" placeholder="快递公司" />'
        __html += '</div>'
        __html += '<div class="subject pb10">'
        __html += '<input type="text" id="ship_nu" class="input_normal" placeholder="快递单号" />'
        __html += '</div>'

        __html += '<div class="subject pt20">'
        __html += '<button id="btn_shipreturn_order" class="btn_normal">确定</button>'
        __html += '</div>'
        __html += '<br/>'
        __html += '</div>'
        __html += '</div>'
    }

    if(type=="get_wuliu") {
        __html += '<div id="popwin">';
        __html += '<div class="wuliu_box">';

        // 0在途中、1已揽收、2疑难、3已签收、4退签、5同城派送中、6退回、7转单
        switch(wuliuinfo.state){
            case '0':
                state = '在途中';
                break;
            case '1':
                state = '已揽收';
                break;
            case '2':
                state = '疑难件';
                break;
            case '3':
                state = '已签收';
                break;
            case '4':
                state = '退签';
                break;
            case '5':
                state = '派送中';
                break;
            case '6':
                state = '退回';
                break;
            case '7':
                state = '转单';
                break;
            default:
                state = '商家已发货';
                break;
        }
        __html += '<div class="wuliu_head">';
        if(state!=''){__html += '<p>物流状态：'+state+'</p>';}
        __html += '<p>物流公司：'+ship_com+'</p>';
        __html += '<p>快递单号：'+ship_sn+'</p>';
        __html += '</div>';
        if(wuliuinfo && wuliuinfo!=undefined){
            __html += '<div class="wuliu_body"><dl>';
            __html += '<dt>物流跟踪</dt>';
            $.each(wuliuinfo.data,function(i,w){
                __html += '<dd class="d-'+i+'"><p>'+w.context+'</p><p>'+w.ftime+'</p></dd>';
            });
            __html += '</dl></div>';
        }else{
            __html += '<p style="color: #999;line-height: 2em;margin-top: 3em;font-size: 1.2em;">暂时没有获取到物流信息，<br>您可登录&lt;'+ship_com+'&gt;官网查询最新物流状态。</p>';
        }
        __html += '</div>';
        __html += '</div>';
    }

    

    return __html;
}


function cancel_order(order_id){
    var title = $("#cancel_title1").prop("checked") ? $("#cancel_title1").val() : $("#cancel_title2").val()
    var content = $("#cancelcontent").val()

    var options = [{ "url": "/ajax/order.cancel.php", "data":{order_id:order_id,title:title,content:content}, "type":"POST", "dataType":"json"}]
    Load(options, function(json){
        if(json.status=="success"){
            shownotice(
                {
                    "icon":"success",
                    "title":"成功取消订单",
                    "remark":"您已经成功取消订单。<br/>该窗口2秒后关闭。"
                },
                [],
                function(){
                    $("#systemnoticebox .popwinbtnclose").hide()
                    window.setTimeout(function(){
                        window.location.href='/?m=account&a=order_detail&order_id='+json.order_id;
                    },2000)
                }
            )

        }else{
            _working = false
            var errorsummary = ""
            $("#maskdiv").remove()
            switch(json.status){
                case "no_order_id":
                    errorsummary = "不存在的订单号"
                    break;
                case "no_login":
                    errorsummary = "用户没有登录，或用户数据过期，请重新登录"
                    break;
            }

            shownotice({
                "icon":"notice",
                "title":"未能取消订单，错误如下",
                "remark":errorsummary
            },[],function(){
                user.exit()
            })
        }
    })
}

function tuikuan_order(order_id){
    var options = [{ "url": "/ajax/order.tuikuan.php", "data":{order_id:order_id}, "type":"POST", "dataType":"json"}]
    Load(options, function(json){
        if(json.status=="success"){
            layer.open({
                content:'发送退款请求成功<br>您已经成功发送退款，等待审核后款项将原路退回您的账户。',
                btn:['知道了'],
                end:function(){
                    user.exit();
                    window.location.reload();
                }
            });
            return ;
        }else{
            _working = false
            var errorsummary = ""
            $("#maskdiv").remove()
            switch(json.status){
                case "no_order_id":
                    errorsummary = "不存在的订单号"
                    break;

                case "no_login":
                    errorsummary = "用户没有登录，或用户数据过期，请重新登录"
                    break;

                case "packing":
                    errorsummary = "非常抱歉,刚刚查询到您的订单状态为打包中，订单将为锁定状态,不能退款.<br>如果疑问，请与客服联系。"
                    break;
                case "refuse_refund":
                    errorsummary = "已经发货，不可以申请退款。"
                    break;
            }

            if(json.status=="packing"){
                shownotice({
                    "icon":"notice",
                    "title":"未能退款，错误如下",
                    "remark":errorsummary
                },[],function(){
                    $('.btn_secondary').click(function(){
                        user.exit();
                        window.location.reload();
                    });
                });
            }else{
                shownotice({
                    "icon":"notice",
                    "title":"未能退款，错误如下",
                    "remark":errorsummary
                },[],function(){
                    $('.btn_secondary').click(function(){
                        user.exit();
                        window.location.reload();
                    });
                });
            }
        }
    });
}


function confirm_order(order_id){
    var options = [{ "url": "/ajax/order.confirm.php", "data":{order_id:order_id}, "type":"POST", "dataType":"json"}]
    Load(options, function(json){
        if(json.status=="success"){
            layer.open({content:'交易完成，感谢您的支持！',time:2});
            window.setTimeout(function(){
                user.exit();
                window.location.reload();
            },2000);
        }else{
            _working = false
            var errorsummary = ""
            $("#maskdiv").remove()
            switch(json.status){
                case "no_order_id":
                    errorsummary = "不存在的订单号"
                    break;
                case "no_login":
                    errorsummary = "用户没有登录，或用户数据过期，请重新登录"
                    break;
            }
            shownotice({
                "icon":"notice",
                "title":"操作失败，原因如下",
                "remark":errorsummary
            },[],function(){
                user.exit()
            });
        }
    });
}


function check_balance(order_id){
    var options = [{ "url": "/ajax/check.balance.php", "data":{order_id:order_id}, "type":"POST", "dataType":"json"}];
    Load(options, function(json){
        if(json.status=="success"){
            $("#btn_confirm_balance").removeClass("btn_other");
            $("#btn_confirm_balance").removeAttr('disabled');
            $("#btn_confirm_balance").html('<i class="iconfont bag">&#xe65b;</i> 钱包支付 (¥'+json.total+')');
        }else if(json.status=='merge'){
            $("#btn_confirm_balance").addClass('checkbox').html('<label><input type="checkbox" id="merge_payment" /> 使用余额合并付款 (¥'+json.total+')</label>');
        }else{
            $("#btn_confirm_balance").unbind('click');
            $("#btn_confirm_balance").attr('disabled','disabled');
            $("#btn_confirm_balance").html('<i class="iconfont bag">&#xe65b;</i> 余额不足 (¥'+json.total+')');
        }
    },function(){});
}

function pay_balance(order_id){

    user.show("reenterpassword",function(){
        $("#maskdiv").remove();
        messagetitle = "正在使用余额支付";

        $("body").append('<div class="realcenter"  id="maskdiv" style="text-align: center">\
            <img src="/statics/img/loader.gif" /><br/>'+messagetitle+'，请稍候...\
        </div>');
        var options = [{ "url": "/ajax/pay.balance.php", "data":{order_id:order_id}, "type":"POST", "dataType":"json"}];
        Load(options, function(json){
            if(json.status=="success"){
                $("#maskdiv").remove()
                shownotice({
                    "icon":"success",
                    "title":"购买成功",
                    "remark":"恭喜您，您已经成功支付。订单号："+json.order_sn+" ;我们将尽快为您发货配送！系统将在3秒后跳转。<br>感谢您的支持与信赖！"
                },[],function(){
                    window.setTimeout(function(){
                        window.location.href = "/?m=account&a=order_detail&order_id="+json.order_id;
                    },3000)
                });
            }else{
                $("#maskdiv").remove();
                if(json.status=="no_balance"){
                    shownotice({
                        "icon":"notice",
                        "title":"购买失败",
                        "remark":"非常遗憾，支付失败。您当前的余额："+json.balance+" 不足以支付本次订单金额："+json.price+" 。<br>请充值后再试，或者直接采用微信支付，<br>感谢您的支持与信赖！"
                    },[],function(){
                        window.setTimeout(function(){
                            window.location.href = "/?m=account&a=order_detail&order_id="+json.order_id;
                        },3000)
                    });
                }else if(json.status=="no_login"){
                    shownotice({
                        "icon":"notice",
                        "title":"购买失败",
                        "remark":"非常遗憾，您还没登录，请登录后再试，<br>感谢您的支持与信赖！"
                    },[],function(){
                        window.setTimeout(function(){
                            window.location.href = "/?m=account&a=order_detail&order_id="+json.order_id;
                        },3000)
                    });
                }else if(json.status=="is_payed"){
                    shownotice({
                        "icon":"notice",
                        "title":"支付失败",
                        "remark":"该订单已经支付，请勿重复付款，<br>感谢您的支持与信赖！"
                    },[],function(){
                        window.setTimeout(function(){
                            window.location.href = "/?m=account&a=order_detail&order_id="+json.order_id;
                        },3000)
                    })
                }else{
                    shownotice({
                        "icon":"notice",
                        "title":"购买失败",
                        "remark":"未知错误，请登录后再试，<br>感谢您的支持与信赖！"
                    },[],function(){
                        window.setTimeout(function(){
                            window.location.href = "/?m=account&a=order_detail&order_id="+json.order_id;
                        },3000)
                    })
                }


            }
        },function(){})
        //alert(order_id)
    });
}