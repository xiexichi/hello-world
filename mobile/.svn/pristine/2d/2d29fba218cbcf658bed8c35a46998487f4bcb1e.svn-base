{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<!-- o2o公共js -->
<script src="/statics/js/o2o/common.js?v={$version}"></script>

<div id="bodybox">
    <section class="main pagemain">
        {if $session_uid==""||$session_uid==0}
            {include file="public/remind_login.tpl" title=header}
        {else}
            <section class="order_class borderbottom" show="0">
                <span class="arrow"></span>
                <ul>
                    <li rel="" style="background:#f0f0f0;">订单筛选</li>
                    <!-- <li rel="all" {if $current_title=="全部"}class="selected"{/if}>全部订单</li>
                    <li rel="0" {if $current_title=="待付款"}class="selected"{/if}>待付款</li>
                    <li rel="1" {if $current_title=="等待发货"}class="selected"{/if}>等待发货</li>
                    <li rel="2" {if $current_title=="待收货"}class="selected"{/if}>待收货</li>
                    <li rel="4" {if $current_title=="退款"}class="selected"{/if}>退款订单</li>
                    <li rel="7" {if $current_title=="退货"}class="selected"{/if}>退货订单</li>
                    <li rel="6" {if $current_title=="换货"}class="selected"{/if}>换货订单</li>
                    <li rel="3" {if $current_title=="交易成功"}class="selected"{/if}>交易成功</li>
                    <li rel="-1" {if $current_title=="交易关闭"}class="selected"{/if}>交易关闭</li> -->
                    
                    <li rel="all" {if $s == "1000"}class="selected"{/if}>全部订单</li>
                    {foreach from=$order_status key="k" item="i"}
                        <li rel="{$k}" {if $s eq $k}class="selected"{/if}>{$i}</li>
                    {/foreach}

                </ul>
            </section>
            {if $order|count>0}
                <section class="order_list">
                    <ul id="order_list_ul">
                        {section name=item loop=$order}
                        <li>
                            <div class="infobox">
                                <div class="itemno" style="position: relative;">
                                    单号：<strong>{$order[item].order_sn}</strong>
                                    <i class="iconfont btn_remark" order_id="{$order[item].order_id}" data-sn="{$order[item].order_sn}" data-color="{$order[item].flag_color}" data-remark="{$order[item].remark}" style="color:#{$order[item].flag_color};font-size:1.5em;position:absolute;top:0;left:9.5em;line-height:0.5em;">&#xe64f;</i>
                                    <br/>
                                    {if $order[item].order_time>0 && $order[item].status ==0}
                                    <span class="f-c-999 distancetime" order_time="{$order[item].order_time}" show_date="{$order[item].order_date}">{$order[item].order_date}  <strong class="f-c-main" style="{if $order[item].order_time==0}display: none{/if}">(还剩过期)</strong></span>
                                    {else}
                                    <span class="f-c-999 distancetime">{$order[item].order_date}</span>
                                    {/if}
                                </div>
                                {if $order[item].is_return == '1'}
                                    <span class="statusbox disbg">退货中</span>
                                {elseif $order[item].status == '4'}
                                    <span class="statusbox disbg">退款中</span>
                                {elseif $order[item].status==5}
                                    <span class="statusbox normalbg">退款完成</span>
                                {elseif $order[item].status==1}
                                    <span class="statusbox normalbg">商品打包中</span>
                                {elseif $order[item].status==-1}
                                    <span class="statusbox disbg">交易关闭(拒绝退货)</span>
                                {else}
                                    <span class="statusbox {$spanbg[$order[item].status]}">{$order[item].status_name}</span>
                                {/if}

                            </div>
                            {if $order[item].order_items|count>0}
                            <div class="order_list_box" onclick="window.location.href='/?m=account&a=o2o_order_detail&order_id={$order[item].order_id}'">
                                {section name=itemlist loop=$order[item].order_items}
                                <div class="innerbox">
                                    <div class="imgbox"><img src="{$order[item].order_items[itemlist].color_photo}" alt="{$order[item].order_items[itemlist].product_name}" /></div>
                                    <div class="itemsummary">
                                        <p class="title">{$order[item].order_items[itemlist].product_name}</p>
                                        <p class="model">颜色：{$order[item].order_items[itemlist].color_prop}</p>
                                        <p class="model">尺码：{$order[item].order_items[itemlist].size_prop}{$order[item].order_items[itemlist].presale_date}</p>
                                        <p class="model">单价：<sup>￥</sup>{$order[item].order_items[itemlist].price} x {$order[item].order_items[itemlist].quantity}</p>
                                    </div>
                                </div>
                                {/section}
                            </div>
                            {/if}
                            {if $order[item].order_items|count>1}
                            <div class="order_list_more ycenter" rel="close">
                                <div class="textbox">
                                共{$order[item].order_totalnum}件商品&nbsp;支付:<sup>￥</sup><strong style="font-size: 1.2em;">{$order[item].pay_total}</strong>
                                {if $order[item].delivery['is_there'] == 1}
                                    （{$order[item].delivery['delivery_name']}）
                                {else}   
                                    {if $order[item].ship_price > 0}
                                        &nbsp;(含运费:{$order[item].ship_price}元{if $order[item].delivery != ''}{/if})
                                    {else}
                                        &nbsp;(包邮{if $order[item].delivery != ''}{/if})
                                    {/if}
                                {/if}
                                </div>
                                <span></span>
                            </div>
                            {else}
                            <div class="order_list_more ycenter disopen" rel="close">
                                <div class="textbox">
                                共{$order[item].order_totalnum}件商品&nbsp;支付:<sup>￥</sup><strong style="font-size: 1.2em;">{$order[item].pay_total}</strong>
                                {if $order[item].delivery['is_there'] == 1}
                                    （{$order[item].delivery['delivery_name']}）
                                {else}
                                    {if $order[item].ship_price > 0}
                                        &nbsp;(含运费:{$order[item].ship_price}元{if $order[item].delivery != ''}{/if})
                                    {else}
                                        &nbsp;(包邮{if $order[item].delivery != ''}{/if})
                                    {/if}
                                    
                                {/if}
                                </div>
                            </div>
                            {/if}
                            <div class="btn_box">


                                {if $order[item].status == 0}
                                    <a class="btn_cancle a_btn mgr-20 fl" order_id="{$order[item].order_id}" href="javascript:;" status="{$order[item].status}" >取消订单</a>
                                    
                                    {if $order[item].order_type == 'issuing'}
                                        <a class="a_btn a_btn_import fr" href="/?m=account&a=o2o_order_detail&order_id={$order[item].order_id}" onclick="showLoading()">立即付款</a>
                                    {else}
                                        <a class="btn_paynow a_btn a_btn_import fr" order_id="{$order[item].order_id}" type="{$order[item].order_type}" data-sn="{$order[item].order_sn}">立即付款</a>
                                    {/if}

                                {elseif $order[item].status == 1}
                                    <span class="noticebox">订单已创建，我们正在为您安排发货。</span>
                                    <!-- {if $order[item].reout != "shenhe"}
                                        <a class="btn_tuikuan a_btn fr" order_id="{$order[item].order_id}" href="javascript:;">申请退款</a>
                                    {/if} -->

                                {elseif $order[item].status == 2}
                                    <!-- {if $order[item].is_seller}
                                    <a class="btn_tuihuan a_btn fl" status="{$order[item].status}" order_id="{$order[item].order_id}" href="javascript:;" >申请退换</a>
                                    {/if} -->
                                    <a class="btnconfirm a_btn a_btn_import fr" order_id="{$order[item].order_id}" href="javascript:;" >确定收货</a>
                                {elseif $order[item].status == 5}
                                    <span class="noticebox">资金已经退回您的帐户，关闭结束。</span>
                                {elseif $order[item].status == 3}
                                    <span class="noticebox">交易完成，感谢您的支持！</span>
                                    {*<a class="btn_pingjia a_btn fl" order_id="{$order[item].order_id}" href="javascript:;">评价</a>*}
                                {/if}

                            </div>

                        </li>
                        {/section}
                    </ul>
                </section>

                {$showPage}
                
            {else}
                <div class="empty-content"><i class="iconfont"></i></div>
            {/if}
        {/if}
    </section>

</div>


<link rel="stylesheet" type="text/css" href="/statics/css/account.css?v={$version}">
{include file="public/js.tpl" title=js}
<script type="text/javascript">
var account_balance = '{$account_balance}';
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
<script src="/statics/js/account_o2o_order.js?v={$version}"></script>
{/if}

{include file="public/footer.tpl" title=footer}