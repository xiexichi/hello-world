{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<div id="bodybox">
    <section class="main pagemain pagegrey" style="background-color: #efefef">
        {if $session_uid==""||$session_uid==0}
            {include file="public/remind_login.tpl" title=header}
        {else}
            <section class="balance_total ycenter">
                <div class="content" style="margin:0 auto;">
                    <div class="totaltext"><span><sup>￥</sup><span>{$balance}</span></span></div>
                    <div class="btnbox">
                        <button id="btn_recharge">我要充值</button>
                    </div>
                </div>
            </section>
            <section class="nav-tags nav-tags-col4 borderbottom">
                <span class="item {if $c=='xf'}current{/if}"><a href="/?m=account&a=balance&c=xf">消费记录</a></span>
                <span class="item {if $c=='cz'}current{/if}"><a href="/?m=account&a=balance&c=cz">充值记录</a></span>
                <span class="item {if $c=='tk'}current{/if}"><a href="/?m=account&a=balance&c=tk">退款记录</a></span>
                <span class="item {if $c=='yf'}current{/if}"><a href="/?m=account&a=balance&c=yf">售后运费</a></span>
            </section>
            {if $total_bag<1}
                <section class="nologinbox">
                    <div class="imgbox"><img src="/statics/img/icon.status.empty.png" width="50%"/></div>
                    <div class="titlebox">无记录！</div>
                </section>
            {else}
                <ul class="balance_list"> </ul>
            {/if}
        {/if}
    </section>
    {if $total_bag>0}
        <div class="loaddiv" {if $session_uid==""||$session_uid==0}style="display: none"{/if}>
            <div class="loading-msg">
                <span>数据加载中请稍后</span>
                <div class="loading-box">
                    <div class="loading" index="0"></div>
                    <div class="loading" index="1"></div>
                    <div class="loading" index="2"></div>
                    <div class="loading" index="3"></div>
                    <div class="loading" index="4"></div>
                </div>
            </div>
        </div>
    {/if}
</div>
<link rel="stylesheet" type="text/css" href="/statics/css/account.css?v={$version}">
{include file="public/js.tpl" title=js}
<script>
    var c = "{$c}";
</script>

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
    <script src="/statics/js/bag.list.js?v={$version}"></script>
    <script src="/statics/js/jquery.animateNumber.min.js"></script>
    <script>
        var decimal_places = 2;
        var decimal_factor = decimal_places === 0 ? 1 : decimal_places * 10;
        $(function(){
            var integral_total = $('.totaltext span span').html();
            $('.totaltext span span').animateNumber({
                number: integral_total * decimal_factor,
                numberStep: function(now, tween) {
                    var floored_number = now / decimal_factor,
                            target = $(tween.elem);
                    if (decimal_places > 0) {
                        floored_number = floored_number.toFixed(decimal_places);
                        /*floored_number = floored_number.toString().replace('.', ',');*/
                    }

                    target.text(floored_number);
                }
            },1000);
            $("#btn_recharge").click(function(){
                window.location.href = "?m=account&a=recharge";
            });
        });
        function show_recharge(){

        }
    </script>
{/literal}
{/if}

{include file="public/footer.tpl" title=footer}