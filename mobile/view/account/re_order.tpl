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
                    <li rel="">订单筛选</li>
                    <li rel="all" {if $current_title=="全部"}class="selected"{/if}>全部售后订单</li>
                    <li rel="0" {if $current_title=="待支付运费"}class="selected"{/if}>待支付运费</li>
                    <li rel="1" {if $current_title=="等待发货"}class="selected"{/if}>等待发货</li>
                    <li rel="2" {if $current_title=="等待收货"}class="selected"{/if}>等待收货</li>
                    <li rel="8" {if $current_title=="交易成功"}class="selected"{/if}>交易完成</li>
                </ul>
            </section>
            {if $order|count>0}
                <section class="order_list">
                    <ul>
                        {section name=item loop=$order}
                        <li>
                            <div class="infobox">
                                <div class="itemno">
                                    <span class="org_sn">原订单号：{$order[item].relation_order}</span>
                                    <br/>
                                    售后单号：<strong>{$order[item].order_sn}</strong>
                                    <br/>
                                    {if $order[item].order_time>0 && $order[item].status ==0}
                                    <span class="f-c-999 distancetime" order_time="{$order[item].order_time}" show_date="{$order[item].order_date}">{$order[item].order_date}  <strong class="f-c-main" style="padding: 0 0 0 10px; {if $order[item].order_time==0}display: none{/if}">(还剩过期)</strong></span>
                                    {else}
                                    <span class="f-c-999 distancetime">{$order[item].order_date}</span>
                                    {/if}
                                </div>

                                {if $order[item].reout=='expback' && $order[item].status==7 && $order[item].return_num==2}
                                    <span class="statusbox {$spanbg[$order[item].status]}">同意换出</span>
                                {elseif $order[item].reout=='expback' && $order[item].status==7 && $order[item].return_num==3}
                                    <span class="statusbox {$spanbg[$order[item].status]}">拒绝换出</span>
                                {elseif $order[item].status==7 && $order[item].return_num==6}
                                    <span class="statusbox {$spanbg[$order[item].status]}">换货处理中</span>
                                {elseif $order[item].status==5 && $order[item].return_num==6}
                                    <span class="statusbox {$spanbg[$order[item].status]}">退货处理中</span>
                                {elseif $order[item].status==1 && $order[item].reout=='shenhe'}
                                    <span class="statusbox {$spanbg[$order[item].status]}">商品打包中</span>
                                {elseif $order[item].status==-1 && $order[item].reout=='endrefuse'}
                                    <span class="statusbox {$spanbg[$order[item].status]}">交易关闭(拒绝退货)</span>
                                {else}
                                    <span class="statusbox {$spanbg[$order[item].status]}">{$order[item].status_name}</span>
                                {/if}



                            </div>
                            {if $order[item].order_items|count>0}
                            <div class="order_list_box" onclick="window.location.href='/?m=account&a=order_detail&order_id={$order[item].order_id}'">
                                {section name=itemlist loop=$order[item].order_items}
                                <div class="innerbox">
                                    <div class="imgbox"><a href="/?m=category&a=product&id={$order[item].order_items[itemlist].product_id}"><img src="{$order[item].order_items[itemlist].color_photo}" /></a></div>
                                    <div class="itemsummary">
                                        <p class="title">{$order[item].order_items[itemlist].product_name}</p>
                                        <p class="model">颜色：{$order[item].order_items[itemlist].color_prop}</p>
                                        <p class="model">尺码：{$order[item].order_items[itemlist].size_prop}</p>
                                        <p class="model">数量：{$order[item].order_items[itemlist].num} 件</p>
                                    </div>
                                </div>
                                {/section}
                            </div>
                            {/if}
                            {if $order[item].order_items|count>1}
                            <div class="order_list_more ycenter" rel="close">
                                <div class="textbox">
                                    共{$order[item].order_totalnum}件商品
                                    {if $order[item].delivery['is_there'] == 1}
                                        （{$order[item].delivery['delivery_name']}）
                                    {else} 
                                        运费：<sup>￥</sup>{$order[item].pay_total}{if $order[item].delivery != ''}（{$order[item].delivery['delivery_name']}）{/if}
                                    {/if}
                                    </div>
                                    <span></span>
                                </div>
                            {else}
                            <div class="order_list_more ycenter disopen" rel="close">
                                <div class="textbox">
                                    共{$order[item].order_totalnum}件商品
                                    {if $order[item].delivery['is_there'] == 1}
                                        （{$order[item].delivery['delivery_name']}）
                                    {else}    
                                        运费：<sup>￥</sup>{$order[item].pay_total}{if $order[item].delivery != ''}（{$order[item].delivery['delivery_name']}）{/if}
                                    {/if}
                                </div>
                            </div>
                            {/if}
                            <div class="btn_box bordertop">
                                {if $order[item].status == 0}
                                    <span class="noticebox">七天无理由退换，需要买家承担来回运费。</span>
                                    <a class="btn_paynow a_btn a_btn_import fr" order_id="{$order[item].order_id}" data-sn="{$order[item].order_sn}">支付运费</a>
                                    <a class="btn_cancle a_btn mgr-20 fr" order_id="{$order[item].order_id}" href="javascript:;" status="{$order[item].status}" style="margin-right:0;">取消退换</a>
                                {elseif $order[item].status == 1}
                                    {if $order[item].orgOrder.reout!='expback'}
                                        <span class="noticebox">请7天内寄回商品,并填写物流单号。</span>
                                        <a class="btn_shipreturn a_btn a_btn_import fr" order_id="{$order[item].orgOrder.order_id}" href="javascript:;" >寄回商品</a>
                                    {elseif $order[item].orgOrder.reout=='expback' && $order[item].orgOrder.return_num==6}
                                        <span class="noticebox">商品已经寄回，我们会第一时间安排换出。</span>
                                    {elseif $order[item].orgOrder.reout=='expback' && $order[item].orgOrder.return_num==3}
                                        <span class="noticebox">换货申请被拒绝，详情请登录电脑版查看。</span>
                                        <a href="javascript:;" order_id="{$order[item].order_id}" class="a_btn a_btn_import fr btnconfirm">确定交易</a>
                                        <a class="btn_tuihuan a_btn fr" status="{$order[item].status}" order_id="{$order[item].order_id}" href="javascript:;" >重新申请</a>
                                    {elseif $order[item].orgOrder.reout=='expback' && $order[item].orgOrder.return_num==2}
                                        <span class="noticebox">已同意换出，正在为您安排发货。</span>
                                    {/if}
                                {elseif $order[item].status == 2}
                                    <span class="noticebox">商品已经寄出，请注意查收。</span>
                                    {*<a class="a_btn fl" order_id="{$order[item].order_id}" href="javascript:;" >查询物流</a>*}
                                    <a class="btnconfirm a_btn a_btn_import fr" order_id="{$order[item].order_id}" href="javascript:;">确定收货</a>
                                {elseif $order[item].status == -1}
                                    <span class="noticebox">抱歉，您的订单不符合换货条例，交易关闭。</span>
                                {elseif $order[item].status == 8}
                                    <span class="noticebox">此订单已经完成，感谢您的支持。</span>
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
{literal}
    <script src="/statics/js/account_re_order.js?v={$version}"></script>
{/literal}
{/if}

{include file="public/footer.tpl" title=footer}