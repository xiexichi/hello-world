{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<style>
    .consume_paid_box {
        width:90%;
        margin:0 auto;
    }
    .consume_paid_box b{
        font-size:2em;
        color:green;
    }
    .consume_paid_pass {
        width:88%;
        height:30px;
        line-height: 30px;
        padding:0.2em 0 0.2em 2.5em;
        background: #eee;
        font-size:13px;
        border:1px solid #ccc;
    }
    .btn_consume_paid {
        width:98%;
        height:40px;
        line-height: 40px;
        border-radius:2px;
        font-weight: normal;
        font-size:20px;
        margin:0;border:1px solid #b01f23;
        background:#b01f23;
        color:#fff;
        padding:0 1em;
        text-align:center; 
    }
</style>

<div id="bodybox">
    <section class="main pagegrey">
        {if $session_uid==""||$session_uid==0}
            {include file="public/remind_login.tpl" title=header}
        {else}
            <div class="account_header">
                <div class="bg"></div>
                <div class="contentbox">
                    <div class="infobox">
                        <a class="qrcode" href="/ajax/get.qrcode.php?act=my" id="btn_qrcode"><i class="iconfont">&#xe689;</i></a>
                        <a class="setting" href="?m=account&a=setting" id="btn_setting"><i class="iconfont">&#xe68c;</i></a>
                        <div class="user_icon"><a href="?m=account&a=setting"><img src="{$user.icon}" width="200" alt="头像" id="user_icon_img" /></a></div>
                        <div class="rightbox">
                            <div class="name">{$user.nickname} <i class="iconfont">{$v_img}</i></div>
                            <!-- <div class="address">{$user.address}</div> -->
                        </div>
                    </div>
                    <div class="btngroup">
                        <span class="item frist"><a href="?m=account&a=favorite">{$user.favorite}<span>关注的产品</span></a></span>
                        <span class="item"><a href="?m=account&a=history">{$user.history}<span>浏览历史纪录</span></a></span>
                    </div>
                </div>

                <div class="orderbox">
                    <div class="itemgroup">
                        <div class="div">
                            <div class="item div_a" rel="?m=account&a=order&s=nopay">
                                <i class="iconfont icon_wallet">&#xe612;</i>
                                <span>待付款</span>
                            </div>
                        </div>
                        <div class="div">
                            <div class="item div_a" rel="?m=account&a=order&s=pack">
                                <i class="iconfont icon_message">&#xe640;</i>
                                <span>待发货</span>
                            </div>
                        </div>
                        <div class="div">
                            <div class="item div_a" rel="?m=account&a=order&s=wait">
                                <i class="iconfont icon_car">&#xe635;</i>
                                <span>待收货</span>
                            </div>
                        </div>
                        <div class="div">
                            <div class="item div_a" style="border: 0" rel="?m=account&a=order&s=return">
                                <i class="iconfont icon_tran">&#xe607;</i>
                                <span>退换货</span>
                            </div>
                        </div>
                    </div>
                </div>
                <a class="account_row" href="?m=account&a=order">
                    <i class="iconfont order">&#xe608;</i>
                    全部订单({$user.order.total})
                    <span class="more fr"></span>
                    <span class="fr">查看全部</span>
                </a>
            </div>

            {if $account_balance=='show'}
                <a class="account_row" href="?m=account&a=balance">
                    <i class="iconfont bag">&#xe65b;</i>
                    我的钱包
                    <span class="more fr"></span>
                    <span class="fr">账户余额 ¥<span id="total_bag">{$user.bag}</span></span>
                </a>
                <!-- <div class="account_row_followbox" style="padding:1em 0;">
                    <div class="p50 cel_3 text-center div_a" rel="?m=account&a=balance">
                        <div class="f-s-22 f-c-000" id="total_bag">{$user.bag}</div>
                        <div class="f-s-16 f-c-999 l-h-2em">账户余额</div>
                    </div>
                    <div class="p30 cel_3 text-center div_a" rel="?m=account&a=integral">
                        <div class="f-s-22 f-c-000" id="total_integral">{$user.integral}</div>
                        <div class="f-s-16 f-c-999 l-h-2em">我的积分</div>
                    </div>
                </div> -->
            {/if}

            <a class="account_row" href="?m=account&a=earnings">
                <i class="iconfont earnings">&#xe621;</i>
                推广返佣
                <span class="more fr">fr</span>
                {if $is_promote=='1'}
                <span class="fr">可提现余额 ¥{$promote['cash_total']}</span>
                {else}
                <span class="fr">查看详情</span>
                {/if}
            </a>
        
            <a class="account_row" href="?m=account&a=coupon">
                <i class="iconfont share">&#xe658;</i>
                我的卡券({$user.coupon})
                <span class="more fr"></span>
                <span class="fr">查看全部</span>
            </a>
            
            <a class="account_row" href="?m=account&a=share">
                <i class="iconfont share">&#xe62f;</i>
                我的晒图
                <span class="more fr"></span>
                <span class="fr">查看全部</span>
            </a>
        {/if}
    </section>
</div>
<link rel="stylesheet" type="text/css" href="/statics/css/account.css?v={$version}">
{include file="public/js.tpl" title=js}
<script src="/statics/js/root.js?v={$version}"></script>
{if $session_uid==""||$session_uid==0}
<script>
$(function(){
    user.by_btn("#cart_user_by","by",true);
})
</script>
{else}
<script src="/statics/js/jquery.animateNumber.min.js"></script>
<script src="/statics/js/account.js?v={$version}"></script>
{/if}
{include file="public/footer.tpl" title=footer}