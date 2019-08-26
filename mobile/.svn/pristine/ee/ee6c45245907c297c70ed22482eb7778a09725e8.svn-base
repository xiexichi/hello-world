{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<div id="bodybox" style="position:relative;">
    <section class="main pagemain promoteApply">
   
        <div class="img-box">
            <img src="http://img.25miao.com/115/1498881632.jpg!w640" alt="一次推广，终身收益" />
        </div>

        <div class="fl-box">

            <div class="fl-wordBox">
                <ul class="flex fl-word-tab">
                    <li class="on"><a href="javascript:void(0);">活动规则</a></li>
                    <li><a href="javascript:void(0);">我的邀请</a></li>
                    <li><a href="javascript:void(0);">收益排行榜</a></li>
                </ul>
                <div class="fl-word-con">

                    <div class="word-con-box">
                        <div class="inbox">
                            <h4>邀请奖励返佣规则</h4>
                            <ol>
                                <!-- <li>您分享出去的是一个<a href="/?m=hd&a=redpack" target="_blank" style="color:#337ab7;text-decoration:underline">领红包页面</a>，可以免费领取50元红包。</li> -->
                                <li>只需要分享一次，您推荐注册的用户首次消费您将获得10%返佣，以后每笔消费您会获得5%返佣。</li>
                                <li>每月的10-20号为可提现时间，佣金可提现到帐户余额或支付宝。</li>
                                <li>充值返佣即时入账，消费返佣在订单完确认收货后入账。</li>
                                <li>充值后使用余额消费不再记录返佣，因为在充值时已经返了。</li>
                            </ol>
                        </div>
                        <div class="example">
                            <p><b>举个例子：</b></p>
                            <p>A君通过你分享的链接进入25BOY并注册了帐号，第1次消费了600元，那么你将获得60元收益(600x10%,首次收益)；</p>
                            <p>过了一段时间A君又成功消费1000元，那么你将获得50元(1000x5%,后续收益)。</p>
                            <p>以后A君的所有消费你都能获得5%的后续收益。</p>
                        </div>
                    </div>

                    <div class="word-con-box">
                        <div class="inbox">
                            <div class="invite-mine">
                                <div class="invite-unit">
                                    <p class="main-text"><span>{$myProer|count}</span>人</p>
                                    <p class="sub-text">已邀请好友</p>
                                </div>
                                <div class="invite-unit">
                                    <p class="main-text"><span>{$earnings_total|floatval}</span>元</p>
                                    <p class="sub-text">已获得奖励</p>
                                </div>
                            </div>
                            {if !$session_uid}
                            <a class="loginTips" href="javascript:showLoginLayer();">您还没登录，点击这里登录后查询</a>
                            {/if}
                        </div>
                        {if $myProer}
                        <table class="invite-table">
                            <thead>
                                <tr>
                                    <th>好友</th>
                                    <th>状态</th>
                                    <th>时间</th>
                                    <th>带来收益</th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach from=$myProer item=item}
                                <tr>
                                    <td>{$item.nickname}</td>
                                    <td>注册成功</td>
                                    <td>{$item.create_date|date_format:"%y-%m-%d"}</td>
                                    <td>¥{$item.total|string_format:"%.2f"}</td>
                                </tr>
                                {/foreach}
                            </tbody>
                        </table>
                        {/if}
                    </div>

                    <div class="word-con-box">
                        <div class="inbox">
                            <div class="invite-mine">
                                <div class="invite-unit">
                                    <p class="main-text"><span>{$give_total}</span>元</p>
                                    <p class="sub-text">佣金累计发放</p>
                                </div>
                            </div>
                        </div>
                        {if $ranking}
                        <table class="invite-table">
                            <thead>
                                <tr>
                                    <th>排名</th>
                                    <th>用户昵称</th>
                                    <th>累计收益</th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach from=$ranking item=item key=key}
                                <tr>
                                    <td>{$key+1}</td>
                                    <td>{$item.nickname}</td>
                                    <td>{$item.earnings_total|string_format:"%.2f"}</td>
                                </tr>
                                {/foreach}
                            </tbody>
                        </table>
                        {/if}
                    </div>
                </div>
            </div>

            <div class="fl-BtnBox">
                <!-- <div class="share-btn">
                    <a class="weixin" href="javascript:check_share_leyer();"><img src="/statics/img/fl-btn-weixin.png?v={$version}"></a>
                    <a class="quan" href="javascript:check_share_leyer('link');"><img src="/statics/img/fl-btn-link.png?v={$version}"></a>
                    <a class="qrcode" href="javascript:getQrcode();"><img src="/statics/img/fl-btn-qrcode.png?v={$version}"></a>
                </div> -->
                <a class="fl-btn-more" href="/?m=hd&a=promoteCourse"><img src="http://api.25boy.cn/Public/images/fl-btn-more.png?v={$version}"></a>
            </div>

        </div>

    </section>
