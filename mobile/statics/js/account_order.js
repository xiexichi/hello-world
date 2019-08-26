var re_pay_url = "";
$(function() {
    init();
    $('.order_list_box').each(function(){
        var innerbox = $(this).find('.innerbox');
        var h = $(this).find('.innerbox').height()+20;
        if(innerbox.length>1){
            $(this).height(h+h/2);
        }
    });
});

// 下拉菜单
function down_menu(height,hrefString) {
    var height = arguments[0] ? arguments[0] : 409;
    var hrefString = arguments[1] ? arguments[1] : '?m=account&a=order&s=';
    $(".order_class").click(function () {
        if ($(this).attr("show") == 0) {
            show_class_list(height)
            $(this).attr("show", 1)
        } else {
            hide_class_list()
            $(this).attr("show", 0)
        }
    })

    $(".order_class ul li").click(function () {
        if ($(this).index() != 0) {
            window.location.href = hrefString+$(this).attr("rel")
        }
    })
}

function init(){
    //下拉菜单
    if(typeof height == 'undefined') {
        down_menu(); //我的订单专用
    }else {
        down_menu(height,hrefString);//我的推广
    }

    $(".order_list ul li").each(function(){
        var target = $(this).find(".distancetime")
        if(target.attr("order_time")!=0){
            timer(target.attr("order_time"),target)
        }
        $(this).find(".btn_remark").click(function(){
            var order_sn = $(this).data("sn");
            var flag_color = $(this).data("color");
            var remark = $(this).data("remark");
            var data = {
                order_sn : order_sn,
                flag_color : flag_color,
                remark : remark
            };
            show_pop($(this).attr("order_id"),"remark_order",data);          
        })
        $(this).find(".btn_cancle").click(function(){
            show_pop($(this).attr("order_id"),"cancel_order");
        })
        $(this).find(".btn_delete").click(function(){
            show_pop($(this).attr("order_id"),"delete_order");
        })
        $(this).find(".btn_tuikuan").click(function(){
            show_pop($(this).attr("order_id"),"tuikuan_order");
        })
        $(this).find(".btnconfirm").click(function(){
            show_pop($(this).attr("order_id"),"confirm_order");
        })
        $(this).find(".btn_tuihuan").click(function(){
            show_pop($(this).attr("order_id"),"tuihuan_order");
        })
        $(this).find(".btn_shipreturn").click(function(){
            show_pop($(this).attr("order_id"),"ship_return_order");
        })
        $(this).find(".btn_paynow").click(function(){
            var data = {
                order_sn : $(this).data('sn')
            };
            show_pop($(this).attr("order_id"),"repay",data);
        })
        $(this).find(".btn_cancle2").click(function(){
            show_pop($(this).attr("order_id"),"cancel_order2");
        })

    })
    $(".order_list ul li .order_list_more").click(function(){
        if(!$(this).hasClass("disopen")){
            var listbox = $(this).parent().find(".order_list_box")
            var h = listbox.find('.innerbox').height()+20;
            if($(this).attr("rel")=="close"){
                listbox.animate({"height":listbox.parent().find(".innerbox").length*h})
                $(this).attr("rel","open")
                $(this).find("span").removeClass("rotate180back").addClass("rotate180")
            }else{
                listbox.animate({"height":h+h/2})
                $(this).attr("rel","close")
                $(this).find("span").removeClass("rotate180").addClass("rotate180back")
            }
        }
    })
    //订单搜索
    $('#search_orders').click(function(){
        show_pop(0,"search_orders");
    });
    //我要推广搜索
    $('#search_promote').click(function(){
        show_pop(0,"search_promote");
    });
    //我要推广 类目搜索
    $('#search_promote_category').click(function(){
        show_pop(0,"search_promote_category");
    });    //我的推广搜索
    $('#search_promotion').click(function(){
        show_pop(0,"search_promotion");
    });
    //提现
    $('#btn_withdraw').click(function(){
        show_pop(0,"btn_withdraw");
    });
    //提现方式
    $('#btn_add_withdrawal').click(function(){
        show_pop(0,"btn_add_withdrawal");
    });
    //修改提现方式
    $('.click_to_update_withdrawal').click(function(){
        var pwithdrawal_id = $(this).data('id');
        var withdrawal_type = $(this).find('li:first').data('type');
        var withdrawal_account = $(this).find('li:eq(1)').text();
        var withdrawal_name = $(this).find('li:last').text();
        var data = {
            "pwithdrawal_id" : pwithdrawal_id,
            "withdrawal_type" : withdrawal_type,
            "withdrawal_account" : withdrawal_account,
            "withdrawal_name" : withdrawal_name,
        };
        show_pop(0,"click_to_update_withdrawal",data);
    });

}

