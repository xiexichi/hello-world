{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<div id="bodybox">
    <section class="main pagemain pagegrey">
        {if $session_uid==""||$session_uid==0}
            {include file="public/remind_login.tpl" title=header}
        {else}

            <!-- <section class="balance_total ycenter bg_danger">
                <div class="content" style="margin:0 auto;">
                    <div class="totaltext" style="font-size:3em">轻松推广,轻松赚钱</div>
                </div>
            </section> -->
            
            <section class="earnings_detail">
                <ul class="balance_list">
                    {foreach $earnings_detail as $v}
                    <li>
                        <p class="earnings_detail_header">
                            <span class="earnings_detail_type">
                            {if $v['earnings_type'] == 're_shopping'}
                            购物返佣
                            {elseif $v['earnings_type'] == 're_recharge'}
                            充值返佣
                            {/if}
                            </span>
                            <span class="right earnings_detail_price">¥{$v['earnings']}</span>
                        </p>
                        <p class="earnings_detail_body">
                        项目：
                        {if $v['earnings_type'] == 're_shopping'}
                        {$v['product_name']}
                        {elseif $v['earnings_type'] == 're_recharge'}
                            {if $v['method'] == 'alipay'}
                            支付宝充值  
                            {/if}
                        {/if}
                        </p>
                        <p class="earnings_detail_body">金额：¥{$v['re_price']}&nbsp;&nbsp;佣金比率：{$v['commission_rate']}%</p>
                        <p class="earnings_detail_body">收益时间：{$v['received_time']}
                            <span class="right">
                            {if $v['is_get']}
                            已结算
                            {else}
                            未结算
                            {/if}
                            
                            </span>
                        </p>
                    </li>

                    

                    {foreachelse}
                    <div  class="nodata">暂无数据！</div>
                    {/foreach}
                </ul>
                
            </section>
                    <div class="loaddiv" style="display: none;">
                        <div class="blank10"></div>
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

    </section>

<div class="shade">
    <img src="/statics/img/ani_arrow.gif" class="shade-ani">
    <img src="/statics/img/pointer_click.png" class="shade-pointer"/>
</div>  

   
<link rel="stylesheet" type="text/css" href="/statics/css/account.css">
<link rel="stylesheet" type="text/css" href="/statics/css/promote.css">

{include file="public/js.tpl" title=js}
<script src="/statics/js/earnings_detail.js?v={$version}"></script>
<script src="/statics/js/promote.module.js?v={$version}"></script>

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
<script src="/statics/js/account_order.js?v={$version}"></script>
{/if}


{include file="public/footer.tpl" title=footer}