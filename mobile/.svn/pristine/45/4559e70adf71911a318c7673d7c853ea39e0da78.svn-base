{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<div id="bodybox">
    <section class="main pagemain pagegrey">
        {if $session_uid==""||$session_uid==0}
            {include file="public/remind_login.tpl" title=header}
        {else}
            <section class="account_setting">
                <div class="icon_box">
                    <div class="usericon"><div class="loader"></div><img src="{$user.icon}" width="200" alt="头像" /></div>
                    <div class="btnchangebox">
                        <input class="btnchange" id="iconfile" type="file" accept="image/*;capture=camera">
                    </div>
                </div>
                <div class="row {if $user['nickname']=='' || empty($user['nickname'])}row_nickname{else}disenable_edit{/if}">
                    <div class="fl">昵称</div>
                    <div class="fr">
                    {if !empty($user['nickname'])}
                    <i class="iconfont" style="font-size:1.6em;color:#f4c600;padding-right: 0.3em;vertical-align: bottom;">{$v_img}</i>
                    {/if}
                    {$user.nickname}
                    
                    </div>
                </div>
                <div class="row {if $user['email']=='' || empty($user['email'])}row_email{else}disenable_edit{/if}">
                    <div class="fl">电子邮箱</div>
                    <div class="fr">{$user.email}</div>
                </div>
                <div class="row {if $user['flag'] != 1 || $user['phone']==""}row_phone{else}disenable_edit{/if}">
                    <div class="fl">手机号码</div>
                    <div class="fr">{$user.phone}</div>
                    {if $user['flag'] != 1 || $user['phone']==""}
                    <div class="phone-here">
                        <span>点击验证</span>
                    </div>
                    {/if}
                </div>
                <div class="row row_realname">
                    <div class="fl">姓名</div>
                    <div class="fr">{$user.realname}</div>
                </div>
                <div class="row row_gender">
                    <div class="fl">性别</div>
                    <div class="fr">{$user.sex}</div>
                </div>
                <div class="row row_birthday">
                    <div class="fl">生日</div>
                    <div class="fr"><input style="font-weight: normal; color:#666; border:none; padding:0; text-align: right;" id="birthday" value="{$user.birthday}" /></div>
                </div>
                <div class="rt borderbottom"></div>
                <div class="row row_address">
                    <div class="fl">地址管理</div>
                    <div class="fr"><span class="arrow_noraml"></span></div>
                </div>
                <div class="rt borderbottom"></div>
                <div class="row row_security">
                    <div class="fl">账户安全</div>
                    <div class="fr"><span class="arrow_noraml"></span></div>
                </div>
                {if $is_weixin}
                <div class="row row_unby">
                    <div class="fl">微信解绑</div>
                    <div class="fr"><span class="arrow_noraml"></span></div>
                </div>
                {/if}
            </section>

        {/if}
    </section>

</div>

<div class="blank20"></div>

<link rel="stylesheet" type="text/css" href="/statics/css/account.css?v={$version}">
{include file="public/js.tpl" title=js}

{if $session_uid==""||$session_uid==0}
{literal}
    <script>
        $(function(){
            user.by_btn("#cart_user_by","by",true);
        })
    </script>
{/literal}
{else}
{literal}
    <script>
        function update_address(json){
            var address_id = json.address_id;
            var options = [{ "url": "/ajax/address.set.php", "data":{'act':'default',address_id:address_id}, "type":"POST", "dataType":"json"}];
            Load(options, function(json){
                if(json.status=="success"){
                }
            });
        }

        $(function(){
            user.by_btn(".row_unby","unby",true);
            user.by_btn(".row_address","address_list",true);
            user.by_btn(".row_security","password_change",true);

            $(".row_nickname").click(function(){
                if($(this).find(".fr input").length==0){
                    $(this).find(".fr").attr("orgval",$(this).find(".fr").html()).html("<input type='text' value='"+$(this).find(".fr").html()+"' class='input_short text_right' id='nickname' />");
                    $("#nickname").focus();
                    $("#nickname").blur(function(){
                        if($(this).parent().attr("orgval")!=$(this).val()&&$(this).val()!=""){
                            saveitem("nickname",$(this).val());
                        }else{
                            $(this).parent().html($(this).parent().attr("orgval"));
                        }
                    });
                }
            });

            $(".row_email").click(function(){
                if($(this).find(".fr input").length==0){
                    $(this).find(".fr").attr("orgval",$(this).find(".fr").html()).html("<input type='text' value='"+$(this).find(".fr").html()+"' class='input_short text_right' id='email' />");
                    $("#email").focus();
                    $("#email").blur(function(){
                        if($(this).parent().attr("orgval")!=$(this).val()&&$(this).val()!=""){
                           /* 如果邮箱变化了，就保存信息*/
                            saveitem("email",$(this).val());
                        }else{
                            /*如果邮箱无变化，保持原样*/
                            $(this).parent().html($(this).parent().attr("orgval"));
                        }
                    });
                }
            });

            $(".row_realname").click(function(){
                if($(this).find(".fr input").length==0){
                    $(this).find(".fr").attr("orgval",$(this).find(".fr").html()).html("<input type='text' value='"+$(this).find(".fr").html()+"' class='input_short text_right' id='realname' />");
                    $("#realname").focus();
                    $("#realname").blur(function(){
                        if($(this).parent().attr("orgval")!=$(this).val()&&$(this).val()!=""){
                            saveitem("realname",$(this).val());
                        }else{
                            $(this).parent().html($(this).parent().attr("orgval"));
                        }
                    });
                }
            });

            $(".row_phone").click(function(){
                if($(this).find(".fr input").length==0){
                    $(this).find(".fr").attr("orgval",$(this).find(".fr").html()).html("<input type='text' value='"+$(this).find(".fr").html()+"' class='input_short text_right' id='phone' />");
                    $("#phone").focus();
                    $(".phone-here").hide();
                    $("#phone").blur(function(){
                        if($(this).val()!=""){
                           saveitem("phone",$(this).val());
                        }else{
                            $(this).parent().html($(this).parent().attr("orgval"));
                        }
                    });
                }
            });

            $(".row_gender").click(function(){
                if($(this).attr("rel")==undefined || $(this).attr("rel")=="0"){
                    $(this).attr("rel","1");
                    $(this).find(".fr").attr("orgval",$(this).find(".fr").html());
                    var sexname = $(this).find(".fr").attr("orgval");
                    var sex = "male";
                    var temp = "";
                    if(sexname=="女士"){
                        sex = "female";
                        temp = "<div class='btncheckbox' id='maleitem' rel='male'>先生</div><div id='femaleitem' class='btncheckbox btncheckbox_checked' rel='female'>女士</div>";
                    }else{
                        sex = "male";
                        temp = "<div class='btncheckbox btncheckbox_checked' id='maleitem' rel='male'>先生</div><div id='femaleitem' class='btncheckbox' rel='female'>女士</div>";
                    }
                    $(this).find(".fr").html(temp);
                    $(this).find(".fr .btncheckbox").click(function(){
                        if($(this).attr("rel")=="male"){
                            $(".row_gender .btncheckbox").removeClass("btncheckbox_checked");
                            $("#maleitem").addClass("btncheckbox_checked");
                        }else{
                            $(".row_gender .btncheckbox").removeClass("btncheckbox_checked");
                            $("#femaleitem").addClass("btncheckbox_checked");
                        }
                        saveitem("gender",$(this).attr("rel"));
                    });
                }
            });
            
            if($('#birthday').val() == '未填写' || $('#birthday').val()==""){
                $('#birthday').mobiscroll().calendar({
    				theme: 'mobiscroll',
    				mode: 'mixed',
    				dateFormat: 'yy-mm-dd',
    				display: 'bottom',
    				lang: 'zh',
    				onSelect:function(valueText,inst){
    				    var temp = valueText.split("-");
                        $('#birthday').val(valueText+" ("+getAstro(temp[1],temp[2])+"座)");
                        saveitem("birthday",valueText);
    				}
    			});
            }
        });

        function saveitem(field,val){
            $(".row_"+field+" .fr").empty();
            $(".row_"+field+" .fr").addClass('load');
            var orgval = $(".row_"+field+" .fr").attr('orgval');
            var options = [{ "url": "/ajax/user.modify.php", "data":{field:field,val:val}, "type":"POST", "dataType":"json"}];
            Load(options, function(json){
                $(".row_"+field+" .fr").removeClass('load');

                if(json.status=="format"){
                    $(".row_"+field+" .fr").html(orgval);
                    layer.open({'content':'手机号码格式不正确。',time : 1});
                }else if(json.status=="error"){
                    $(".row_"+field+" .fr").html(orgval);
                    layer.open({'content':'修改失败，找不到相关数据。'});
                }else if(json.status=="nologin"){
                    $(".row_"+field+" .fr").html(orgval);
                    layer.open({'content':'你还没有登录。'});
                }else if(json.status=="nickname"){
                    $(".row_"+field+" .fr").html(orgval);
                    layer.open({'content':'昵称已经有人使用了。'});
                }else if(json.status=="deny_nickname"){
                    $(".row_"+field+" .fr").html(orgval);
                    layer.open({'content':'此昵称不允许使用，换一个吧亲！'});
                }else if(json.status=="noeditnickname"){
                    $(".row_"+field+" .fr").html(orgval);
                    layer.open({'content':'昵称不允许修改。',time : 1});
                }else if(json.status=="len_nickname"){
                    $(".row_"+field+" .fr").html(orgval);
                    layer.open({'content':'昵称长度不符合5-16字符。'});
                }else if(json.status=="dis_str"){
                    $(".row_"+field+" .fr").html(orgval);
                    layer.open({'content':'昵称不能包含特殊符号，只允许中文英文数字划线'});
                }else if(json.status=="is_email"){
                    $(".row_"+field+" .fr").html(orgval);
                    layer.open({'content':'邮箱格式不正确。',time : 1});
                }else if(json.status=="has_email"){
                    $(".row_"+field+" .fr").html(orgval);
                    layer.open({'content':'邮箱已经被使用了。',time : 1});
                }else if(json.status=="has_phone"){
                    $(".row_"+field+" .fr").html(orgval);
                    layer.open({'content':'手机号码已经被使用了。'});
                }else if(json.status=="noeditphone"){
                    $(".row_"+field+" .fr").html(orgval);
                    layer.open({'content':'手机号码不允许修改。'});
                }else if(json.status=="apply_failed"){
                    $(".row_"+field+" .fr").html(orgval);
                    layer.open({'content':'申请验证码无效',time : 2});
                }else if(json.status=="waiting_apply_time"){
                    $(".row_"+field+" .fr").html(orgval);
                    layer.open({'content':'你操作的太频繁，请过会再操作！',time : 2});
                }else if(json.status=="is_real_phone"){
                    var phone = json.val;
                    var html = '<div class="account-setting-phone"><div class=" relative"><input type="text" class="input-phone-code" style="" placeholder="短信验证码" maxlength="6"/><button class="btn btn-info btn-inline">获取验证码</button></div><button class="btn-phone-code">验证</button></div>';

                    /*短信验证开始*/
                    layer.open({
                        type : 1,
                        content : html,
                        title : '手机验证',
                        className : 'layer-html',
                        shadeClose : false,
                        cancel : function() {
                            $(".row_"+field+" .fr").html(orgval);
                            $(".phone-here").show();
                        },
                    });

                    $('.input-phone-code').focus();

                    /*点击获取验证码*/
                    $('.btn-inline').click(function() { 
                        var _this = this;
                         $(this).text('60s重新获取').attr('disabled','disabled').css({
                        'background' : '#ccc'});
                        $.post('ajax/pcode.php?a=getCode_Setting',{phone:phone},function(data) {
                            switch(data) {
                                case '-1':
                                    layer.open({content : '申请验证码无效!'});
                                    break;
                                case '-42':
                                    layer.open({content : '操作太频繁，请过会再申请验证码!',time : 2});
                                    $(_this).text('获取验证码').removeAttr('disabled').css({
                                       'background' : '#5bc0de' 
                                    });                              
                                    break;
                                default:
                                    var down_time = 60;

                                    setTimeout(function() {
                                        if(down_time > 1) {
                                           down_time--;
                                           $(_this).text(down_time + 's重新获取');
                                           setTimeout(arguments.callee,1000);
                                        }else {
                                            $(_this).text('获取验证码').removeAttr('disabled').css({
                                               'background' : '#5bc0de' 
                                            }); 
                                        }
                                     },1000);
                                    break;
                            }
                        });
                    });



                    /*点击验证按钮*/
                   $('.btn-phone-code').click(function() {
                        var _this = this;
                        var phone_code = $('.input-phone-code').val();
                        if(phone_code == '') {
                            layer.open({
                                type : 0,
                                content : '请输入验证码',
                                time : 1
                            });

                            return;
                        }
                        $(_this).text('验证中...');

                        $.post('ajax/pcode.php?a=verifyCode_Settint',{'phone' :phone,'phone_code' : phone_code},function(data) {
                                switch(data) {
                                    case 'empty':
                                         layer.open({
                                            content : '请输入验证码',
                                            time : 1
                                        });
                                         $(_this).text('验证');
                                        break;
                                    case 'wrong_code':
                                         layer.open({
                                            content : '验证码错误或过了有效期',
                                            time : 2
                                        });
                                        $(_this).text('验证');
                                        break; 
                                    case 'wrong_phone':
                                         layer.open({
                                            content : '请重新获取验证码',
                                            time : 2
                                        });
                                        $(_this).text('验证');
                                        break;  
                                    case 'success':
                                        layer.open({content : '手机验证成功!',time : 1});
                                        /*手机修改成功*/
                                        $(".row_"+field+" .fr").html(phone);
                                        $(".row_phone").addClass('disenable_edit').off('click');
                                        setTimeout(function() {layer.closeAll();},1000);
                                        
                                        /*window.location.href = '?m=account&a=setting';*/
                                        
                                        break;
                                }

                        });

                    });              
                }else if(json.status=="copy"){
                    $(".row_"+field+" .fr").html(orgval);
                    layer.open({'content':'没有任何修改。'});
                }else if(json.status=="success"){
                    if(field=="nickname"){
                        $(".row_nickname").addClass('disenable_edit');
                    }
                    if(field=="email"){
                        $(".row_email").addClass('disenable_edit');
                        $(".row_email").off('click');
                        /*$(".row_email").addClass('disenable_edit').removeClass('row_email');*/
                    }
                    if(field!="birthday"){
                        $(".row_"+field+" .fr").html(json.val);
                    }else{
                        var html = '<input style="font-weight: normal; color:#666; border:none; padding:0; text-align: right;" id="birthday" value="'+json.val+'" />';
                        $(".row_"+json.field+" .fr").html(html);
                        $('#birthday').mobiscroll().calendar({
                            theme: 'mobiscroll',
                            mode: 'mixed',
                            dateFormat: 'yy-mm-dd',
                            display: 'bottom',
                            lang: 'zh',
                            onSelect:function(valueText,inst){
                                var temp = valueText.split("-");
                                $('#birthday').val(valueText+" ("+getAstro(temp[1],temp[2])+"座)");
                                saveitem("birthday",valueText);
                            }
                        });
                    }
                }else{
                    if(field!="birthday"){
                        $(".row_"+json.field+" .fr").html(orgval);
                    }
                }
                $(".row_gender").attr("rel","0");
            });
        }

        $(function(){
            $('.usericon').on('click',function(){
                $('#iconfile').click();
            });
        });

        window.onload = function(){
            document.getElementById('iconfile').onchange = function(){
                var img = event.target.files[0];
                if(!img){
                    return ;
                }
                if(!(img.type.indexOf('image')==0 && img.type && /\.(?:jpg|png|gif)$/.test(img.name)) ){
                    shownotice({
                        "icon":"error",
                        "title":"出错了，错误如下",
                        "remark":"图片只能是JPG,PNG或GIF格式！"
                    },[]);
                    return ;
                }
                var reader = new FileReader();
                reader.readAsDataURL(img);
                reader.onload = function(e){
                    $(".usericon").removeClass("rotate360 shadow").addClass("rotate360 shadow");
                    $(".btnchangebox").hide();
                    $(".loader").show();
                    $.post("ajax/save.icon.php", { img: e.target.result},function(ret){
                        switch (ret.status){
                            case "success":
                                $(".usericon img").attr("src",ret.img_url);
                                break;
                            case "error_upload":
                                break;
                            case "error_format":
                                break;
                        }
                        $(".btnchangebox").show();
                        $(".loader").hide();
                        $(".usericon").removeClass("rotate360 shadow");
                    },'json');
                }
            }
        }
    </script>
    <link href="statics/css/mobiscroll.custom-2.16.1.min.css" rel="stylesheet" type="text/css" />
    <script src="statics/js/mobiscroll.custom-2.16.1.min.js" type="text/javascript"></script>
{/literal}
{/if}

{include file="public/footer.tpl" title=footer}