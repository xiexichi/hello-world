{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<div id="bodybox">
    <section class="main pagemain">
        {if $session_uid==""||$session_uid==0}
            {include file="public/remind_login.tpl" title=header}
        {else}
            <section class="order_class borderbottom" show="0">
                <span class="arrow"></span>
                <ul>
                    <li rel="" style="background:#f0f0f0;">订单筛选</li>
                    <li rel="all" {if $current_title=="全部"}class="selected"{/if}>全部订单</li>
                    <li rel="0" {if $current_title=="待付款"}class="selected"{/if}>待付款</li>
                    <li rel="1" {if $current_title=="等待发货"}class="selected"{/if}>等待发货</li>
                    <li rel="2" {if $current_title=="待收货"}class="selected"{/if}>待收货</li>
                    <li rel="3" {if $current_title=="退款"}class="selected"{/if}>退款订单</li>
                    <li rel="5" {if $current_title=="退货"}class="selected"{/if}>退货订单</li>
                    <li rel="7" {if $current_title=="换货"}class="selected"{/if}>换货订单</li>
                    <li rel="8" {if $current_title=="交易成功"}class="selected"{/if}>交易成功</li>
                    <li rel="-1" {if $current_title=="交易关闭"}class="selected"{/if}>交易关闭</li>
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
                                {if $order[item].reout=='expback' && $order[item].status==7 && $order[item].return_num==2}
                                    <span class="statusbox disbg">同意换出</span>
                                {elseif $order[item].reout=='expback' && $order[item].status==7 && $order[item].return_num==3}
                                    <span class="statusbox disbg">拒绝换出</span>
                                {elseif $order[item].status==7 && $order[item].return_num==6}
                                    <span class="statusbox normalbg">换货处理中</span>
                                {elseif $order[item].status==5 && $order[item].return_num==6}
                                    <span class="statusbox normalbg">退货处理中</span>
                                {elseif $order[item].status==1 && $order[item].reout=='shenhe'}
                                    <span class="statusbox normalbg">商品打包中</span>
                                {elseif $order[item].status==-1 && $order[item].reout=='endrefuse'}
                                    <span class="statusbox disbg">交易关闭(拒绝退货)</span>
                                {else}
                                    <span class="statusbox {$spanbg[$order[item].status]}">{$order[item].status_name}</span>
                                {/if}

                            </div>
                            {if $order[item].order_items|count>0}
                            <div class="order_list_box" onclick="window.location.href='/?m=account&a=order_detail&order_id={$order[item].order_id}'">
                                {section name=itemlist loop=$order[item].order_items}
                                <div class="innerbox">
                                    <div class="imgbox"><img src="{$order[item].order_items[itemlist].color_photo}" alt="{$order[item].order_items[itemlist].product_name}" /></div>
                                    <div class="itemsummary">
                                        <p class="title">{$order[item].order_items[itemlist].product_name}</p>
                                        <p class="model">颜色：{$order[item].order_items[itemlist].color_prop}</p>
                                        <p class="model">尺码：{$order[item].order_items[itemlist].size_prop}{$order[item].order_items[itemlist].presale_date}</p>
                                        <p class="model">单价：<sup>￥</sup>{$order[item].order_items[itemlist].price} x {$order[item].order_items[itemlist].num}</p>
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
                                    <a class="btn_paynow a_btn a_btn_import fr" order_id="{$order[item].order_id}" data-sn="{$order[item].order_sn}">立即付款</a>

                                {elseif $order[item].status == 1}
                                    <span class="noticebox">订单已创建，我们正在为您安排发货。</span>
                                    {if $order[item].reout != "shenhe"}
                                        <a class="btn_tuikuan a_btn fr" order_id="{$order[item].order_id}" href="javascript:;">申请退款</a>
                                    {/if}

                                {elseif $order[item].status == 2}
                                    {if $order[item].is_seller != 2}
                                    <a class="btn_tuihuan a_btn fl" status="{$order[item].status}" order_id="{$order[item].order_id}" href="javascript:;" >申请退换</a>
                                    {/if}
                                    <a class="btnconfirm a_btn a_btn_import fr" order_id="{$order[item].order_id}" href="javascript:;" >确定收货</a>
                                {elseif $order[item].status == 4}
                                    <span class="noticebox">资金已经退回您的帐户，关闭结束。</span>
                                {elseif $order[item].status == 8}
                                    <span class="noticebox">交易完成，感谢您的支持！</span>
                                    {*<a class="btn_pingjia a_btn fl" order_id="{$order[item].order_id}" href="javascript:;">评价</a>*}

                                {elseif $order[item].status == 7}
                                    {if $order[item].return_num == 0}
                                        <span class="noticebox">您申请了退换货，我们正在为您处理。</span>
                                    {elseif $order[item].return_num == 2 && $order[item].reout != 'expback'}
                                        {if $order[item].saOrder.order_sn && $order[item].saOrder.status==0}
                                            <span class="noticebox">已同意换货，关联售后订单：<a href="/?m=account&a=re_order">{$order[item].saOrder.order_sn}</a></span>
                                            <a class="btn_paynow a_btn a_btn_import fr" order_id="{$order[item].saOrder.order_id}" data-sn="{$order[item].saOrder.order_sn}">支付运费</a>
                                        {elseif $order[item].saOrder.order_sn && $order[item].saOrder.status==1}
                                            <span class="noticebox">请7天内寄回商品并填写物流单号，关联售后订单：<a href="/?m=account&a=re_order">{$order[item].saOrder.order_sn}</a></span>
                                            <a class="btn_shipreturn a_btn a_btn_import fr" order_id="{$order[item].order_id}" href="javascript:;" >寄回商品</a>
                                        {/if}
                                    {elseif $order[item].return_num==6 && $order[item].reout == 'expback'}
                                        <span class="noticebox">关联售后订单：<a href="/?m=account&a=re_order">{$order[item].saOrder.order_sn}</a></span>
                                    {elseif $order[item].return_num == 3}
                                        {if $order[item].history.0.condition=='again'}
                                            <span class="noticebox">换货申请被拒绝，详情请登录电脑版查看。</span>
                                            <a href="javascript:;" order_id="{$order[item].order_id}" class="a_btn a_btn_import fr btnconfirm">确定交易</a>
                                            <a class="btn_tuihuan a_btn fr" status="{$order[item].status}" order_id="{$order[item].order_id}" href="javascript:;" >重新申请</a>
                                        {elseif $order[item].history.0.condition=='end'}
                                            <span class="noticebox">换货申请被拒绝，详情请登录电脑版查看。</span>
                                            <a href="javascript:;" order_id="{$order[item].order_id}" class="a_btn a_btn_import fr btnconfirm">确定交易</a>
                                        {else}
                                            <span class="noticebox">关联售后订单：<a href="/?m=account&a=re_order">{$order[item].saOrder.order_sn}</a></span>
                                        {/if}
                                    {elseif $order[item].return_num == 2 && $order[item].reout == 'expback'}
                                        <span class="noticebox">关联售后订单：<a href="/?m=account&a=re_order">{$order[item].saOrder.order_sn}</a></span>
                                    {/if}

                                {elseif $order[item].status == 5}
                                    {if $order[item].return_num == 4}
                                        <span class="noticebox">请7天内寄回商品,并填写物流单号。</span>
                                        <a class="btn_shipreturn a_btn a_btn_import fr" order_id="{$order[item].order_id}" href="javascript:;" >寄回商品</a>
                                    {elseif $order[item].return_num == 5}
                                        <span class="noticebox">抱歉，您的商品无法退货！</span>
                                        <a href="javascript:;" order_id="{$order[item].order_id}" class="a_btn a_btn_import fr btnconfirm">确定交易</a>
                                    {/if}
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
<script src="/statics/js/account_order.js?v={$version}"></script>
{/if}

{include file="public/footer.tpl" title=footer}