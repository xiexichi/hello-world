{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<div id="bodybox">
    <section class="main pagemain promoteApply">
   
        <div class="img-box">
            <img src="http://img.25miao.com/114/1478749376.jpg!w640" alt="小伙伴大招募" />
        </div>

        <div class="btnBox">
            {if $isPromote}
            <a class="btn" href="/?m=account&a=promote">获取收益链接</a>
            {else}
            <button class="btn" type="button" onclick="applyPromote();">成为推广者</button>
            {/if}
        </div>

        <dl class="instro">
            <dt>成为推广者<br> 好友通过您分享的链接<br> 注册成功并消费（购物/充值）<br> 会给您带来返利</dt>
            <dd><img src="http://img.25miao.com/695/1478406917.jpg" alt="首次收益"></dd>
            <dd><img src="http://img.25miao.com/695/1478406918.jpg" alt="后续收益"></dd>
        </dl>

        <div class="example">
            <p><b>举个例子：</b></p>
            <p>A君通过你分享的链接进入25BOY，然后注册为25BOY会员，第1次成功消费了300，那么你将获得30收益(300元x10%,首次收益)；</p>
            <p>过了一段时间A君又成功消费200，那么你将获得10 收益(200元x5%,后续收益)。</p>
        </div>

        <p class="moreHelp"><a href="/?m=hd&a=promoteCourse">&gt;&gt;更多使用教程&lt;&lt;</a></p>

        <div class="statement">
            活动内容，最终解释权归25BOY所有。
        </div>

    </section>
</div>


{include file="public/js.tpl" title=js}
<script src="/statics/js/account_order.js?v={$version}"></script>
<link rel="stylesheet" type="text/css" href="/statics/css/promote.css?v={$version}">
{literal}
<script>
function applyPromote(){
    var options = [{ "url": "/?m=hd&a=promoteApply",data:{do:'join'}, "type":"POST", "dataType":"json"}];
    Load(options, function(json){
        var errorsummary = '';
        switch(json.status){
            case "nologin":
                layer.open({
                    content: '还没有登录，请登录后操作。'
                    ,btn: ['马上登录']
                    ,shadeClose: false
                    ,yes: function(){
                        if(iswx){
                            var b = new Base64();
                            var gourl = b.encode(window.location.href);
                            window.location.href='/?m=login&a=weixin.bind&gourl='+gourl;
                        }else{
                            layer.closeAll();
                            user.action('by',true);
                            user.createpannel();
                        }
                        return false;
                    }
                });
                return false;
                break;
            
            case "success":
            case "joined":
                layer.open({
                    content: '您已成为推广者，马上开始赚钱吧！'
                    ,btn: ['好的']
                    ,shadeClose: false
                    ,yes:function(){
                        window.location.href='/?m=account&a=promote';
                    }
                });
                break;
        }
    });
}
</script>
{/literal}
{include file="public/footer.tpl" title=footer}