var UserModel = {
    createNew: function(){
        var user = {};
        var _html = "";
        var _this = this;
        var _init = true;
        var _action = "by";
        var _current_pannel = ""
        var _working = _reload = false;
        var _oldscene = 0;
        var _callback;
        var _back = _loop = false;
        var _turn = false;
        var _addressjson = {
            receiver_name:"",
            receiver_phone:"",
            address_id:0,
            district_id:0,
            city_id:0,
            state_id:0,
            liid:0
        }
        var _is_state = true;
        var self_url = window.location.href;
        user.name = "";

        user.createpannel = function(){
            var style = "";

            _html =  "<section id='user_pannel'>";
            _html += '<span id="btn_close"><img src="/statics/img/btn.close.png" width="50" /></span>';
            _html += '</section>';

            if($("#user_pannel").length>0){
                $("#user_pannel").remove()
            }
            $("body").append(_html)//.css("overflow","hidden");

            user.render()

            $("#user_pannel #btn_close").click(function(){
                if(!_working){
                    if(_back){
                        user.back();
                        _back = false;
                    }else{
                        user.exit()
                    }
                }
            });
        }

        user.htmlelement=function(){
            var __html =""
            if(_action=="by"){
                __html += '<div id="user_by">'
                __html += '<div class="inbox">'
                __html += '<div class="status_icon"><img src="/statics/img/icon.byuser.png" width="30%" class="rotate360" /></div>'
                __html += '<div class="action_title">登录您的帐户</div>'
                __html += '<div class="subject pb10 icon-relative" style="margin:0 auto;">'
                __html += '<i class="iconfont icon-font">&#xe642</i><input type="text" id="account" class="ex-text icon-phone" placeholder="手机号码/邮箱" /><i class="iconfont icon-close">&#xe639;</i>'
                __html += '</div>'
                __html += '<div class="subject pb10 icon-relative login-msg" style="display:none;">'
                __html += '<i class="iconfont icon-font">&#xe644;</i><input type="text" id="login-phone-code" class="ex-text ex-text-short" placeholder="短信验证码" /><i class="iconfont icon-close incon-close-short">&#xe639;</i>'
                __html += '<button class="button_get_code" onclick="get_code(this,2)">获取验证码</button>' 
                __html += '</div>'
                __html += '<div class="subject pb10 icon-relative login-pass">'
                __html += '<i class="iconfont icon-font">&#xe643</i><input type="password" id="password" class="ex-text" placeholder="登录密码" /><i class="iconfont icon-close">&#xe639;</i>'
                __html += '</div>'
                __html += '<div class="subject pb20">'
                __html += '<button id="btn_bynow" class="btn_normal ex-btn">登录</button>'
                __html += '</div>'
                __html += '<div class="subject pb20 ex-horse">'
                __html += '<a id="btn_regnow" class=" ex-horse-left">注册帐户</a>'
                __html += '<a class="horse-login-msg ex-horse-right">短信登录</a>'
                __html += '<a class="horse-login-pass ex-horse-right">普通登录</a>'
                __html += '</div>'
                __html += '<div class="other-login">'
                __html += '<div class="o-text">'
                __html += '<span>其他登录方式</span>'
                __html += '</div>'
                __html += '<a href="/?m=login&a=qq&uri='+escape(self_url)+'"> <i class="iconfont ico-qq"></i> </a>'
                __html += '<a href="https://api.weibo.com/oauth2/authorize?client_id=2250175595&response_type=code&redirect_uri=http://www.25boy.cn/weibo.callback.php?return_to_url='+escape(self_url)+'" id="cart_user_weibo"> <i class="iconfont ico-weibo"></i> </a>'
                __html += '</div>'
                __html += '</div>'
                __html += '</div>'
            }
            if(_action=="unby"){
                __html += '<div id="user_unby">'
                __html += '<div class="inbox">'
                __html += '<div class="status_icon"><img src="/statics/img/icon.status.nologin.grey.png" width="30%" class="rotate360" /></div>'
                __html += '<div class="action_title">解绑您的帐户</div>'
                // __html += '<div class="subject pb30">'
                // __html += '<input type="password" id="password" class="input_normal" placeholder="请输入登录密码" />'
                // __html += '</div>'
                __html += '<div class="subject pb40">'
                __html += '<button id="btn_unbynow" class="btn_normal">解除绑定</button>'
                __html += '</div>'
                __html += '<br/>'
                __html += '</div>'
                __html += '</div>'
            }                               
            if(_action=="signup"){
                __html += '<div id="user_signup">'
                __html += '<div class="inbox">'
                __html += '<div class="status_icon"><img src="/statics/img/icon.reg.png" width="30%" class="rotateZ360" /></div>'
                __html += '<div class="action_title">创建新账户</div>'
                __html += '<div class="subject pb10 icon-relative">'
                __html += '<i class="iconfont icon-font">&#xe642</i><input type="text" id="phone" class="ex-text icon-phone" placeholder="请输入手机号码" /><i class="iconfont icon-close">&#xe639;</i>'  
                __html += '</div>'
                __html += '<div class="subject pb10 icon-relative">'
                __html += '<i class="iconfont icon-font">&#xe644;</i><input type="text" id="phone_code" class="ex-text ex-text-short" placeholder="请输入验证码" /><i class="iconfont icon-close incon-close-short">&#xe639;</i>'
                __html += '<button class="button_get_code" onclick="get_code(this,1)">获取验证码</button>' 
                __html += '</div>'
                __html += '<div class="subject pb10 icon-relative">'
                __html += '<i class="iconfont icon-font">&#xe645;</i><input type="text" id="nickname" class="ex-text" placeholder="请输入呢称,不可少于4位" /><i class="iconfont icon-close">&#xe639;</i>'
                __html += '</div>'
                __html += '<div class="subject pb10 icon-relative">'
                __html += '<i class="iconfont icon-font">&#xe643</i><input type="password" id="password" class="ex-text" placeholder="请输入密码,不可少于6位" /><i class="iconfont icon-eye">&#xe646;</i>'
                __html += '</div>'
                __html += '<div class="subject pb20">'
                __html += '<button id="btn_signup" class="btn_normal ex-btn">创建账户</button>'
                __html += '</div>'
                __html += '<div class="subject pb10">'
                __html += '<a id="back_login" >&lt;&lt; 我有帐号，返回登录</a>'
                __html += '</div>'
                __html += '</div>'
                __html += '</div>'
            }
            if(_action=="reenterpassword"){
                __html += '<div id="user_reenterpassword">'
                __html += '<div class="inbox">'
                __html += '<div class="status_icon"><img src="/statics/img/icon.key.png" width="30%" class="rotateZ360" /></div>'
                __html += '<div class="action_title">认证您的支付操作！</div>'

                __html += '<div class="subject pb10" style="font-size: 14px;line-height: 1.5em;">使用余额或者合并付款，若该订单需要退换货，<br>退款将退回25BOY帐户余额，不能提现与转赠，请知晓。</div>'
                __html += '<div class="subject pb30">'
                __html += '<input type="password" id="password" class="input_normal" placeholder="请输入帐户密码" />'
                __html += '</div>'
                __html += '<div class="subject pb40">'
                __html += '<button id="btn_auth" class="btn_normal">确定余额支付</button>'
                __html += '</div>'
                __html += '<br/>'
                __html += '</div>'
                __html += '</div>'
            }
            if(_action=="address_add"){
                __html += '<div id="user_address_add">'
                __html += '<div class="inbox">'
                __html += '<div class="status_icon" style="padding:0 0 30px 0;"><img src="/statics/img/icon.address.png" width="25%" class="rotateZ360" /></div>'
                __html += '<div class="action_title">添加收货地址</div>'

                __html += '<div class="subject pb10">'
                __html += '<input type="text" id="receiver_name" class="input_normal" placeholder="收货人姓名" />'
                __html += '</div>'

                __html += '<div class="subject pb10" id="address_select_box">'
                __html += '<img src="/statics/img/loader.gif" height="30" />'
                __html += '</div>'

                __html += '<div class="subject pb10">'
                __html += '<input type="text" id="address" class="input_normal" placeholder="详细地址" />'
                __html += '<input type="hidden" id="city_id" class="input_normal" />'
                __html += '<input type="hidden" id="district_id" class="input_normal" />'
                __html += '<input type="hidden" id="state_id" class="input_normal" />'
                __html += '</div>'

                __html += '<div class="subject pb20">'
                __html += '<input type="text" id="receiver_phone" class="input_normal" placeholder="联系电话" />'
                __html += '</div>'

                __html += '<div class="subject pb10">'
                __html += '<button id="btn_add_address" class="btn_normal">确定添加</button>'
                __html += '</div>'
                __html += '<br/>'
                __html += '</div>'
                __html += '</div>'
            }

            if(_action=="address_modify"){

                //_addressjson = {
                //    receiver_name:$(this).parent().parent().attr("receiver_name"),
                //    receiver_phone:$(this).parent().parent().attr("receiver_phone"),
                //    address_id:$(this).parent().parent().attr("address_id"),
                //    district_id:$(this).parent().parent().attr("district_id"),
                //    city_id:$(this).parent().parent().attr("city_id"),
                //    state_id:$(this).parent().parent().attr("state_id")
                //}

                __html += '<div id="user_address_modify">'
                __html += '<div class="inbox">'
                __html += '<div class="status_icon" style="padding:0 0 30px 0;"><img src="/statics/img/icon.address.png" width="25%" class="rotateZ360" /></div>'
                __html += '<div class="action_title">修改收货地址</div>'

                __html += '<div class="subject pb10">'
                __html += '<input type="text" id="receiver_name" value="'+_addressjson.receiver_name+'" class="input_normal" placeholder="收货人姓名" />'
                __html += '</div>'

                __html += '<div class="subject pb10" id="address_select_box">'
                __html += '<img src="/statics/img/loader.gif" height="30" />'
                __html += '</div>'

                __html += '<div class="subject pb10">'
                __html += '<input type="text" id="address" class="input_normal" value="'+_addressjson.address+'" placeholder="详细地址" />'
                __html += '<input type="hidden" id="city_id" value="'+_addressjson.city_id+'" class="input_normal" />'
                __html += '<input type="hidden" id="district_id" value="'+_addressjson.district_id+'" class="input_normal" />'
                __html += '<input type="hidden" id="state_id" value="'+_addressjson.state_id+'" class="input_normal" />'
                __html += '</div>'

                __html += '<div class="subject pb20">'
                __html += '<input type="text" id="receiver_phone" value="'+_addressjson.receiver_phone+'"  class="input_normal" placeholder="联系电话" />'
                __html += '</div>'

                __html += '<div class="subject pb10">'
                __html += '<button id="btn_save_address" address_id="'+_addressjson.address_id+'" liid="'+_addressjson.liid+'"  class="btn_normal">保存</button>'
                __html += '</div>'
                __html += '<br/>'
                __html += '</div>'
                __html += '</div>'
            }

            if(_action=="address_list"){
                __html += '<div id="user_address_list">';
                if(current_model=='order'){
                    var address_tips_txt = '点击选择收货地址';
                }else{
                    var address_tips_txt = '点击设为默认地址';
                }
                __html += '<p class="tips">'+address_tips_txt+'</p>' +
                    '<ul>' +
                    '<div style="text-align:center;padding-top:80px;"><img src="/statics/img/loader.gif" /></div>' +
                    '</ul>' +
                    '<center style="position:absolute; bottom:25px; left:0; width:100%;">' +
                    '<a href="javascript:;" class="btn btn_mini" id="in_btn_add_address">添加收货地址</a>' +
                    '</center> ' +
                    '<img src="/statics/img/loader.gif"/>' +
                    '</div>'
            }


            if(_action=="product_list"){
                __html += '<div id="user_product_list">' +
                    '<ul>'

                $("#temp_product_list ul li").each(function(){
                    var obj = $(this)
                    __html += '<li>'+
                        '<div class="productitembox">'+
                        '<div class="imgbox"><img src="'+obj.find("span.thumb").attr("data-src")+'"></div>'+
                        '<div class="detailbox">'+
                        '<div class="product_name_box">'
                    if(obj.find("span.ship_free").html()=="1"){
                    __html += '<span class="ship">包邮</span>'
                    }

                    __html += obj.find("span.product_name").html()+
                        '</div>'+
                        '<div class="prop_box">'+obj.find("span.color_prop").html()+'，'+obj.find("span.size_prop").html()+'</div>'+
                        '<div class="price_box"><sup>￥</sup>'+obj.find("span.price").html()+ '</div>'+
                        '<div class="quantity_box">数量：'+obj.find("span.quantity").html()+'</div>'+
                        '</div>'+
                        '</div>'+
                        '</li>'
                })
                __html += '</ul></div>'
            }

            if(_action=="coupon_list"){

                __html += '<div id="user_coupon_list">'+
                        '<div class="loader"></div>'+
                        '<ul>';
                if(coupon_json=="" || coupon_json==undefined){
                    __html += '<li class="notice-tips">暂无可用代金券。</li>';
                }else{
                    $.each(coupon_json,function(i,obj){
                        if(hasevents=='yes' && obj['coupon_type']=='C'){
                            __html += '<li class="notice-tips">提示：不可用的代金券不会显示。</li>';
                        }else{
                            __html += '<li onclick="use_coupon('+obj['coupon_id']+')">'+
                                '<div class="couponitembox">'+
                                '<i class="iconfont">&#xe605;</i>'+
                                '<div class="coupon_name_box">'+obj['coupon_title']+'</div>'+
                                '<div class="intro_box">';
                                if(obj['not_top']==1){
                                    __html += '每';
                                }
                                __html += '满 '+obj['price_limit']+' 减 '+obj['coupon_price']+'</div>'+
                                '<div class="expdate_box">有效期至：'+obj['exp_date']+'</div>'+
                                '</div>'+
                                '</li>';
                        }
                    });
                }
                __html += '</ul></div>'
            }

            if(_action=="password_change"){
                __html += '<div id="user_password_change">'
                __html += '<div class="inbox">'
                __html += '<div class="status_icon" style="padding:30px 0;"><img src="/statics/img/icon.lock.png" width="30%" class="rotateZ360" /></div>'
                __html += '<div class="action_title">更改您的登录密码</div>'

                /*__html += '<div class="subject pb20">'
                __html += '<input type="password" id="oldpassword" class="input_normal" placeholder="请输入原始密码" />'
                __html += '</div>'*/
                __html += '<div class="subject pb20">'
                __html += '<input type="password" id="password" class="input_normal" placeholder="请输入新登录密码" />'
                __html += '</div>'
                __html += '<div class="subject pb20">'
                __html += '<input type="password" id="repassword" class="input_normal" placeholder="请再次输入新登录密码" />'
                __html += '</div>'
                __html += '<div class="subject pb40">'
                __html += '<button id="btn_changepassword" class="btn_normal">更改密码</button>'
                __html += '</div>'
                __html += '</div>'
                __html += '</div>'
            }
            return __html
        }

        user.render = function(){

            _html = user.htmlelement()
            if(!_back){
                if(_init){
                    _current_pannel = _action;

                    $(".main").removeClass("zoom_out").addClass("zoom_in");
                    $("#user_pannel").append(_html)
                    $("#user_"+_action).css({"transform":"translateX(0)","z-index":1});
                    user.eventup()
                    $("#user_pannel").removeClass("left_to_right").addClass("right_to_left");
                    _init = false;
                }else{
                    // console.log("333333")
                    if(_oldscene!=0){
                        $("#user_"+_oldscene).remove();
                    }
                    $("#user_pannel").append(_html)
                    $("#user_"+_action).addClass("right_to_left")
                    $("#user_"+_current_pannel).addClass("zoom_in")
                    user.eventup()
                    _oldscene = _current_pannel;
                    _current_pannel = _action;

                }

                if(_action=="address_add"){
                    user.get_area("")
                }
                if(_action=="address_modify"){
                    user.get_area("")
                }
                if(_action=="address_list"){
                    user.get_address()
                }
            }else{

            }
        };
        user.get_address = function(){
            var options = [{ "url": "/ajax/get.address.list.php", "type":"GET", "dataType":"json"}]
            Load(options, function(json) {
                var _temp = ""
                // console.log(json)
                $("#user_address_list img").remove()

                if(json.list.length>4){
                    $("#user_address_list a").hide();
                }

                if(json.list.length>0){
                    $(json.list).each(function(i,obj){

                        _temp += '<li address="'+obj.address+'" receiver_name="'+obj.receiver_name+'" receiver_phone="'+obj.receiver_phone+'" address_id="'+obj.address_id+'" city_name="'+obj.city_name+'" state_name="'+obj.state_name+'" district_name="'+obj.district_name+'"   district_id="'+obj.district_id+'" city_id="'+obj.city_id+'" state_id="'+obj.state_id+'">' +
                        '<div class="default_address">' +
                        '<div class="row"><strong>'+obj.receiver_name+'</strong></div>' +
                        '<div class="row">'+obj.receiver_phone+'</div>' +
                        '<div class="row">'+obj.full_address+'</div>' +
                                '<a class="modify_btn"><img src="/statics/img/icon.edit.png" width="100%" /></a>' +
                            '<a class="delete_btn"><img src="/statics/img/icon.deleteorder.png" width="100%" /></a>' +
                        '</div>' +
                        '</li>'
                    })

                }
                $("#user_address_list ul").html(_temp);
                user.address_list_event()
            },function(){

            });
        }
        user.get_area = function(path){
            // console.log(path);
            var state = 'N';
            if(_is_state){
                state = 'Y';
            }
            var options = [{ "url": "/ajax/get.area.php", "data":{path:path,state:state}, "type":"GET", "dataType":"json"}]
            Load(options, function(json) {
                user.set_area(json)
            },function(){

            });
        };

        user.set_area = function(json){
            // console.log(json);
            var selectobj;
            var _temphtml = "";
            var _param = "";
            if(json.data.length == 0){
                return false;
            }

            if(json.depth==0){
                _temphtml = "<select id='state_box'><option value='0'>请选择省份</option>";
                $(json.data).each(function(i,obj){
                    if(_addressjson.state_id!=obj.area_id){
                        _temphtml += "<option rel='"+i+"' value='"+obj.area_id+"'>"+obj.areaname+"</option>";
                    }else{
                        _param = i
                        _temphtml += "<option rel='"+i+"' value='"+obj.area_id+"' selected>"+obj.areaname+"</option>";
                        _addressjson.state_id = 0
                    }
                })
                _temphtml += "</select>";
                $("#address_select_box").html(_temphtml)
                $("#state_box").change(function(){
                    //console.log($(this).children('option:selected').attr("rel"));
                    selectobj = $(this).children('option:selected');
                    $("#district_box").remove()
                    $("#city_box").remove()
                    $("input#city_id").val("0");
                    $("input#district_id").val("0");
                    $("input#state_id").val(selectobj.val());
                    if(selectobj.val()>0){
                        user.get_area(selectobj.attr("rel"))
                        $("input#state_id").attr("rel",selectobj.attr("rel"));
                    }
                })
                if(_addressjson.city_id!=0){
                    $("input#state_id").attr("rel",_param);
                    _is_state = false;
                    user.get_area(_param)
                }
            }
            if(json.depth==1){
                $("#city_box").remove()
                _temphtml = "<select id='city_box'><option value='0'>请选择城市</option>";
                $(json.data).each(function(i,obj){
                    if(_addressjson.city_id!=obj.area_id) {
                        _temphtml += "<option rel='" + i + "' value='" + obj.area_id + "'>" + obj.areaname + "</option>";
                    }else{
                        _param = $("input#state_id").attr("rel")+","+i

                        _temphtml += "<option rel='" + i + "' value='" + obj.area_id + "' selected>" + obj.areaname + "</option>";
                        _addressjson.city_id = 0
                    }
                })
                _temphtml += "</select>";
                $("#address_select_box").append(_temphtml)
                $("#city_box").select()
                $("#city_box").change(function(){
                    //console.log($(this).children('option:selected').attr("rel"));
                    selectobj = $(this).children('option:selected');
                    $("#district_box").remove()
                    $("input#city_id").val(selectobj.val());
                    $("input#district_id").val("0");
                    if(selectobj.val()>0){
                        user.get_area($("input#state_id").attr("rel")+","+selectobj.attr("rel"));
                        $("input#city_id").attr("rel",selectobj.attr("rel"));
                    }
                })
                // console.log(_param)
                if(_addressjson.district_id!=0){
                    _is_state = false;
                    user.get_area(_param)
                }
            }
            if(json.depth==2){
                $("#district_box").remove()
                _temphtml = "<select id='district_box'><option value='0'>请选择区域</option>";
                $(json.data).each(function(i,obj){
                    if(_addressjson.district_id!=obj.area_id) {
                        _temphtml += "<option rel='" + i + "' value='" + obj.area_id + "'>" + obj.areaname + "</option>";
                    }else{
                        _temphtml += "<option rel='" + i + "' value='" + obj.area_id + "' selected>" + obj.areaname + "</option>";
                        _addressjson.district_id = 0
                    }
                })
                _temphtml += "</select>";
                $("#address_select_box").append(_temphtml)
                $("#district_box").select()
                $("#district_box").change(function(){
                    //console.log($(this).children('option:selected').attr("rel"));
                    selectobj = $(this).children('option:selected');
                    //if(selectobj.val()>0){
                    $("input#district_id").val(selectobj.val());
                    //}
                })
            }
        }


        user.show=function(action,callback){
            _action = action;
            $("#systemnoticebox").remove()
            user.createpannel()
            _turn = true
            _callback=callback
        }

        user.eventup=function(){
            $("#user_pannel #user_password_change #btn_changepassword").unbind("click")
            $("#user_pannel #user_unby #btn_unbynow").unbind("click")
            $("#user_pannel #user_address_list #in_btn_add_address").unbind("click")
            $("#btn_save_address").unbind("click")
            $("#user_pannel #user_address_add #btn_add_address").unbind("click")
            $("#user_pannel #user_by #btn_regnow").unbind("click")
            $("#user_pannel #user_reenterpassword #btn_auth").unbind("click")
            $("#user_pannel #user_signup #btn_signup").unbind("click")
            $("#user_pannel #user_by #btn_bynow").unbind("click")
            /*密码开显功能*/
            $('.icon-eye').click(function() {
                if($(this).prev().attr('type') == 'password') {
                    $(this).css('color','#333').prev().attr('type','text');
                }else {
                   $(this).css('color','#bbb').prev().attr('type','password');
                }

            });


            $('.icon-close').click(function() {
                $(this).hide().prev().val('').focus();

            });
            $('.ex-text').keyup(function() {
                if($(this).val() == '') {
                    $(this).next().hide();
                }else {
                      $(this).next().show();

                    }
            });
             //转换登录方式
            $('.ex-horse-right').click(function() {
                if($(this).hasClass('horse-login-msg')) {
                    //以短信方式登录
                    $('.login-pass').find('input').val('').keyup(); //清空输入框数据
                    $('#account').attr('placeholder','手机号码');
                    $('.login-msg').slideToggle();
                    $('.login-pass').slideToggle();
                    $('.horse-login-pass').show();
                    $('.horse-login-msg').hide();
                }else {
                    //以密码方式登录
                    $('.login-msg').find('input').val('').keyup(); //清空输入框数据
                    $('#account').attr('placeholder','手机号码/邮箱').css('border','1px solid #ccc');
                    $('.login-msg').slideToggle();
                    $('.login-pass').slideToggle();
                    $('.horse-login-msg').show();
                    $('.horse-login-pass').hide();
                }
            });


            //马上去注册
            $("#user_pannel #user_by #btn_regnow,#btn_regnow2").click(function(){
                if(!_working) {
                    user.delete_ani_class()
                    _action = "signup"
                    user.render()
                }
            })
            //返回登录
            $("#user_pannel #back_login").click(function(){
                if(!_working) {
                   user.delete_ani_class()
                   _action = "by"
                    user.render()
                }
            })

            $("#user_pannel #user_signup #btn_signup").click(function(){
                if(!_working){
                    user.check_signup()
                }
            })
            $("#user_pannel #user_by #btn_bynow").click(function(){

                 if(!_working){
                    user.check_signin()
                }
            })

            $("#user_pannel #user_unby #btn_unbynow").click(function(){
                if(!_working){
                    user.check_unby()
                }
            })
            $("#user_pannel #user_password_change #btn_changepassword").click(function(){
                if(!_working){
                    user.check_changepassword()
                }
            })
            $("#user_pannel #user_reenterpassword #btn_auth").click(function(){
                if(!_working){
                    user.check_pass()
                }
            })
            $("#user_pannel #user_address_add #btn_add_address").click(function(){
                if(!_working){
                    user.add_address()
                }
            })
            $("#btn_save_address").click(function(){
                if(!_working){
                    user.save_address($(this).attr("address_id"),$(this).attr("liid"))
                }
            })

            $("#user_pannel #user_address_list #in_btn_add_address").click(function(){
                // console.log(_loop)
                _is_state = true;
                _action = "address_add"
                if(!_working){
                    if(!_loop){
                        user.delete_ani_class()
                        _action = "address_add"
                        user.render();
                        _loop = true
                    }else{
                        _current_pannel = "address_add"
                        _html = user.htmlelement()
                        $("#user_pannel").append(_html)
                        user.get_area("")
                        $("#user_address_add").removeClass("left_to_right").addClass("right_to_left");
                        $("#user_address_list").removeClass("zoom_out").addClass("zoom_in");
                        $("#btn_add_address").click(function(){
                            if(!_working){
                                user.add_address()
                            }
                        }) 
                    }
                    _back = true
                }
                $('#user_address_modify').remove();
            })
            keyboardFix()
        }


        user.add_address=function(){

            var state_id = $("input#state_id").val()
            var city_id = $("input#city_id").val()
            var district_id = $("input#district_id").val()
            var receiver_name = $("input#receiver_name").val()
            var address = $("input#address").val()
            var receiver_phone = $("input#receiver_phone").val()

            var errorcode="";
            if(receiver_name==""||receiver_name==0){
                errorcode += "没有收货人姓名；<br/>"
            }
            if(state_id==""||state_id==0){
                errorcode += "省份没有选择；<br/>"
            }
            if(city_id==""||city_id==0){
                errorcode += "城市没有选择；<br/>"
            }
            /*if(district_id==""||district_id==0){
                errorcode += "区域没有选择；<br/>"
            }*/
            if(address==""||address==0){
                errorcode += "详细地址没有输入；<br/>"
            }
            if(receiver_phone==""||receiver_phone==0){
                errorcode += "联系电话没有输入；<br/>"
            }

            if(errorcode!=""){
                shownotice({
                    "icon":"notice",
                    "title":"输入有误，错误如下",
                    "remark":errorcode
                },[])
                return;
            }
            $("#user_pannel #user_address_add #btn_add_address").html("<img src='/statics/img/loader2.gif' style='display:block;' />")

            var postdata = {
                "state_id":state_id,
                "city_id":city_id,
                "district_id":district_id,
                "address":address,
                "receiver_phone":receiver_phone,
                "receiver_name":receiver_name
            }

            //console.log(postdata);

            var options = [{ "url": "/ajax/address.add.php", "data":postdata, "type":"POST", "dataType":"json"}]
            Load(options, function(json) {
                if (!user.isJson(json)) {
                    shownotice({
                        "icon": "error",
                        "title": "出错了",
                        "remark": "没有原因，因为找不到！"
                    }, [])
                    _working = false
                    $("#user_pannel #user_address_add #btn_add_address").html("确定添加")
                } else {
                    if(json.status=="success"){
                        $("#user_pannel #user_address_add #btn_add_address").html("添加成功")
                       
                        _turn = false;/*强制它插入地址列表*/
                        //判断是否直接显示在原始页面显示
                        if(_turn==true){
                            //插入页面
                            _callback(json)
                            $("#systemnoticebox .popwinbtnclose").hide()
                            user.exit()
                        }else{
                            //插入地址列表；
                            var _temp = ""
                            _temp += '<li address_id="'+json.address_id+'" city_name="'+json.city_name+'" state_name="'+json.state_name+'" district_name="'+json.district_name+'"  district_id="'+json.district_id+'" city_id="'+json.city_id+'" state_id="'+json.state_id+'">' +
                                '<div class="default_address">' +
                                '<div class="row"><strong>'+json.receiver_name+'</strong></div>' +
                                '<div class="row">'+json.receiver_phone+'</div>' +
                                '<div class="row">'+json.full_address+'</div>' +
                                '</div>' +
                                '</li>'
                            $("#user_address_list ul").append(_temp);

                            user.address_list_event()

                            if($("#user_address_list ul li").length>4){
                                $("#in_btn_add_address").hide()
                            }
                            user.back();
                            _back = false;
                        }

                    }else{
                        switch(json.status){
                            case "error":
                                shownotice({
                                    "icon":"error",
                                    "title":"出错了",
                                    "remark":"你可能没有登录，请刷新再试！"
                                },[])
                                break;
                            case "noquota":
                                shownotice({
                                    "icon":"error",
                                    "title":"配额不足",
                                    "remark":"您最多可以存储5个地址！"
                                },[])
                                break;
                            case "error":
                                shownotice({
                                    "icon":"error",
                                    "title":"抱歉，出错了",
                                    "remark":"请刷新页面重试！"
                                },[])
                                break;
                            case "empty":
                                shownotice({
                                    "icon":"error",
                                    "title":"数据输入不完整",
                                    "remark":"没有输入数据或不完整，请检查输入！"
                                },[])
                                break;
                        }
                        _working = false
                        $("#user_pannel #user_address_add #btn_add_address").html("确定添加")
                    }
                }
            },function(){
                _working = false
                $("#user_pannel #user_address_add #btn_add_address").html("确定添加")
            })
        }
        user.save_address=function(address_id,liid){
            var state_id = $("input#state_id").val()
            var city_id = $("input#city_id").val()
            var district_id = $("input#district_id").val()
            var receiver_name = $("input#receiver_name").val()
            var address = $("input#address").val()
            var receiver_phone = $("input#receiver_phone").val()

            var errorcode="";
            if(receiver_name==""||receiver_name==0){
                errorcode += "没有收货人姓名；<br/>"
            }
            /*if(state_id==""||state_id==0){
                errorcode += "省份没有选择；<br/>"
            }
            if(city_id==""||city_id==0){
                errorcode += "城市没有选择；<br/>"
            }*/
            if(state_id==""||state_id==0 || city_id==""||city_id==0){
                errorcode += "请完善地址：省-市-区县<br/>"
            }

            if(address==""||address==0){
                errorcode += "详细地址没有输入；<br/>"
            }
            if(receiver_phone==""||receiver_phone==0){
                errorcode += "联系电话没有输入；<br/>"
            }

            if(errorcode!=""){
                shownotice({
                    "icon":"notice",
                    "title":"输入有误，错误如下",
                    "remark":errorcode
                },[])
                return;
            }
            $("#btn_save_address").html("<img src='/statics/img/loader2.gif' style='display:block;' />")
            var postdata = {
                "liid":liid,
                "address_id":address_id,
                "state_id":state_id,
                "city_id":city_id,
                "district_id":district_id,
                "address":address,
                "receiver_phone":receiver_phone,
                "receiver_name":receiver_name
            }

            //console.log(postdata);

            var options = [{ "url": "/ajax/address.set.php", "data":postdata, "type":"POST", "dataType":"json"}]
            Load(options, function(json) {
                if (!user.isJson(json)) {
                    shownotice({
                        "icon": "error",
                        "title": "出错了",
                        "remark": "没有原因，因为找不到！"
                    }, [])
                    _working = false
                    $("#btn_save_address").html("保存")
                } else {
                    if(json.status=="success"){
                        $("#btn_save_address").html("保存成功")

                        _turn = false;
                        //判断是否直接显示在原始页面显示
                        if(_turn==true){
                            //插入页面
                            _callback(json)
                            $("#systemnoticebox .popwinbtnclose").hide()
                            user.exit()
                        }else{

                            $("#user_address_list ul li:eq("+liid+")").remove()
                            //插入地址列表；
                            var _temp = ""

                            _temp += '<li address="'+json.address+'" receiver_name="'+json.receiver_name+'" receiver_phone="'+json.receiver_phone+'" address_id="'+json.address_id+'" city_name="'+json.city_name+'" state_name="'+json.state_name+'" district_name="'+json.district_name+'"   district_id="'+json.district_id+'" city_id="'+json.city_id+'" state_id="'+json.state_id+'">' +
                        '<div class="default_address">' +
                        '<div class="row"><strong>'+json.receiver_name+'</strong></div>' +
                        '<div class="row">'+json.receiver_phone+'</div>' +
                        '<div class="row">'+json.full_address+'</div>' +
                                '<a class="modify_btn"><img src="/statics/img/icon.edit.png" width="100%" /></a>' +
                            '<a class="delete_btn"><img src="/statics/img/icon.deleteorder.png" width="100%" /></a>' +
                        '</div>' +
                        '</li>'


                            // _temp += '<li address_id="'+json.address_id+'" city_name="'+json.city_name+'" state_name="'+json.state_name+'" district_name="'+json.district_name+'"  district_id="'+json.district_id+'" city_id="'+json.city_id+'" state_id="'+json.state_id+'">' +
                            //     '<div class="default_address">' +
                            //     '<div class="row"><strong>'+json.receiver_name+'</strong></div>' +
                            //     '<div class="row">'+json.receiver_phone+'</div>' +
                            //     '<div class="row">'+json.full_address+'</div>' +
                            //     '<a class="modify_btn"><img src="/statics/img/icon.edit.png" width="100%" /></a>' +
                            // '<a class="delete_btn"><img src="/statics/img/icon.deleteorder.png" width="100%" /></a>' +
                            //     '</div>' +
                            //     '</li>'
                            $("#user_address_list ul").append(_temp);

                            user.address_list_event()

                            if($("#user_address_list ul li").length>4){
                                $("#in_btn_add_address").hide()
                            }
                            user.back();
                            _back = false;
                        }

                    }else{
                        switch(json.status){
                            case "error":
                                shownotice({
                                    "icon":"error",
                                    "title":"出错了",
                                    "remark":"你可能没有登录，请刷新再试！"
                                },[])
                                break;
                            case "noquota":
                                shownotice({
                                    "icon":"error",
                                    "title":"配额不足",
                                    "remark":"您最多可以存储5个地址！"
                                },[])
                                break;
                            case "error":
                                shownotice({
                                    "icon":"error",
                                    "title":"抱歉，出错了",
                                    "remark":"请刷新页面重试！"
                                },[])
                                break;
                            case "empty":
                                shownotice({
                                    "icon":"error",
                                    "title":"出错了",
                                    "remark":"没有输入数据或不完整，请检查输入！"
                                },[])
                                break;
                        }
                        _working = false
                        $("#btn_save_address").html("保存")
                    }
                }
            },function(){
                _working = false
                $("#btn_save_address").html("保存")
            })


        }
        user.address_list_event=function(){
            $("#user_address_list ul li a").unbind("click")

            $("#user_address_list ul li").unbind("click")
            $("#user_address_list ul li a.modify_btn").click(function(event) {

                _is_state = true;
                var id= $(this).parent().parent().attr("address_id")
                // console.log(id)
                _addressjson = {
                    receiver_name:$(this).parent().parent().attr("receiver_name"),
                    receiver_phone:$(this).parent().parent().attr("receiver_phone"),
                    address_id:$(this).parent().parent().attr("address_id"),
                    district_id:$(this).parent().parent().attr("district_id"),
                    city_id:$(this).parent().parent().attr("city_id"),
                    state_id:$(this).parent().parent().attr("state_id"),
                    address:$(this).parent().parent().attr("address"),
                    liid:$(this).parent().parent().index()
                }

                // console.log(_addressjson)
                _action = "address_modify"
                if(!_working){
                    if(!_loop){
                        user.delete_ani_class()
                        _action = "address_modify"
                        user.render();
                        _loop=true
                    }else{
                        //_loop=false
                        //console.log("1111111")
                        $("#user_address_modify").remove()
                        _current_pannel = "address_modify"
                        _html = user.htmlelement()
                        $("#user_pannel").append(_html)                        
                        user.get_area("")
                        $("#user_address_modify").removeClass("left_to_right").addClass("right_to_left");
                        $("#user_address_list").removeClass("zoom_out").addClass("zoom_in");
                        $("#btn_save_address").click(function(){
                            if(!_working){
                                user.save_address($(this).attr("address_id"),$(this).attr("liid"))
                            }
                        })                        
                    }
                    _back = true
                }
                $("#user_address_add").remove()
                event.stopPropagation();
            })
            $("#user_address_list ul li a.delete_btn").click(function(event) {
                var id= $(this).parent().parent().attr("address_id")
                var index = $(this).parent().parent().index()
                // console.log(id)
                shownotice({
                    "icon":"notice",
                    "title":"你确定要删除地址吗？",
                    "remark":"此操作将彻底删除地址。"
                },[{"url":"javascript:;","title":"确定删除"}])
                $("#systemnoticebox a.btn_white").attr("address_id",id)
                $("#systemnoticebox a.btn_white").attr("currentid",index)
                $("#systemnoticebox a.btn_white").click(function(){
                    //console.log($(this).attr("currentid"))
                    //console.log("删除")

                    var options = [{ "url": "/ajax/address.delete.php", "data":{"currentid":$(this).attr("currentid"),"address_id":$(this).attr("address_id")}, "type":"GET", "dataType":"json"}]
                    Load(options, function(json) {
                        if(json.status=="success"){
                            $("#user_address_list ul li:eq("+json.currentid+")").remove()
                            $("#systemnoticebox").remove()
                        }

                    })

                })

                event.stopPropagation();
            })


            $("#user_address_list ul li").click(function(){
                var address = $(this).find(".row:eq(2)").html()
                var replacestring = $(this).attr("state_name")+$(this).attr("city_name")+$(this).attr("district_name");

                address = address.replace(replacestring,"")
                var _json = {
                    "address_id":$(this).attr("address_id"),
                    "receiver_name":$(this).find(".row:eq(0) strong").html(),
                    "receiver_phone":$(this).find(".row:eq(1)").html(),

                    "state_name":$(this).attr("state_name"),
                    "city_name":$(this).attr("city_name"),
                    "district_name":$(this).attr("district_name"),
                    "address":address

                }

                // andy 2018-01-05 新增,o2o订单调用自定义函数
                if(undefined != user['o2oOrderDetial'] && user['o2oOrderDetial']) {

                    // console.info('调用o2oOrderCallBack函数');

                    user['o2oOrderCallBack']($(this));
                } else {
                    // 原有更新地址
                    update_address(_json)
                }

                user.exit()
            })
        }
        user.check_changepassword = function(){

            var oldpassword = $("#user_pannel #user_password_change input#oldpassword").val()
            var password = $("#user_pannel #user_password_change input#password").val()
            var repassword = $("#user_pannel #user_password_change input#repassword").val()

            var errorcode="";

            if(oldpassword==""||oldpassword==0){
                errorcode += "原始登录密码没有输入;<br/>"
            }
            if(password==""||password==0){
                errorcode += "新登录密码没有输入;<br/>"
            }
            if(repassword==""&&repassword!=password){
                errorcode += "两次输入的密码不一致;<br/>"
            }
            if(errorcode!=""){
                shownotice({
                    "icon":"notice",
                    "title":"输入有误，错误如下",
                    "remark":errorcode
                },[])
                return;
            }
            $("#user_pannel #user_password_change #btn_changepassword").html("<img src='/statics/img/loader2.gif' style='display:block;' />")

            var options = [{ "url": "/ajax/user.changepassword.php", "data":{password:password,oldpassword:oldpassword,repassword:repassword}, "type":"POST", "dataType":"json"}]
            Load(options, function(json){
                if (!user.isJson(json))
                {
                    shownotice({
                        "icon":"error",
                        "title":"出错了",
                        "remark":"没有原因，因为找不到！"
                    },[])
                    _working = false
                    $("#user_pannel #user_password_change #btn_changepassword").html("更改密码")
                }else{


                    if(json.status=="success"){
                        $("#user_pannel #user_password_change #btn_changepassword").html("更改密码成功")
                        shownotice(
                            {
                                "icon":"success",
                                "title":"成功更改密码",
                                "remark":json.nickname+"，您已经成功更改密码，系统可能需要你重新登录。<br/>该窗口2秒后关闭。"
                            },
                            [],
                            function(){
                                $("#systemnoticebox .popwinbtnclose").hide()
                                //alert("111111")
                                window.setTimeout(function(){
                                    $("#systemnoticebox").remove()


                                    if(_reload){
                                        window.location.reload()
                                    }
                                    user.exit()
                                    InitCart()
                                },2000)
                            }
                        )
                    }else{

                        switch(json.status){
                            case "nouser":
                                shownotice({
                                    "icon":"error",
                                    "title":"出错了，错误如下",
                                    "remark":"哦，没有这个账户哦！"
                                },[])
                                break;
                            case "nopassword":
                                shownotice({
                                    "icon":"error",
                                    "title":"出错了，错误如下",
                                    "remark":"哎，密码不正确啊！"
                                },[])
                                break;
                            case "nosame":
                                shownotice({
                                    "icon":"error",
                                    "title":"出错了，错误如下",
                                    "remark":"两次输入的新密码不一致"
                                },[])
                                break;
                            case "error":
                                shownotice({
                                    "icon":"error",
                                    "title":"抱歉，出错了",
                                    "remark":"请刷新页面重试！"
                                },[])
                                break;
                            case "empty":
                                shownotice({
                                    "icon":"error",
                                    "title":"出错了",
                                    "remark":"没有输入数据或不完整，请检查输入！"
                                },[])
                                break;
                        }
                        _working = false
                        $("#user_pannel #user_password_change #btn_changepassword").html("更改密码")
                    }
                }



            },function(){
                _working = false
                $("#user_pannel #user_password_change #btn_changepassword").html("更改密码")
            })

        }
        user.check_unby = function(){

            var password = $("#user_pannel #user_unby input#password").val()

            var errorcode="";

            /*if(password==""||password==0){
                errorcode += "登录密码没有输入;<br/>"
            }
            if(errorcode!=""){
                shownotice({
                    "icon":"notice",
                    "title":"输入有误，错误如下",
                    "remark":errorcode
                },[])
                return;
            }*/
            $("#user_pannel #user_unby #btn_unbynow").html("<img src='/statics/img/loader2.gif' style='display:block;' />")

            var options = [{ "url": "/ajax/user.unby.php", "data":{password:password,type:'weixin'}, "type":"POST", "dataType":"json"}]
            Load(options, function(json){
                if (!user.isJson(json))
                {
                    shownotice({
                        "icon":"error",
                        "title":"出错了",
                        "remark":"没有原因，因为找不到！"
                    },[])
                    _working = false
                    $("#user_pannel #user_unby #btn_unbynow").html("解除绑定")
                }else{


                    if(json.status=="success"){
                        $("#user_pannel #user_unby #btn_unbynow").html("解除绑定成功")
                        shownotice(
                            {
                                "icon":"success",
                                "title":"成功解除绑定账户",
                                "remark":json.nickname+"，您已经成功解除绑定了账户，系统可能需要你重新登录。<br/>该窗口2秒后关闭。"
                            },
                            [],
                            function(){
                                $("#systemnoticebox .popwinbtnclose").hide()
                                //alert("111111")
                                window.setTimeout(function(){
                                    $("#systemnoticebox").remove()


                                    if(_reload){
                                        window.location.reload()
                                    }
                                    user.exit()
                                    InitCart()
                                },2000)
                            }
                        )
                    }else{

                        switch(json.status){
                            case "nouser":
                                shownotice({
                                    "icon":"error",
                                    "title":"帐号不存在",
                                    "remark":"该帐号找不到，请核对清楚！"
                                },[])
                                break;
                            case "nopassword":
                                shownotice({
                                    "icon":"error",
                                    "title":"哎，密码不对呀",
                                    "remark":"请核对帐号，输入正确的密码！"
                                },[])
                                break;
                            case "openid":
                                shownotice({
                                    "icon":"error",
                                    "title":"重复绑定微信号",
                                    "remark":"咦！不可能啊，账户 "+json.email+" 绑定了这个微信号！或者跟他沟通下。。。"
                                },[])
                                break;
                            case "error":
                                shownotice({
                                    "icon":"error",
                                    "title":"抱歉，出错了",
                                    "remark":"请刷新页面重试！"
                                },[])
                                break;
                            case "empty":
                                shownotice({
                                    "icon":"error",
                                    "title":"出错了",
                                    "remark":"没有输入数据或不完整，请检查输入！"
                                },[])
                                break;
                        }
                        _working = false
                        $("#user_pannel #user_unby #btn_unbynow").html("解除绑定")
                    }
                }



            },function(){
                _working = false
                $("#user_pannel #user_unby #btn_unbynow").html("解除绑定")
            })

        }
        user.check_signin = function(){
            var account = $("#user_pannel #user_by input#account").val();
            var password = $("#user_pannel #user_by input#password").val();
            var login_phone_code = $("#login-phone-code").val();

            var Regex = /^(?:\w+\.?)*\w+@(?:\w+\.)*\w+$/;
            var errorcode="";
            var way = 'pass';
            if(account==""||account==0){
                layer.open({content : '请输入帐户',time : 1});
                $("#user_pannel #user_by input#account").focus();
                return;
            }

            if($('.login-pass:visible').size()) {
                if(password==""||password==0){
                    layer.open({content : '请输入密码',time : 1});
                    $("#user_pannel #user_by input#password").focus();
                    return;
                }
                way = 'pass';
            }

            if($('.login-msg:visible').size()) {
                if(login_phone_code==""||login_phone_code==0){
                    layer.open({content : '请输入验证码',time : 1});
                    $("#login-phone-code").focus();
                    return;
                }
                 way = 'msg';
            }

            $("#user_pannel #user_by #btn_bynow").html("登录中...")

            var options = [{ "url": "/ajax/user.login.php", "data":{account:account,password:password,login_phone_code:login_phone_code,way:way}, "type":"POST", "dataType":"json"}]
            Load(options, function(json){
                if (!user.isJson(json))
                {

                    layer.open({content : '非法操作',time : 1});
                    _working = false
                    $("#user_pannel #user_by #btn_bynow").html("登录")
                }else{

                    if(json.status=="success"){
                        $("#user_pannel #user_by #btn_bynow").html("成功了")
                        shownotice(
                            {
                                "icon":"success",
                                "title":"登录成功",
                                "remark":"恭喜您，"+json.nickname+"，您已经成功登录了账户，现在你可享用会员功能了。<br/>该窗口2秒后关闭。"
                            },
                            [],
                            function(){
                                $("#systemnoticebox .popwinbtnclose").hide()
                                //alert("111111")
                                //判断登录方式
                                if($('#user_pannel').hasClass('login_pannel')) {
                                    //纯登录方式
                                    if(json.gourl){
                                        window.location.href=json.gourl;
                                    }else{
                                        window.location.href="?m=account";
                                    }
                                    return;
                                }
                                window.setTimeout(function(){
                                    $("#systemnoticebox").remove()
                                    //非纯登录方式
                                    if($('#user_pannel').hasClass('right_to_left')) {
                                        if(_reload){
                                            window.location.reload()
                                        }
                                        user.exit()
                                        InitCart()
                                    }
                                },2000)
                            }
                        )
                    }else{

                        switch(json.status){
                            case "phone":
                                layer.open({content : '请输入正确的手机号码',time : 1});
                                break;                 
                            case "phone-code-x":
                                layer.open({content : '输入的验证码有误或者已过了有效期',time : 1});
                                break;       
                            case "nouser":
                                layer.open({content : '找不到此帐户',time : 1});
                                break;
                            case "nopassword":
                                layer.open({content : '请输入正确的密码',time : 1});
                                break;
                            case "error":
                                layer.open({content : '请刷新页面重试',time : 1});
                                break;
                            case "empty":
                                layer.open({content : '没有输入数据或不完整',time : 1});
                            case "openid":
                                shownotice({
                                    "icon":"error",
                                    "title":"重复绑定微信号",
                                    "remark":"咦！不可能啊，账户 "+json.account+" 绑定了这个微信号！或者跟他沟通下。。。"
                                },[])
                                break;
                            case "account":
                                layer.open({content : '请使用正确的手机号码或者邮箱登录',time : 1});
                                break;
                        }
                        _working = false
                        $("#user_pannel #user_by #btn_bynow").html("登录")
                    }
                }



            },function(){
                _working = false
                $("#user_pannel #user_by #btn_bynow").html("登录")
            })

        }
        user.check_pass = function(){
            var password = $("#user_pannel #user_reenterpassword input#password").val()
            var errorcode="";

            if(password==""||password==0){
                errorcode += "登录密码没有输入;<br/>"
            }
            if(errorcode!=""){
                shownotice({
                    "icon":"notice",
                    "title":"输入有误，错误如下",
                    "remark":errorcode
                },[])
                return;
            }
            $("#user_pannel #user_reenterpassword #btn_auth").html("<img src='/statics/img/loader2.gif' style='display:block;' />")

            var options = [{ "url": "/ajax/user.authentication.php", "data":{password:password}, "type":"POST", "dataType":"json"}]
            Load(options, function(json){
                if (!user.isJson(json))
                {
                    shownotice({
                        "icon":"error",
                        "title":"出错了",
                        "remark":"没有原因，因为找不到！"
                    },[])
                    _working = false
                    $("#user_pannel #user_by #btn_bynow").html("绑定")
                }else{


                    if(json.status=="success"){
                        $("#user_pannel #user_reenterpassword #btn_auth").html("认证成功")
                        shownotice(
                            {
                                "icon":"success",
                                "title":"成功认证账户",
                                "remark":"您现在可以使用余额支付了。<br/>该窗口1秒后关闭。"
                            },
                            [],
                            function(){
                                $("#systemnoticebox .popwinbtnclose").hide()
                                //alert("111111")
                                window.setTimeout(function(){
                                    $("#systemnoticebox").remove()


                                    if(_reload){
                                        window.location.reload()
                                    }
                                    _callback();
                                    user.exit();

                                },1000)
                            }
                        )
                    }else{

                        switch(json.status){
                            case "nolgoin":
                                shownotice({
                                    "icon":"error",
                                    "title":"出错了，错误如下",
                                    "remark":"哦，没有登录账户哦！请刷新浏览器重新登录"
                                },[])
                                break;
                            case "nopassword":
                                shownotice({
                                    "icon":"error",
                                    "title":"出错了，错误如下",
                                    "remark":"哎，密码不正确啊！"
                                },[])
                                break;

                            case "error":
                                shownotice({
                                    "icon":"error",
                                    "title":"抱歉，出错了",
                                    "remark":"请刷新页面重试！"
                                },[])
                                break;
                            case "empty":
                                shownotice({
                                    "icon":"error",
                                    "title":"数据输入不完整",
                                    "remark":"没有输入数据或不完整，请检查输入！"
                                },[])
                                break;
                        }
                        _working = false
                        $("#user_pannel #user_reenterpassword #btn_auth").html("确定余额支付")
                    }
                }



            },function(){
                _working = false
                $("#user_pannel #user_reenterpassword #btn_auth").html("确定余额支付")
            })

        }
        user.isJson = function(obj){

            var isjson = typeof(obj) == "object" && Object.prototype.toString.call(obj).toLowerCase() == "[object object]" && !obj.length;
            return isjson;

        }
        user.check_signup = function(){
            var phone = $("#user_pannel #user_signup input#phone").val();
            var phone_code = $("#user_pannel #user_signup input#phone_code").val();
            var nickname = $("#user_pannel #user_signup input#nickname").val();
            var password = $("#user_pannel #user_signup input#password").val();

            var Regex = /^(?:\w+\.?)*\w+@(?:\w+\.)*\w+$/;
            var errorcode="";

            if(phone =="" || phone==0){
                errorcode += "没有输入手机号码;<br/>"
            }
            if(phone_code =="" || phone_code==0){
                errorcode += "没有输入手机验证码;<br/>"
            }
            if(nickname==""||nickname==0){
                errorcode += "没有输入昵称;<br/>"
            }
            if(password==""||password==0){
                errorcode += "没有输入密码;<br/>"
            }else{
                if(password.length<6){
                    errorcode += "密码长度过短了;<br/>"
                }
            }
            if(errorcode!=""){
                shownotice({
                    "icon":"notice",
                    "title":"输入有误，错误如下",
                    "remark":errorcode
                },[])
                return;
            }

          $("#user_pannel #user_signup #btn_signup").html("注册中...");
             var options = [{ "url": "/ajax/user.signup.php", "data":{phone:phone,phone_code:phone_code,nickname:nickname,password:password}, "type":"POST", "dataType":"json"}]
            Load(options, function(json){
                if (!user.isJson(json))
                {
                    layer.open({content : '非法操作',time : 1});
                    _working = false
                    $("#user_pannel #user_signup #btn_signup").html("创建账户");
                }else{
                    if(json.status=="success"){
                        $("#user_pannel #user_signup #btn_signup").html("成功了");
                        var remark = "恭喜您，"+nickname+"<br />你已成功注册为25boy会员!";
                        if(json.coupon){
                            remark += "<br /><strong style=\"color:orange;font-size:1.4em;\">20元代金券已发放到你帐户！</strong>";
                        }
                        shownotice(
                            {
                                "icon":"success",
                                "title":"成功创建账户",
                                "remark":remark
                            },
                            [{"title":"去完善资料","url":"?m=account&a=setting"}],
                            function(){
                                $("#systemnoticebox .popwinbtnclose").hide()
                                
                                /*setTimeout(function(){                                    
                                    window.location.href="?m=account&a=setting";

                                },2000);*/
                               

                            }
                        )
                    }else{
                        switch(json.status){
                            case "phone":
                                layer.open({content : '手机号码格式不正确',time : 1});
                                break;
                            case "has_nickname":
                                layer.open({content : '此昵称已被注册',time : 1});
                                break;
                            case "has_phone":
                                layer.open({content : '此手机号码已被注册',time : 1});
                                break;
                            case "error":
                                layer.open({content : '抱歉出错了,请刷新重试',time : 1});
                                break;
                            case "empty":
                                layer.open({content : '没有输入数据或不完整',time : 1});
                                break;
                            case "deny_nickname":
                                layer.open({content : '此昵称不允许注册',time : 1});
                                break;
                            case "len_nickname":
                                layer.open({content : '昵称长度不可少于4位，不可大于16位',time : 1});
                                break;
                            case "dis_str":
                                layer.open({content : '昵称不能包含特殊符号，只允许中文英文数字划线',time : 1});
                                break;
                            case "len_password":
                                layer.open({content : '密码长度不能少于6位',time : 1});
                                break;
                            case "phone_code_x":
                                layer.open({content : '输入的验证码有误,或者已过了有效期',time : 1});
                                break;
                             case "phone_x":
                                layer.open({content : '使用新号码，请重新获取验证码',time : 1});
                                break;   
                                
                        }
                        _working = false
                        $("#user_pannel #user_signup #btn_signup").html("创建账户")
                    }
                }

            },function(){
                _working = false
                $("#user_pannel #user_signup #btn_signup").html("创建账户")
            })

        }
        user.by_btn = function(id,action,reload,callback){

            $(id).click(function(){
                _reload = reload
                _action = action;

                $("#systemnoticebox").remove()
                user.createpannel()
                _callback=callback
                //_callback()
            });


            //user.render()
        };
        user.back = function () {
            $("#user_"+_current_pannel).removeClass("right_to_left").addClass("left_to_right");
            $("#user_"+_oldscene).removeClass("zoom_in").removeClass("zoom_out");
            _back = false

        }
        user.auto = function(id,action,reload){
                _reload = true
                _action = "by";
                $("#systemnoticebox").remove()
                user.createpannel()
            //user.render()
        };
        user.delete_ani_class=function(){
            $("#user_by").removeClass()
            $("#user_signup").removeClass()
            $("#user_password").removeClass()
        }

        user.exit = function(){
            $(".main").removeClass("zoom_in").removeClass("zoom_out");
            $("#user_pannel").removeClass("right_to_left").addClass("left_to_right");
            if(navigator.userAgent.indexOf("Firefox")>0){
                $("#user_pannel").fadeOut();
            }
            $("body").attr("style","display:block;");
            _init = true;
            _working = false
            _reload = false;
            _oldscene = 0
            _back = _loop = false;
        }

        user.action = function(act,reload){
            _action = act;
            _reload = reload;
        }

        return user;
    }
};