function show_class_list(height){
    $(".order_class span.arrow").removeClass("rotate180back").addClass("rotate180")
    $(".order_class").animate({"height":height},"fast")
}

function hide_class_list(){
    $(".order_class span.arrow").removeClass("rotate180").addClass("rotate180back")
    $(".order_class").animate({"height":40},"fast")
}
function timer(intDiff,target){
    window.setInterval(function(){
        var day=0,
            hour=0,
            minute=0,
            second=0, //时间默认值
            exp_time = '还剩 ';
        if(intDiff > 0){
            day = Math.floor(intDiff / (60 * 60 * 24));
            hour = Math.floor(intDiff / (60 * 60)) - (day * 24);
            minute = Math.floor(intDiff / 60) - (day * 24 * 60) - (hour * 60);
            second = Math.floor(intDiff) - (day * 24 * 60 * 60) - (hour * 60 * 60) - (minute * 60);
        }
        if (minute <= 9) minute = '0' + minute;
        if (second <= 9) second = '0' + second;


        if (day>0) exp_time += day+'天';
        if (hour>0) exp_time += hour+'小时';
        if (minute>0) exp_time += minute+'分';
        if (second>0) exp_time += second+'秒 过期';
        target.find('strong').html(exp_time);
        //console.log(intDiff)
        intDiff--;
    }, 1000);
}

