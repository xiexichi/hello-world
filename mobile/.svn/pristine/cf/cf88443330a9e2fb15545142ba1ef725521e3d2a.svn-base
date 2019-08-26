{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<body class="{if $session_uid==""||$session_uid==0}pagebgwhite{else}pagegrey{/if}">
<div id="bodybox">
    <section class="main pagemain {if $session_uid==""||$session_uid==0}pagebgwhite{else}pagegrey{/if}">
        {if $session_uid==""||$session_uid==0}
            {include file="public/remind_login.tpl" title=header}
        {else}
            {if $default_address|count>0}
            <div class="default_address" id="address_review">
                <div class="title">收货地址<span>></span></div>
                <div class="row"><strong>{$default_address.receiver_name}</strong></div>
                <div class="row">{$default_address.receiver_phone}</div>
                <div class="row">{$default_address.state_name} {$default_address.city_name} {$default_address.district_name} {$default_address.address}</div>
                
            </div>
            {else}
            <div class="default_address" id="address_review">
                <div class="title">收货地址<span>></span></div>
                <div class="row"><a href="javascript:;" class="btn btn_mini" id="btn_add_address">添加收货地址</a></div>
            </div>
            {/if}
            <div class="default_address shipping_type">
                <div class="title">配送方式</div>
                {foreach from=$delivery key=k item=v name=delivery}
                <div class="row">
                    <label>
                        <span class="name"><input type="radio" name="delivery" data-there="{$v['is_there']}" data-name="{$v['delivery_name']}" value="{$k}" {if $v['is_default']}checked="checked"{else}disabled="disabled"{/if}>
                        {$v['delivery_name']}</span>
                        {$v['delivery_desc']}
                    </label>
                </div>
                {/foreach}
            </div>
            
            <div class="order_row product_list" id="product_review">
                <ul>
                    {foreach from=$products key=index item=item name=cart_product}
                    <li>
                        <div class="productitembox" data-goods="{$item.product_id}x{$item.quantity}">
                            <div class="discountbox">
                                {foreach from=$item['event_title'] key=k item=v name=event_title}
                                <p style="text-align: left;padding:2px 0;color:#f60;">{$v}</p>
                                {/foreach}
                            </div>
                            <div class="imgbox"><img src="{$item.thumb}!w200" alt="{$item.product_name}"></div>
                            <div class="detailbox">
                                <div class="product_name_box">{$item.product_name}</div>
                                <div class="prop_box">{$item.color_prop}，{$item.size_prop}</div>
                                {if $item['presale']==1}
                                <div class="presale_box"><font color="red">[预售,{$item.presale_date}发货]</font></div>
                                {/if}
                                {if $item['miao_price'] > 0}
                                <div class="price_box"> <del><sup>￥</sup>{$item.product_price}</del> <sup>￥</sup>{$item.miao_price} x {$item.quantity}</div>
                                {else}
                                <div class="price_box"> <sup>￥</sup>{$item.product_price} x {$item.quantity}</div>
                                {/if}
                            </div>
                        </div>
                    </li>
                    {/foreach}
                </ul>
            </div>
            {if $coupon_list_json!='[]' && $coupon_list_json!=''}
            <div class="order_row totalrow" id="coupon_review">
                <span></span>
                <div class="totaltext fr" id="coupon_use" data-val="0"><font color="red">请选择代金券</font></div>
                <div class="totaltext fl">代金券</div>
            </div>
            <div class="order_remind" id="coupon_review_tips">注: 不可用的代金券不会显示。</div>
            {/if}
            
            {if $coupon_list && $coupon_list!=''}
            <div class="order_row">
                <div id="user_coupon_list">
                    <ul>
                        {foreach from=$coupon_list key=index item=item name=coupon_list}
                        <li id="coupon_item_{$item.coupon_id}" onclick="use_coupon({$item.coupon_id})" data-val="{$item.coupon_id}">
                            <div class="couponitembox">
                                <div class="coupon_name_box">{$item.coupon_title}</div>
                                <div class="intro_box">满 {$item.price_limit} 减 {$item.coupon_price}</div>
                                <div class="expdate_box">有效期至：{$item.exp_date}</div>
                            </div>
                        </li>
                        {/foreach}
                    </ul>
                </div>
                <div class="order_remind" id="coupon_review_tips">注: 不可用的代金券不会显示。<!-- <font onclick="window.location.reload();" style="color:#da3335;float:right;">无法使用？点击刷新</font> --></div>
            </div>
            {/if}

            <div class="order_row totalrow youhui_box" style="display:none;">
                <div class="totaltext fl youhui_div" {if $total_event_price<=0}style="display:none;"{/if}>优惠组合：-<strong class="f-c-main" id="youhui" data-val="{$total_event_price}">{$total_event_price}</strong> 元</div>
                <div class="totaltext fr voucher_div" style="display:none;">代金券：-<strong class="f-c-main" id="voucher">0</strong>元</div>
            </div>

            {if $prize_cut_total>0}
            <div class="order_row totalrow prizes_box">
                <div class="totaltext fl prizes_div">礼品奖励：-<strong class="f-c-main" id="prizes" data-val="{$prize_cut_total}">{$prize_cut_total}</strong> 元</div>
            </div>
            {/if}

            <div class="order_row totalrow">
                <div class="totaltext fl">商品金额：<strong class="f-c-main" id="sub_total" data-val="{$total_price}"><sup>￥</sup>{$total_price}</strong> 元</div>
                <div class="totaltext fr">运费：<strong class="f-c-main" id="ship_fee"><sup>￥</sup>{$ship_price}</strong></div>
            </div>

            <div class="order_row totalrow">
                <div class="totaltext">
                    <label for="buyer_here">买家留言：</label>
                    <input type="text" style="border:0;width: 75%;color:#333;" placeholder="备注留言" id="buyer_note">
                </div>
            </div>

            <div class="order_remind"><a href="/h5/subscribe.html">提示: 使用微信登录并绑定二五帐号，全场免运费！</a></div>
            <span id="consume_line" style="display: none;" data-val="{if $seller}{$seller.consume_line}{else}-1{/if}"></span>
            

            <div id="temp_address_list" style="display: none;">
                <ul>
                    {if $address_list|count>0}
                    {section name=item loop=$address_list}
                        <li>
                            <span class="address" state_id="{$address_list[item].state_id}" city_id="{$address_list[item].city_id}" district_id="{$address_list[item].district_id}">{$address_list[item].state_name}{$address_list[item].city_name}{$address_list[item].district_name}{$address_list[item].address}</span>
                            <span class="address">{$address_list[item].address}</span>
                            <span class="address_id">{$address_list[item].address_id}</span>
                            <span class="receiver_name">{$address_list[item].receiver_name}</span>
                            <span class="receiver_phone">{$address_list[item].receiver_phone}</span>
                            <span class="addre_id">{$address_list[item].state_name}</span>
                        </li>
                    {/section}
                    {/if}
                </ul>
            </div>

            <span class="blank20"></span><span class="blank20"></span><span class="blank20"></span><span class="blank20"></span><span class="blank20"></span>
        {/if}
    </section>
</div>

{assign var="display_order_bottom" value="show"}
{if ($prizes && count($prizes)>0)}
<div id="prizes_limit_tips">
  <div class="prizes_backdrop"></div>
  {foreach from=$prizes key=index item=prize name=prizes}
  {if ($prize['count_num'] > $prize['quantity'])}
    {literal}
    <script type="text/javascript">
    document.getElementById("prizes_limit_tips").style.display="block";
    </script>
    {/literal}
  {$display_order_bottom='hide'}
  <div class="tips_box">
    <p>您获得【{$prize.title}】，总共可领取<b>{$prize.quantity}</b>件，您购物车符合领取条件的数量超出了限制。</p>
    <p>如需要请分成两张订单拍下，您不必担心运费，绑定微信号是免运费的</p>
    <p><a class="btn" href="/?m=cart">返回购物车修改</a></p>
  </div>
  {/if}
  {/foreach}
</div>
{/if}

{include file="public/js.tpl" title=js}
{if $session_uid==""||$session_uid==0}
{literal}
    <script>
        $(function(){
            user.by_btn("#cart_user_by","by",true);
        });
    </script>
{/literal}
{else}
<script type="text/javascript">
    var combomealId = '{$combomeal['combomeal_id']}';
    var ccp = '{$combomeal_choose_products}';
</script>
<script defer src="/statics/js/combomealOrder.js?v={$version}"></script>
{/if}
{if $display_order_bottom!='hide'}
{include file="public/order.bottom.tpl" title=cartbottom}
{/if}

{include file="public/footer.tpl" title=footer}