var user = UserModel.createNew();

/*$(document).on("click","#back_login",function(){
    user.action('by',true);
    user.createpannel();
    if($('#action_zindex').val()==1){
        $('#user_pannel').addClass('login_pannel');
    }
});*/

/*
 *  获取手机验证码
 *  type: 1 调用注册,2 调用登录
*/

function get_code(button,type, cb) {
    var $phone = $(button).parent().prev().find('input');
    var phone = $phone.val();
    var post_php = '';
    if(type == 1) post_php = 'pcode.php?a=get_reg';
    if(type == 2) post_php = 'pcode.php?a=get_login';
    if(type == 31) post_php = 'pcode.php?a=get_qq_reg';
    if(type == 33) post_php = 'pcode.php?a=get_weibo_reg';
    // re = /^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$/;
    if(phone == '') {
        $phone.css('border','1px solid red');
        return;
    }
    /*判断是否是正确的手机号格式*/
    if(phone.length == 11) {
        $phone.css('border','1px solid #ccc');
        $(button).html('<img src="/statics/img/bx_loader.gif" height="20" style="margin-top:12px;" />');
        $(button).attr('disabled','disabled');

        /*发送短信*/
        $.post('ajax/' + post_php,{'phone' : phone},function(data){
            switch(data) {
                case 'is_phone':
                    layer.open({content : '请输入正确的手机号码!',time : 1});
                    $(button).text('获取验证码').removeAttr('disabled');
                    break;
                case 'has_phone':
                    layer.open({content : '此手机号码已被注册!',time : 1});
                    $(button).text('获取验证码').removeAttr('disabled');
                    break;
                case 'nouser':
                    if(typeof cb == "function"){
                        cb(data);
                    }else{
                        layer.open({content : '不存在此用户',time : 1});
                    }
                    $(button).text('获取验证码').removeAttr('disabled');
                    break;
                case '-1' :
                    layer.open({content : '申请验证码无效',time : 1});
                    $(button).text('获取验证码').removeAttr('disabled');
                    break;
                case '-42':
                    layer.open({content : '请过会再申请验证码!',time : 1});
                    $(button).text('获取验证码').removeAttr('disabled');                              
                    break;
                case '1':
                    var down_time = 60;
                    setTimeout(function() {
                        if(down_time > 1) {
                           down_time--;
                           $(button).text(down_time + 's重新获取');
                           setTimeout(arguments.callee,1000);
                        }else {
                            $(button).text('获取验证码').removeAttr('disabled'); 
                        }
                    },1000);
                    typeof cb == "function" && cb(data);
                    break;
            }
        });

        return;



    }else {
        $phone.css('border','1px solid red');
    }
    
}