</div>

{include file="public/js.tpl" title=js}
<link rel="stylesheet" type="text/css" href="/statics/css/promote.css?v={$version}">
<style type="text/css">
body,#bodybox{
    background-color:#fcdd35;
}
</style>
<script type="text/javascript">
$(function(){
    /*内容定位*/
    var bodyWidth = $('body').width();
    var zoom = bodyWidth / 640;
    $('.fl-box').css('top',740*zoom);

    $('.fl-word-tab li').click(function(){
        var eq = $(this).index();
        $('.fl-word-tab li').removeClass('on');
        $(this).addClass('on');
        $('.fl-word-con .word-con-box').hide();
        $('.fl-word-con .word-con-box').eq(eq).show();
    });
});
function check_share_leyer(copy){
    console.log(copy);
    var options = [{
        "url": "/?m=hd&a=promoteApply",data:{
            do:'checkLogin'
        }, "type":"POST", "dataType":"json"
    }];
    Load(options, function(json){
        var errorsummary = '';
        switch(json.status){
            case "nologin":
                layer.open({
                    content: '请登录后获取您的专属返佣链接。'
                    ,btn: ['登录/注册']
                    ,yes: function(){
                        if(iswx){
                            var b = new Base64();
                            var gourl = b.encode(window.location.href);
                            window.location.href='/?m=login&a=weixin.bind&gourl='+gourl;
                        }else{
                            showLoginLayer();
                        }
                        return false;
                    }
                });
                return false;
                break;
            
            case "success":
            case "joined":
                if(iswx && (copy==undefined || copy=='')){
                    show_wx_share_div();
                }else{
                    layer.open({
                        content: '<div class="mobileShareUrlLayer"><p>请长按复制以下链接，粘贴分享</p><code>{$myUrl}</code></div>',
                        style  : 'background:#fff;color:#333;',
                        shade : true,
                        shadeClose :true
                    });
                }
                break;
        }
    });
}
function showLoginLayer(){
    layer.closeAll();
    user.action('by',true);
    user.createpannel();
}
function getQrcode(){
    myPromoteQrcode('p_redpack',function(res){
        if(res.code == '0'){
            layer.open({
                content: '<div class="show_QRcode_Box" style="width:230px;"><img src="http://api.25boy.cn'+res.rs+'" alt="我的返佣二唯码" style="display:block"><p style="color:#000;font-size:16px;font-weight:700;color:red;">领取50元现金红包</p></div>',
                style  : 'background:#fff',
                shade : true,
                shadeClose :true
            });
        }else{
            layer.open({
                content: res.msg,
                btn: ['登录/注册'],
                yes: function(){
                    if(iswx){
                        var b = new Base64();
                        var gourl = b.encode(window.location.href);
                        window.location.href='/?m=login&a=weixin.bind&gourl='+gourl;
                    }else{
                        showLoginLayer();
                    }
                    return false;
                }
            });
        }
    });
}
</script>

{include file="public/footer.tpl" title=footer}