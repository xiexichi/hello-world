{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<div id="bodybox">
    <section class="main pagemain">
        <section class="socailbindbox">
            <div class="inbox">
                <div class="user_profile">
                    <div class="imgbox"><img src="{$user_profile.avatar_hd}" width="100%" /></div>
                    <div class="titlebox">
                        <span>QQ账号:</span>{$user_profile.social_name}
                        <p><a href="/h5/subscribe.html">点击关注，并绑定微信号<br>可享25BOY购物全场包邮！</a></p>
                    </div>
                </div>
                <div class="tabbox">
                    <a href="javascript:;" id="new_account" class="current">新账号</a>
                    <a href="javascript:;" id="old_account">已有账号</a>
                </div>

                <!-- 第三方入口绑定页面 -->


                <div class="subject pb10 new_account icon-relative">
                    <i class="iconfont icon-font">&#xe642;</i>
                    <input type="text" id="phone" class="ex-text" placeholder="请输入手机号码" />
                    <i class="iconfont icon-close">&#xe639;</i>
                </div>
                <div class="subject pb10 new_account icon-relative">
                    <i class="iconfont icon-font">&#xe644;</i>
                    <input type="text" id="phone_code" class="ex-text ex-text-short" placeholder="请输入验证码" />
                    <i class="iconfont icon-close incon-close-short">&#xe639;</i>
                    <button class="button_get_code" onclick="get_code(this,31)">获取验证码</button>

                </div>
                <div class="subject pb10 new_account icon-relative">
                    <i class="iconfont icon-font">&#xe645;</i>
                    <input type="text" id="nickname" value="{$user_profile.social_name}" class="ex-text" placeholder="请输入昵称" />
                    <i class="iconfont icon-close">&#xe639;</i>
                </div>



                <div class="subject pb10 old_account icon-relative" style="display:none;">
                    <i class="iconfont icon-font">&#xe642;</i>
                    <input type="text" id="account" class="ex-text" placeholder="手机号码/电子邮箱" />
                    <i class="iconfont icon-close">&#xe639;</i>
                </div>
                
                <div class="subject pb10 icon-relative">
                    <i class="iconfont icon-font">&#xe643;</i>
                    <input type="password" id="password" class="ex-text" placeholder="请输入密码" />
                    <i class="iconfont icon-eye">&#xe646;</i>
                </div>
                <input type="hidden" id="uri" value="{$self_url}" />
                
                
                <div class="btnbox icon-relative">
                    <a href="javascript:;" id="cart_user_by" class="btn ex-btn f18 p0 w250">立即注册</a>
                </div>
          
                <!--  -->
            </div>
        </section>
    </section>
</div>
<link rel="stylesheet" type="text/css" href="/statics/css/account.css">
{include file="public/js.tpl" title=js}
<script src="/statics/js/root.js?v={$version}"></script>
{literal}
<script>
    var had_account = 0;
    $(function(){

           /*********输入数据到input就显示清空按钮**********/

            $('.ex-text').keyup(function() {
                if($(this).val() == '') {
                    $(this).next().hide();
                }else {
                      $(this).next().show();

                    }
            });


           $('.relative').on('click','.icon-close',function() {
                $(this).hide().prev().val('').focus();

           });


            /*密码开显功能*/
           $('.relative').on('click','.icon-eye',function() {
                if($(this).prev().attr('type') == 'password') {
                    $(this).css('color','#333').prev().attr('type','text');
                }else {
                   $(this).css('color','#bbb').prev().attr('type','password');
                }


           });


            /*********************************************/




        $(".tabbox a").click(function(){
            if($(this).attr("id")=="old_account"){
                $(".tabbox a").removeClass("current");
                $("#old_account").addClass("current");
                $(".new_account").hide();
                $(".old_account").show();
                $('.icon-eye').before('<i class="iconfont icon-close">&#xe639;</i>');
                $('.icon-eye').remove();
                $('#password').attr('type','password').val('');

                $('#cart_user_by').html('绑定登录');
                had_account = 1;
            }else{
                $(".tabbox a").removeClass("current");
                $("#new_account").addClass("current");
                $(".new_account").show();
                $(".old_account").hide();
                $('#password').next().remove();
                $('#password').attr('type','password').after('<i class="iconfont icon-eye">&#xe646;</i>');
                $('#password').val('');
                $('#cart_user_by').html('立即注册');
                had_account = 0;
            }
        });
        $("#cart_user_by").click(function(){
            user_bind();
        });
    });
    function user_bind(){

        var phone = $("input#phone").val(),
            phone_code = $("input#phone_code").val(),
            account = $('input#account').val(),
            password = $("input#password").val(),
            nickname = $("input#nickname").val(),
            uri = $("input#uri").val(),
            original = (!had_account) ? '立即注册' : '绑定登录', 
            loading = (!had_account) ? '注册中...' : '登录中...', 
            success = (!had_account) ? '注册成功' : '登录成功', 
            errorcode="";
        if(!had_account){
            if(phone==""){
                errorcode += "请输入手机号码<br/>";
            }
            if(phone_code==""){
                errorcode += "请输入验证码<br/>";
            }

            if(nickname==""){
                errorcode += "请输入昵称<br/>";
            }

            if(password==""||password==0){
                errorcode += "请输入密码<br/>";
            }
            if(password.length > 0 && password.length < 6){
                errorcode += "密码不可少于6位<br/>";
            }

            if(errorcode!=""){
                shownotice({
                    "icon":"notice",
                    "title":"输入有误，错误如下",
                    "remark":errorcode
                },[]);
                return;
            }

        }else{
            if(account==""){
                layer.open({content : '请输入帐号',time : 1});
                $('input#account').focus();
                return;
            }
            if(password==""){
                layer.open({content : '请输入密码',time : 1});
                $('input#password').focus();
                return;
            }
        }

        $("#cart_user_by").html(loading);
        var options = [{ "url": "/ajax/user.by.php", "data":{
            type:"qq",
            had_account:had_account,
            nickname:nickname,
            account:account,
            phone:phone,
            phone_code:phone_code,
            password:password
        }, "type":"POST", "dataType":"json"}];
        Load(options, function(json){
            if (!user.isJson(json)) {
                layer.open({ content : "请刷新页面重试，或者联系我们。", time : 1});
                _working = false;
                $("#cart_user_by").html(original);
            }else{
                if(json.status=="success"){
                    $("#cart_user_by").html(success);
                    if(json.had_account==1){
                        title = "成功登录并绑定账户";
                        remark = "恭喜您，"+json.nickname+"<br />您已经成功登录并绑定了QQ账户，现在你可享用会员功能了。<br/>该窗口2秒后关闭。";
                    }else{
                        title = "成功创建并绑定账户";
                        remark = "恭喜您，"+json.nickname+"<br />您已注册为25boy会员并绑定了QQ账户！";
                        if(json.coupon){
                            remark += "<br /><strong style=\"color:orange;font-size:1.4em;\">20元代金券已发放到你帐户！</strong>";
                        }
                    }

                    if(json.had_account==1){
                        /*登录*/
                        shownotice({
                            "icon":"success",
                            "title":title,
                            "remark":remark
                        }, [], function(){
                            $("#systemnoticebox .popwinbtnclose").hide();
                            window.setTimeout(function(){
                                 $("#systemnoticebox").remove();
                                window.location.reload();
                                user.exit();
                            },2000);
                        });

                    }else {
                        /*注册*/
                        shownotice(
                            {
                                "icon":"success",
                                "title":title,
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


                    }


                }else{
                    var time = (json.time > 0 ) ? json.time : 1; 
                    layer.open({
                        content : json.remark,
                        time : time
                    });
                    _working = false;
                    $("#cart_user_by").html(original);
                }
            }
        },function(){
            _working = false;
            $("#cart_user_by").html("绑定登录");
        });
    }

</script>
{/literal}

{include file="public/footer.tpl" title=footer}