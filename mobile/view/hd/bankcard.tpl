{include file="public/head.tpl" title=head}

<div class="banner">
    <img src="https://img.25miao.com/1635/c1a40f5e87616d16404a182b5956485b.jpg!w800" alt="25boy与交通银行合作" />
</div>
<div class="bankForm">
    {if $user_id}
        <div class="input-group">
            <div class="getAccount">
                手机号：{$user.phone}
                <a class="edit" href="javascript:logout()">修改</a>
            </div>
        </div>
        <div class="input-group">
            <button class="ext-btn getRedpack" type="button" onclick="signCallback()">点击领取</button>
        </div>
    {else}
        <div class="input-group">
            <i class="iconfont">&#xe642;</i>
            <input type="text" class="ext-input icon-phone" id="phone" placeholder="手机号码" />
        </div>
        <div class="input-group input-short">
            <i class="iconfont">&#xe644;</i>
            <input type="text" id="phone_code" class="ext-input" placeholder="验证码" />
            <button class="ext-btn" onclick="get_phonecode(this)">获取验证码</button>
        </div>
        <div class="input-group" id="password-input" style="display:none;">
            <i class="iconfont">&#xe643</i>
            <input type="text" class="ext-input" id="password" placeholder="设置密码,至少于6位" />
        </div>
        <div class="input-group">
            <button class="ext-btn signin" type="button">确认领取</button>
        </div>
    {/if}
</div>

{literal}
<style type="text/css">
body{background-color:#f2f2f2;}
.bankForm{padding:2em 3em;font-family:"微软雅黑";}
.bankForm .input-group{margin-bottom:15px;position:relative;height:50px;line-height:50px;}
.bankForm .iconfont{top:-2px;left:10px;position:absolute;font-size:22px;}
.bankForm .ext-input{padding:0 40px;background-color:#fff;height:50px;line-height:50px;border:none;font-size:18px;box-sizing:border-box;width:100%;}
.bankForm .ext-btn{box-sizing:border-box;height:50px;line-height:50px;width:100%;color:#fff;background-color:#1aad19;border:1px solid #158a14;border-radius:6px;font-size:16px;font-weight:bold;}
.bankForm .ext-btn.disabled{background-color:#9ED99D;color:rgba(255, 255, 255, 0.6);}
.bankForm .input-short .ext-input{width:60%;}
.bankForm .input-short .ext-btn{width:35%;float:right;background:#fff;border-color:#ddd;color:#000;font-size:14px;}
.bankForm .getAccount{font-size:18px;text-align:center;}
.bankForm .edit{color:#4183c4;margin-left:1.5em;}
</style>
<script type="text/javascript">
var type = '';
$(document).on('click','.signin',function(){
    $('.signin').attr('disabled',true).addClass('disabled');
    if(type == 'reg'){
        var url = '/ajax/user.signup.php';
        var data = {
            phone: $('#phone').val(),
            password: $('#password').val(),
            phone_code: $('#phone_code').val(),
            openid: ''
        };
    }else{
        var url = '/ajax/user.login.php';
        var data = {
            account: $('#phone').val(),
            login_phone_code: $('#phone_code').val(),
            way: 'msg',
            openid: ''
        };
    }

    var options = [{ "url": url, "data":data, "type":"POST", "dataType":"json"}];
    Load(options, function(json){
        $('.signin').attr('disabled',false).removeClass('disabled');
        if(json.status == 'success'){
            signCallback();
        }else{
            layer.open({content:json.msg, time:1});
        }
    });
});
function get_phonecode(button){
    get_code(button, 2, function(data){
        if(data == 'nouser'){
            get_code(button, 1, function(){
                type = 'reg';
                $('#password-input').show();
            })
        }    
    });
}
function signCallback(){
    $('.getRedpack, .signin').attr('disabled',true).addClass('disabled');
    var options = [{ "url": "/?m=hd&a=bankcard", "data":{'do':'callback'}, "type":"POST", "dataType":"json"}];
    Load(options, function(json){
        $('.getRedpack, .signin').attr('disabled',false).removeClass('disabled');
        if(json.status == 'success'){
            layer.open({
                tyep: 1,
                content: "操作成功！<br>填写资料领卡后激动即可获得100元现金券红包！",
                btn: ['去申请校园卡'],
                shadeClose: false,
                end: function(){
                    window.location.href = "https://creditcardapp.bankcomm.com/applynew/front/apply/mgm/account/wechatEntry.html?recomId=13044776&saleCode=371000100&entryRecomId=&trackCode=A090420182885&source=25boy";
                }
            });
        }else{
            layer.open({content:json.msg, time:1});
        }
    });
}
/*function getRedpack(){
    $('.getRedpack, .signin').attr('disabled',true).addClass('disabled');
    var options = [{ "url": "/?m=hd&a=redpack", "data":{do:'get'}, "type":"POST", "dataType":"json"}];
    Load(options, function(json){
        $('.getRedpack, .signin').attr('disabled',false).removeClass('disabled');
        if(json.status == 'success' || json.status=='geted'){
            layer.open({
                tyep: 1,
                content: "领取成功！<br>50元现金券红包已到账，<br>申请校园卡激活后再送50元！",
                btn: ['去申请校园卡'],
                shadeClose: false,
                end: function(){
                    window.location.href = "https://creditcardapp.bankcomm.com/applynew/front/apply/mgm/account/wechatEntry.html?recomId=13044776&saleCode=371000100&entryRecomId=&trackCode=A090420182885&source=25boy";
                }
            });
        }else{
            layer.open({content:json.msg, time:1});
        }
    });
}*/
function logout(){
    var options = [{ "url": "/ajax/user.unby.php", "data":{do:'get'}, "type":"POST", "dataType":"json"}];
    Load(options, function(json){
        if(json.status=="success"){
            window.location.reload();
        }
    });
}
</script>
{/literal}
{include file="public/js.tpl" title=js}
{include file="public/footer.tpl" title=footer}