var _working = false;
function show_pop(order_id,type,data) {
    var data = arguments[2] ? arguments[2] : {};
    _html =  "<section id='user_pannel'>";
    _html += '<span id="btn_close"><img src="/statics/img/btn.close.png" width="50" /></span>';
    _html += pophtml(type,data)
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

    if(type=="search_orders"){
        $('#search_keywords').focus();
    }

    // $("#user_pannel #btn_close").unbind("click");
    $("#btn_confirm_cancel").unbind("click");
    $("#btn_confirm_delete").unbind("click");
    $("#btn_confirm_tuikuan").unbind("click");
    $("#btn_confirm_think").unbind("click");
    $("#btn_confirm_order").unbind("click");
    // $(".btn_confirm_pay").unbind("click");
    $("#btn_remark_order").unbind("click");
    $("#btn_confirm_search").unbind("click");
    $("#btn_search_promote").unbind("click");
    $("#btn_search_promote_category").unbind("click");
    $("#btn_search_promotion").unbind("click");
    $("#btn_confirm_withdraw").unbind("click");
    $("#btn_confirm_withdrawal").unbind("click");

    $("#btn_confirm_search").click(function(){
        if(!_working){
            $(this).html("<img src='/statics/img/loader2.gif' style='height:25px;vertical-align:middle;' />")
            _working = true
            search_orders(this);
        }
    });

    $("#btn_search_promote").click(function(){
        if(!_working){
            $(this).html("<img src='/statics/img/loader2.gif' style='height:25px;vertical-align:middle;' />")
            _working = true
            btn_search_promote(this);
        }
    });

    $("#btn_search_promote_category").click(function(){
        if(!_working){
            $(this).html("<img src='/statics/img/loader2.gif' style='height:25px;vertical-align:middle;' />")
            _working = true
            btn_search_promote_category(this);
        }
    });
    
    $("#btn_search_promotion").click(function(){
        if(!_working){
            $(this).html("<img src='/statics/img/loader2.gif' style='height:25px;vertical-align:middle;' />")
            _working = true
            btn_search_promotion(this);
        }
    });

    $("#btn_confirm_withdraw").click(function(){
        if(!_working){
            $(this).html("<img src='/statics/img/loader2.gif' style='height:25px;vertical-align:middle;' />")
            _working = true
            btn_confirm_withdraw(this);
        }
    });

    $("#btn_confirm_withdrawal").click(function(){
        if(!_working){
            $(this).html("<img src='/statics/img/loader2.gif' style='height:25px;vertical-align:middle;' />")
            _working = true
            btn_confirm_withdrawal(this);
        }
    });
    $("#btn_update_withdrawal").click(function(){
        if(!_working){
            $(this).html("<img src='/statics/img/loader2.gif' style='height:25px;vertical-align:middle;' />")
            _working = true
            btn_update_withdrawal(this);
        }
    });

    $("#btn_remark_order").click(function(){
        if(!_working){
            $(this).html("<img src='/statics/img/loader2.gif' style='height:25px;vertical-align:middle;' />")
            _working = true
            remark_order(order_id,this);
        }
    });

    $("#user_pannel #btn_close").click(function(){
        if(!_working){
            $(".main").removeClass("zoom_in").addClass("zoom_out")
            $("#user_pannel").removeClass("right_to_left").addClass("left_to_right")
            $("body").attr("style","display:block;");
            _working = false
        }
    });

    $("#btn_confirm_cancel").click(function(){
        if(!_working){
            $(this).html("<img src='/statics/img/loader2.gif' style='height:25px;vertical-align:middle;' />")
            _working = true
            cancel_order(order_id);
        }

    })

    $("#btn_confirm_delete").click(function(){
        if(!_working){
            $(this).html("<img src='/statics/img/loader2.gif' style='height:25px;vertical-align:middle;' />")
            _working = true
            delete_order(order_id);
        }
    })

    $("#btn_confirm_tuikuan").click(function(){
        if(!_working){
            $(this).html("<img src='/statics/img/loader2.gif' style='height:25px;vertical-align:middle;' />")
            _working = true
            tuikuan_order(order_id);
        }
    })
    $("#btn_confirm_think").click(function(){
        if(!_working){
            $(".main").removeClass("zoom_in").addClass("zoom_out")
            $("#user_pannel").removeClass("right_to_left").addClass("left_to_right")
            $("body").attr("style","display:block;");
            _working = false
        }
    })
    $("#btn_confirm_order").click(function(){
        if(!_working){
            $(this).html("<img src='/statics/img/loader2.gif' style='height:25px;vertical-align:middle;' />")
            _working = true
            confirm_order(order_id);
        }
    })

    $("#btn_tuihuan_order").click(function(){
        if(!_working){
            $(".main").removeClass("zoom_in").addClass("zoom_out")
            $("#user_pannel").removeClass("right_to_left").addClass("left_to_right")
            $("body").attr("style","display:block;");
            _working = false
        }
    })

    $("#btn_shipreturn_order").click(function(){
        var ship_com = $('#ship_com').val(),
            ship_nu = $('#ship_nu').val();

        if(ship_com=="" || ship_nu==""){
            layer.open({content:'请输入快递公司名称和快递单号'});
            return false;
        }
        $(this).html("<img src='/statics/img/loader2.gif' style='height:25px;vertical-align:middle;' />");
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
    if(type=="search_orders") {
        __html += '<div id="popwin">'
        __html += '<div class="inbox" style="position:relative;top:-60px;">'
        __html += '<div class="status_icon" style="padding:0 0 30px 0;"><i class="iconfont rotateZ360">&#xe657;</i></div>'
        __html += '<div class="action_title">查询订单</div>'
        __html += '<div class="subject pb20" style="width:100%;margin:0 auto;color:#333;">'
        __html += '<div style="text-align:left;margin-bottom:0.5em;font-size:1.5em">'
        // __html += '订单类型&nbsp;'
        __html += '<select id="search_type" style="padding:0.7em;width:100%;-webkit-appearance: menulist">'
        __html += '<option value="all" style="padding:5px;">全部订单</option>'
        __html += '<option value="nopay">待付款</option>'
        __html += '<option value="pack">等待发货</option>'
        __html += '<option value="wait">待收货</option>'
        __html += '<option value="3">退款订单</option>'
        __html += '<option value="5">退货订单</option>'
        __html += '<option value="7">换货订单</option>'
        __html += '<option value="8">交易成功</option>'
        __html += '<option value="-1">交易关闭</option>'
        __html += '</select>'
        __html += '</div>'
        __html += '<div style="text-align:left;font-size:1.5em">'
        // __html += '<label>查询内容&nbsp;'
        __html += '<input type="text" id="search_keywords" style="padding:0.7em 0 0.7em 0.7em;width:96%;border:1px solid #ccc;border-radius:5px;" placeholder="订单号、收货人、联系电话、商品名称、备注">'
        __html += '</label>'
        __html += '</div>'
        __html += '</div>'
        __html += '<div class="subject pb30">'
        __html += '<button id="btn_confirm_search" class="btn" style="width:100%">查询订单</button>'
        __html += '</div>'
        // __html += '<div style="color:#999;">查询内容：订单号、收货人、联系电话、商品名称、备注。</div>'
        __html += '<br/>'
        __html += '</div>'
        __html += '</div>'
    }
    if(type=="search_promote") {
        __html += '<div id="popwin">'
        __html += '<div class="inbox" style="position:relative;top:-60px;">'
        __html += '<div class="status_icon" style="padding:0 0 30px 0;"><i class="iconfont rotateZ360">&#xe657;</i></div>'
        __html += '<div class="action_title">查询推广产品</div>'
        __html += '<div class="subject pb20" style="width:100%;margin:0 auto;color:#333;">'
        __html += '<div style="text-align:left;margin-bottom:0.5em;font-size:1.5em">'
        __html += '<select id="search_category" name="search_category" style="padding:0.8em;width:100%;-webkit-appearance: menulist">'
        __html += '<option value="0" style="padding:5px;">全部类目</option>'
        $.each(secondly_category.cs,function(k,v) {
            __html += '<option value="'+v.category_id+'" style="padding:5px;">'+v.category_name+'</option>';
        });
        __html += '</select>'
        __html += '</div>'
        __html += '<div style="text-align:left;font-size:1.5em">'
        __html += '<input type="text" id="search_keywords" name="search_keywords" style="padding:0.8em 0 0.8em 0.8em;width:96%;border:1px solid #ccc;border-radius:5px;" placeholder="产品标题、产品编号">'
        __html += '</label>'
        __html += '</div>'
        __html += '</div>'
        __html += '<div class="subject pb30">'
        __html += '<button id="btn_search_promote" class="btn" style="width:100%">确认查询</button>'
        __html += '</div>'
        __html += '<br/>'
        __html += '</div>'
        __html += '</div>'
    }
    if(type=="search_promote_category") {
        __html += '<div id="popwin">'
        __html += '<div class="inbox" style="position:relative;top:-60px;">'
        __html += '<div class="status_icon" style="padding:0 0 30px 0;"><i class="iconfont rotateZ360">&#xe657;</i></div>'
        __html += '<div class="action_title">查询推广类目</div>'
        __html += '<div class="subject pb20" style="width:100%;margin:0 auto;color:#333;">'
        __html += '<div style="text-align:left;font-size:1.5em">'
        __html += '<input type="text" id="search_keywords" name="search_keywords" style="padding:0.8em 0 0.8em 0.8em;width:96%;border:1px solid #ccc;border-radius:5px;" placeholder="类目标题">'
        __html += '</label>'
        __html += '</div>'
        __html += '</div>'
        __html += '<div class="subject pb30">'
        __html += '<button id="btn_search_promote_category" class="btn" style="width:100%">确认查询</button>'
        __html += '</div>'
        __html += '<br/>'
        __html += '</div>'
        __html += '</div>'
    }
    if(type=="search_promotion") {
        __html += '<div id="popwin">'
        __html += '<div class="inbox" style="position:relative;top:-60px;">'
        __html += '<div class="status_icon" style="padding:0 0 30px 0;"><i class="iconfont rotateZ360">&#xe657;</i></div>'
        __html += '<div class="action_title">查询推广产品</div>'
        __html += '<div class="subject pb20" style="width:100%;margin:0 auto;color:#333;">'
        __html += '<div style="text-align:left;margin-bottom:0.5em;font-size:1.5em">'
        __html += '<select id="search_category" name="search_category" style="padding:0.8em;width:100%;-webkit-appearance: menulist">'
        __html += '<option value="0" style="padding:5px;">全部类目</option>'
        $.each(secondly_category.cs,function(k,v) {
            __html += '<option value="'+v.category_id+'" style="padding:5px;">'+v.category_name+'</option>';
        });
        __html += '</select>'
        __html += '</div>'
        __html += '<div style="text-align:left;font-size:1.5em">'
        __html += '<input type="text" id="search_keywords" name="search_keywords" style="padding:0.8em 0 0.8em 0.8em;width:96%;border:1px solid #ccc;border-radius:5px;" placeholder="产品标题、产品编号">'
        __html += '</label>'
        __html += '</div>'
        __html += '</div>'
        __html += '<div class="subject pb30">'
        __html += '<button id="btn_search_promotion" class="btn" style="width:100%">确认查询</button>'
        __html += '</div>'
        __html += '<br/>'
        __html += '</div>'
        __html += '</div>'
    }
    if(type=="btn_withdraw") {
        var withdrawal_total = '¥'+$('.totaltext').data('val');
        __html += '<div id="popwin">'
        __html += '<div class="inbox" style="position:relative;top:0;">'
        __html += '<div class="status_icon user_pannel_header" style="padding:0 0 10px 0;"><i class="iconfont rotateZ360">&#xe666;</i></div>'
        __html += '<div class="action_title">提现申请</div>'
        __html += '<div class="subject pb20 withdraw_apply" style="">'
        __html += '<div style="text-align:left;margin-bottom:0.5em;font-size:1.3em">'
        __html += '<p><label>可提余额</label><span>'+withdrawal_total+'</span></p>'
        __html += '<p><label>提现金额</label><span class="bold fs1_2">'+withdrawal_total+'</span>'
        __html += '<span class="withdraw_apply_explain">暂时只支持全额提现</span></p>'
        __html += '<p><label>提现方式</label>'
        __html += '<select name="pwithdrawal_id">'
        __html += '<option value="-1">请选择一种提现方式</option>';
        if(Number(all_withdrawal.bag.withdrawal_bag) == 1) {
            if(all_withdrawal.bag.withdrawal_full>0 && all_withdrawal.bag.withdrawal_plus>0) {
                __html += '<option value="0">提现到钱包(满'+all_withdrawal.bag.withdrawal_full+'送'+all_withdrawal.bag.withdrawal_plus+'，上不封顶)</option>';
            }
        }
        var withdrawal_type_cn = '';
        $.each(all_withdrawal.third,function(k,v) {
            switch(v.withdrawal_type) {
                case 'alipay':
                    withdrawal_type_cn = '支付宝';
                    break;
                default:
                    break;
            }
            __html += '<option value="'+v.pwithdrawal_id+'">提现到'+withdrawal_type_cn+'('+v.withdrawal_account+')</option>';
        });
        __html += '</select>'
        __html += '</p>'
        __html += '<p><label for="password">提现密码</label>'
        __html += '<input type="password" id="password" name="password" style="" placeholder="请输入你的密码">'
        __html += '</p>'
        __html += '</div>'
        __html += '</div>'
        __html += '<div class="subject pb10">'
        __html += '<button id="btn_confirm_withdraw" class="btn" style="width:100%">确认提现</button>'
        __html += '</div>'
        __html += '<a href="?m=account&a=withdrawal" class="btn btn_secondary" style="width:100%">还没有提现方式？立即去添加！</button>'
        __html += '<br/>'
        __html += '</div>'
        __html += '</div>'
    }
    if(type=="btn_add_withdrawal") {
        __html += '<div id="popwin">'
        __html += '<div class="inbox" style="position:relative;top:0;">'
        __html += '<div class="status_icon" style="padding:0 0 10px 0;"><i class="iconfont rotateZ360">&#xe667;</i></div>'
        __html += '<div class="action_title">提现方式</div>'
        __html += '<div class="subject pb20 withdraw_apply" style="">'
        __html += '<div style="text-align:left;margin-bottom:0.5em;font-size:1.3em">'
        __html += '<p><label>提现方式</label>'
        __html += '<select name="withdrawal_type" id="withdrawal_type">'
        __html += '<option value="alipay">支付宝</option>'
        __html += '</select>'
        __html += '</p>'
        __html += '<p><label for="password">提现账户</label>'
        __html += '<input type="text" id="withdrawal_account" name="withdrawal_account" placeholder="请填写你提现方式的账户">'
        __html += '</p>'
        __html += '<p><label for="password">提现姓名</label>'
        __html += '<input type="text" id="withdrawal_name" name="withdrawal_name" placeholder="请填写你的姓名">'
        __html += '</p>'
        __html += '</div>'
        __html += '</div>'
        __html += '<div class="subject pb30">'
        __html += '<button id="btn_confirm_withdrawal" class="btn" style="width:100%">确认添加</button>'
        __html += '</div>'
        __html += '<br/>'
        __html += '</div>'
        __html += '</div>'
    }
    if(type=="click_to_update_withdrawal") {
        __html += '<div id="popwin">'
        __html += '<div class="inbox" style="position:relative;top:0;">'
        __html += '<div class="status_icon" style="padding:0 0 10px 0;"><i class="iconfont rotateZ360">&#xe667;</i></div>'
        __html += '<div class="action_title">提现方式</div>'
        __html += '<div class="subject pb20  withdraw_apply update_withdrawal" style="">'
        __html += '<div style="text-align:left;margin-bottom:0.5em;font-size:1.3em">'
        __html += '<p><label>提现方式</label>'
        __html += '<select name="withdrawal_type" >'
        __html += '<option value="alipay">支付宝</option>'
        __html += '</select>'
        __html += '</p>'
        __html += '<p class="relative"><label for="password">提现账户</label>'
        __html += '<input type="text" name="withdrawal_account" placeholder="请填写你提现方式的账户" value="'+data.withdrawal_account+'" onfocus="show_close(this);">'
        __html += '<i class="iconfont icon-close2" onclick="clear_input(this);">&#xe66d;</i>'
        __html += '</p>'
        __html += '<p class="relative"><label for="password">提现姓名</label>'
        __html += '<input type="text" name="withdrawal_name" placeholder="请填写你的姓名" value="'+data.withdrawal_name+'" onfocus="show_close(this);">'
        __html += '<i class="iconfont icon-close2" onclick="clear_input(this);">&#xe66d;</i>'
        __html += '</p>'
        __html += '</div>'
        __html += '</div>'
        __html += '<div class="subject pb30">'
        __html += '<button id="btn_update_withdrawal" data-id="'+data.pwithdrawal_id+'" class="btn" style="width:100%">确认修改</button>'
        __html += '</div>'
        __html += '<br/>'
        __html += '</div>'
        __html += '</div>'
    }
    if(type=="remark_order") {
        __html += '<div id="popwin">'
        __html += '<div class="inbox">'
        __html += '<div class="status_icon" style="padding:0 0 30px 0;"><i class="iconfont rotateZ360">&#xe606;</i></div>'
        __html += '<div class="action_title">订单备注</div>'
        __html += '<div class="subject pb10">'
        __html += '<div class="subject pb10 remark_order" style="width:85%;margin:0 auto;">'
        __html += '<div style="text-align:left;margin-bottom:1em;font-size:1.5em">单号:'+data.order_sn+'</div>'
        __html += '<div style="text-align:left;font-size:1.5em">'
        __html += '标记:'
        if(data.flag_color == 'd2ad5d') {
            __html += '<label><input type="radio" name="flag_color" id="flag_d2ad5d" value="d2ad5d" checked="checked"><i class="iconfont btn_remark" style="color:#d2ad5d;font-size:1.5em;">&#xe64f;</i></label>'

        }else {
            __html += '<label><input type="radio" name="flag_color" id="flag_d2ad5d" value="d2ad5d"><i class="iconfont btn_remark" style="color:#d2ad5d;font-size:1.5em;">&#xe64f;</i></label>'
        }
        if(data.flag_color == '00bb9c') {
            __html += '<label><input type="radio" name="flag_color" id="flag_00bb9c" value="00bb9c" checked="checked"><i class="iconfont btn_remark" style="color:#00bb9c;font-size:1.5em;">&#xe64f;</i></label>'
        }else {
            __html += '<label><input type="radio" name="flag_color" id="flag_00bb9c" value="00bb9c"><i class="iconfont btn_remark" style="color:#00bb9c;font-size:1.5em;">&#xe64f;</i></label>'
        }
        if(data.flag_color == '476ee9') {
            __html += '<label><input type="radio" name="flag_color" id="flag_476ee9" value="476ee9" checked="checked"><i class="iconfont btn_remark" style="color:#476ee9;font-size:1.5em;">&#xe64f;</i></label>'
        }else {
            __html += '<label><input type="radio" name="flag_color" id="flag_476ee9" value="476ee9"><i class="iconfont btn_remark" style="color:#476ee9;font-size:1.5em;">&#xe64f;</i></label>'
        }
        if(data.flag_color == 'eb4f38') {
            __html += '<label><input type="radio" name="flag_color" id="flag_eb4f38" value="eb4f38" checked="checked"><i class="iconfont btn_remark" style="color:#eb4f38;font-size:1.5em;">&#xe64f;</i></label>'
 
        }else {
            __html += '<label><input type="radio" name="flag_color" id="flag_eb4f38" value="eb4f38"><i class="iconfont btn_remark" style="color:#eb4f38;font-size:1.5em;">&#xe64f;</i></label>'
        }
        __html += '</div>'
        __html += '</div>'
        __html += '</div>'
        __html += '<div class="subject pb20">'
        __html += '<textarea class="content" id="textarea_remark" name="content" rows="3" placeholder="备注内容">'+data.remark+'</textarea>'
        __html += '<input type="hidden" id="order_id" />'
        __html += '</div>'
        __html += '<div class="subject pb20">'
        __html += '<button id="btn_remark_order" class="btn_normal" style="width:85%">提交备注</button>'
        __html += '</div>'
        __html += '<br/>'
        __html += '</div>'
        __html += '</div>'

    }
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

    return __html;
}


function search_orders(obj) {
    var search_type = $('#search_type').val();
    var search_keywords = $('#search_keywords').val();
    window.location.href="?m=account&a=order&s="+search_type+"&k="+search_keywords;
}

function btn_search_promote(obj) {
    var search_category = $('#search_category').val();
    var search_keywords = $('#search_keywords').val();
    window.location.href="?m=account&a=promote&category="+search_category+"&keywords="+search_keywords;
}

function btn_search_promote_category(obj) {
    var search_keywords = $('#search_keywords').val();
    window.location.href="?m=account&a=promote&c=pc&keywords="+search_keywords;
}

function btn_search_promotion(obj) {
    var search_category = $('#search_category').val();
    var search_keywords = $('#search_keywords').val();
    window.location.href="?m=account&a=promotion&category="+search_category+"&keywords="+search_keywords;
}

function btn_confirm_withdraw(obj) {
    var pwithdrawal_id = $('.withdraw_apply select').val();
    var password = $('.withdraw_apply #password').val();
    if(password == '') {
        layer.open({content:'请输入密码',time:1});
        $('.withdraw_apply #password').focus();
        $(obj).html('确定提现');
        _working = false;
        return false;
    }
    if(pwithdrawal_id == '' || pwithdrawal_id == -1) {
        layer.open({content:'请选择一种提现方式',time:1});
        $(obj).html('确定提现');
        _working = false;
        return false;
    }
    var re = /["']+/g;
    if(password.search(re) != -1) {
        layer.open({content:'请不要使用非法字符',time:1});
        $('.withdraw_apply #password').focus();
        $(obj).html('确定提现');
        _working = false;
        return false;
    }
    $.getJSON('/ajax/withdraw.php',{
        pwithdrawal_id : pwithdrawal_id,
        password : password
    },function(json) {
        switch(json.status) {
            case 'success':
                layer.open({
                    content:json.msg,
                    btn: ['我知道了'],
                    yes: function(index){
                        window.location.href = "?m=account&a=earnings";
                    }  
                });
                break;
            case 'wrong_pass':
                layer.open({content:json.msg,time:1});
                $(obj).html('确定提现');
                _working = false;
                break;
            case 'failed': case 'reload':
                layer.open({content:json.msg,time:2});
                window.location.reload();
            default:
                break;

        }
    });
}
function btn_confirm_withdrawal(obj) {
    var withdrawal_type = $('#withdrawal_type').val();
    var withdrawal_account = $('#withdrawal_account').val();
    var withdrawal_name = $('#withdrawal_name').val();
    if(withdrawal_account == '') {
        layer.open({content:'请输入提现账户！',time:2});
        $('#withdrawal_account').focus();
        $(obj).html('确定添加');
        _working = false;
        return false;
    }
    if(withdrawal_name == '') {
        layer.open({content:'请输入提现姓名！',time:2});
        $('#withdrawal_name').focus();
        $(obj).html('确定添加');
        _working = false;
        return false;
    }
    $.getJSON('/ajax/add_withdrawal.php',{
        withdrawal_account : withdrawal_account,
        withdrawal_name : withdrawal_name,
        withdrawal_type : withdrawal_type
    },function(json) {
        switch(json.status) {
            case 'success':
                layer.open({content:json.msg,time:1});
                window.location.href = "?m=account&a=withdrawal";
                break;
            case 'failed':
                layer.open({content:json.msg,time:2});
                window.location.reload();
            default:
                break;
        }
    }); 
}
function btn_update_withdrawal(obj) {
    var withdrawal_type = $('.update_withdrawal').find('[name=withdrawal_type]').val();
    var withdrawal_account = $('.update_withdrawal').find('[name=withdrawal_account]').val();
    var withdrawal_name = $('.update_withdrawal').find('[name=withdrawal_name]').val();
    var pwithdrawal_id = $(obj).data('id');

    if(withdrawal_account == '') {
        layer.open({content:'请输入提现账户！',time:2});
        $('.update_withdrawal').find('[name=withdrawal_account]').focus();
        $(obj).html('确定修改');
        _working = false;
        return false;
    }
    if(withdrawal_name == '') {
        layer.open({content:'请输入提现姓名！',time:2});
        $('.update_withdrawal').find('[name=withdrawal_name]').focus();
        $(obj).html('确定修改');
        _working = false;
        return false;
    }
    $.getJSON('/ajax/update_withdrawal.php',{
        pwithdrawal_id : pwithdrawal_id,
        withdrawal_account : withdrawal_account,
        withdrawal_name : withdrawal_name,
        withdrawal_type : withdrawal_type
    },function(json) {
        switch(json.status) {
            case 'success':
                layer.open({content:json.msg,time:1});
                window.location.href = "?m=account&a=withdrawal";
                break;
            case 'failed':
                layer.open({content:json.msg,time:2});
                window.location.reload();
            default:
                break;
        }
    }); 
}



function remark_order(order_id,obj) {
    var flag_color = $('.remark_order :checked').val();
    var remark = $('#textarea_remark').val();
    if(flag_color == undefined) {
        layer.open({content : '请选择标记',time : 1});
        $(obj).html("提交备注")
        _working = false
        return false;
    }
    if(remark == '') {
        layer.open({content : '请填写备注',time : 1});
        $(obj).html("提交备注")
        _working = false
        return false;
    }

    $.post('/ajax/set.order.php',{order_id : order_id,flag_color : flag_color,remark : remark},function(data) {
        if(data == 'illegal') {
            layer.open({content : '非法操作',time : 1});
            window.location.reload()
        }
        if(data == 1) {
            layer.open({content : '备注成功'});
            window.location.reload()
        }
        if(data == 0) {
            layer.open({content : '没有数据被修改'});
            window.location.reload()
        }
    });
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
                    //alert("111111")
                    window.setTimeout(function(){
                        window.location.reload()
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
    },function(){})
}

function delete_order(order_id){
    shownotice({
        "icon":"error",
        "title":"不允许删除订单",
        "remark":"抱歉，您不能执行此操作。"
    }, [], function(){
        $("#systemnoticebox .popwinbtnclose").hide()
        window.setTimeout(function(){
            window.location.reload()
        },2000)
    });
    return ;

    var options = [{ "url": "/ajax/order.delete.php", "data":{order_id:order_id}, "type":"POST", "dataType":"json"}]
    Load(options, function(json){
        if(json.status=="success"){
            shownotice(
                {
                    "icon":"success",
                    "title":"成功删除订单",
                    "remark":"您已经成功删除订单。<br/>该窗口2秒后关闭。"
                },
                [],
                function(){
                    $("#systemnoticebox .popwinbtnclose").hide()
                    //alert("111111")
                    window.setTimeout(function(){
                        window.location.reload()
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
                "title":"未能删除订单，错误如下",
                "remark":errorsummary
            },[],function(){
                user.exit()
            })
        }
    },function(){})
}

function tuikuan_order(order_id){
    var options = [{ "url": "/ajax/order.tuikuan.php", "data":{order_id:order_id}, "type":"POST", "dataType":"json"}]
    Load(options, function(json){
        if(json.status=="success"){
            layer.open({
                content: '发送退款请求成功<br>您已经成功发送退款，等待审核后款项将原路退回您的账户。',
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
                })
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
                })
            }

        }
    },function(){})
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
            })
        }
    },function(){})
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

function show_close(obj) {
    $(obj).parent().parent().find('.iconfont').hide();
    $(obj).parent().find('.iconfont').show();
}


function clear_input(obj) {
    $(obj).parent().find('input').val('');
}