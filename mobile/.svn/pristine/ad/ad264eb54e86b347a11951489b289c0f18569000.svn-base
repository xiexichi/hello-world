<nav id="orderbottom" class="nav_in cartbottom" {if $session_uid==""||$session_uid==0}style="display: none"{/if}>
    <form action="?m=pay" method="post" id="payform" style="display: none">
        <input type="hidden" name="default_address_id" id="default_address_id" value="{$default_ID}" />
        <input type="hidden" name="cart_id" id="cart_id" value="{$cart_id}" />
        <input type="hidden" name="balance" id="balance" value="0" />
        <input type="hidden" name="delivery_id" id="delivery_id" value="{$default_delivery_id}" />
    </form>
    <form action="wxpay.php" method="get" id="wxpayform" style="display: none">
        <input type="hidden" name="order_id" id="order_id" value="" />
    </form>
    {if $account_balance=='show'}

    <div class="order_belance">
        <span class="checkbox" id="user_balance" authentication="0" enabledcheck="1" ismerge="0"></span>      
        <div class="remin_balance">
            <div class="balancebox">使用钱包支付 （余额：¥<span id="total_balance">{$balance}</span>）</div>
        </div>
     </div>

     {/if}
    <div class="selectall fl">
        <div class="selectstatus">
            实付款：<sup>￥</sup><span id="shiji" data-total="{$total_price_and_ship}">{number_format($total_price_and_ship,2,'.','')}</span>
            <strong class="f-c-main" style="display: none">-￥0</strong>
            <strong class="f-c-main" id="total_ship_fee" style="display: none">+0</strong>
        </div>
    </div>

    <a href="javascript:;" class="btn_text btn_buynow fr" >确认并付款</a>
</nav>
