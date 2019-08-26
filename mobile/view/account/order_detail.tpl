{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<div id="bodybox">
    <section class="main pagemain">
        {if $session_uid==""||$session_uid==0}
            {include file="public/remind_login.tpl" title=header}
        {else}
	        <div class="order_detail_box">
		        <div class="order_status">
	        		{if $order['status']==0}
		        		<i class="iconfont pay">&#xe63b;</i>
		        		<p>未付款</p>
		        		<p class="subcon">{$auto_close_time}</p>
		        	{elseif $order['status']==1}
		        	{if $order['relation_order_id']}
			        	{if $order['relation_reout']=='expback'}
			        		<i class="iconfont wait">&#xe63a;</i>
			        		<p>等待发货</p>
			        		<p class="subcon">订单已经付款，正在配货中</p>
			        	{else}
			        		<i class="iconfont return2">&#xe62a;</i>
			        		<p>待寄回商品</p>
			        		<p class="subcon">请寄回商品并填写快递单号</p>
			        		<p class="subcon">{$re_timeout}</p>
			        	{/if}
		        	{else}
		        		<i class="iconfont wait">&#xe63a;</i>
		        		{if $order['location'] == ''}
		        			<p>已经付款</p>
			        		<p class="subcon">请完善您的收货地址。</p>
		        		{else}
			        		<p>等待发货</p>
			        		<p class="subcon">订单已经付款，正在配货中</p>
		        		{/if}
		        	{/if}
	        		{elseif $order['status']==2}
		        		<i class="iconfont exp">&#xe633;</i>
		        		<p>已经发货</p>
		        		<p class="subcon">{$auto_confirm_order_time}</p>
		        	{elseif $order['status']==3}
		        		<i class="iconfont refund">&#xe621;</i>
		        		<p>退款处理</p>
		        		<p class="subcon">审核通过后，资金将会原路退回</p>
		        	{elseif $order['status']==4}
		        		<i class="iconfont refund">&#xe621;</i>
		        		<p>退款成功</p>
		        		<p class="subcon">资金已原路退回您的帐户中</p>
		        	{elseif $order['status']==5}
		        		<i class="iconfont return">&#xe63c;</i>
		        		{if $order['return_num']==4}
			        		<p>待寄回退货</p>
			        		<p class="subcon">请寄回商品，并填写快递单号</p>
			        		<p class="subcon">{$re_timeout}</p>
		        		{elseif $order['return_num']==5}
		        			<p>拒绝退货</p>
		        			<p class="subcon">订单不符合退货条例</p>
		        			{if $history['condition']=='again'}
		        			<p class="subcon">你可以登录电脑版重新申请</p>
		        			{/if}
		        		{else}
		        			<p>退货订单</p>
			        		<p class="subcon">详情请登录电脑版查看</p>
		        		{/if}
		        	{elseif $order['status']==7}
		        		<i class="iconfont return2">&#xe62a;</i>
		        		{if $order['return_num']==2 && $order['reout'] != 'expback'}
		        			{if $relation_order['status']==1}
			        		<p>待寄回换货</p>
			        		<p class="subcon">请寄回商品，并填写快递单号</p>
			        		<p class="subcon">{$re_timeout}</p>
			        		{else}
			        		<p>待支付运费</p>
			        		<p class="subcon">非质量问题，需要买家承担来回运费</p>
			        		{/if}
		        		{elseif $order['return_num']==3}
		        			<p>拒绝换货</p>
		        			<p class="subcon">订单不符合退货条例</p>
		        			{if $history['condition']=='again'}
		        			<p class="subcon">你可以登录电脑版重新申请</p>
		        			{/if}
		        		{else}
		        			<p>换货订单</p>
			        		<p class="subcon">详情请登录电脑版查看</p>
		        		{/if}
		        	{elseif $order['status']==8}
		        		<i class="iconfont ok">&#xe63e;</i>
		        		<p>交易完成</p>
		        		<p class="subcon">{$history.content}</p>
		        	{elseif $order['status']==-1}
		        		<i class="iconfont cencel">&#xe639;</i>
		        		<p>订单关闭</p>
		        		<p class="subcon">{$history.content}</p>
		        	{/if}
		        	{if $order['relation_order_id']}
	        		<p class="subcon">原订单：<a href="/?m=account&a=order_detail&order_id={$order['relation_order_id']}">{$order['relation_order']}</a></p>
	        		{/if}
	        		{if $relation_order['order_sn']}
	        		<p class="subcon">关联订单：<a href="/?m=account&a=order_detail&order_id={$relation_order['order_id']}">{$relation_order['order_sn']}</a></p>
	        		{/if}
		        </div>

	        	{if $kuaidiinfo}
		        <div class="order-detail-row express">
		        	<div class="ico"><i class="iconfont">&#xe635;</i></div>
		        	<div class="con">
		        		<p class="exping">{$kuaidiinfo.data.0.context}</p>
		        		<p class="time">{$kuaidiinfo.data.0.ftime}</p>
		        	</div>
		        	<div class="arrow btn_checkwuliu"><i class="iconfont">&#xe636;</i></div>
		        </div>
		        {/if}
		        <div class="order-detail-row address">
		        	<div class="ico"><i class="iconfont">&#xe634;</i></div>
		        	<div class="con">
		        		<h5>收货人：{$order.receiver_name}<span class="phone">{$order.receiver_phone}</span></h5>
		        		<p class="localhost">收货地址：{$order.location}</p>
		        		<p class="localhost">配送方式：{if $order.delivery_name}{$order.delivery_name}{/if}</p>
		        	</div>
		        </div>
		        <div class="order_items">
		        	{section name=item loop=$order_items}
		        	<div class="innerbox">
	                    <div class="imgbox"><a href="/?m=category&a=product&id={$order_items[item].product_id}"><img src="{$order_items[item].color_photo}!w200" alt="{$order_items[item].product_name}"></a></div>
	                    <div class="itemsummary">
	                        <p class="title">{$order_items[item].product_name}</p>
	                        <p class="model">SKU：{$order_items[item].sku_sn}</p>
	                        <p class="model">规格：{$order_items[item].color_prop}, {$order_items[item].size_prop}</p>
		                    <p class="model">单价：<sup>￥</sup>{$order_items[item].price} x {$order_items[item].num}</p>
	                        {$order_items[item].presale_date}
	                    </div>
	                </div>
	                {/section}
		        </div>
		        <div class="order_prices">
		        	<dl> <dt>运费</dt><dd>{$order.ship_price}</dd> </dl>
		        	{if $order['discount'] > 0}
		        	<dl> <dt>优惠</dt><dd>-<sup>￥</sup>{$order.discount}</dd> </dl>
		        	{/if}
		        	<dl> <dt>订单总价</dt><dd><sup>￥</sup>{$order.order_total}</dd> </dl>
		        	<dl> <dt>实付款（含运费）</dt><dd style="color:#ff6400"><sup>￥</sup>{$order.pay_total}</dd> </dl>
		        	{if count($order_discounts)>0 && $order_discounts}
		        	<dl> <dt><b>优惠使用情况：</b></dt></dl>
		        	{foreach from=$order_discounts item=discount}
			        	{if $discount.discount > 0}
			        	<dl> <dt>{$discount.name}</dt><dd>减 {$discount.discount}元</dd> </dl>
			        	{/if}
		        	{/foreach}
		        	{/if}
		        </div>
		        <div class="order_services">
		        	<a class="tel" href="http://user.25boy.cn/about/contact" target="_blank"><i class="iconfont">&#xe691;</i> 联系客服</a>
		        </div>
		        <div class="order_sninfo">
		        	<p>订单编号:{$order.order_sn}</p>
		        	{if $order['is_pay']}<p>{$order.pay_sn}</p>{/if}
		        	<p>创建时间:{$order.order_date}</p>
		        	{if $order['pay_date']}<p>付款时间:{$order.pay_date}</p>{/if}
		        	{if $order['ship_time']}<p>发货时间:{$order.ship_time}</p>{/if}
		        </div>
		        <div class="blank20"></div>
		        <div class="blank20"></div>
		    </div>
        {/if}
    </section>
    <div class="order_detail_bottom">
    	{if $order['status']==0}
    		{if $order.relation_order_id==''}
    		<a order_id="{$order.order_id}" href="javascript:;" data-sn="{$order.order_sn}" class="btn_paynow btn btn_mini">立刻支付</a>
        	<a order_id="{$order.order_id}" href="javascript:;" class="btn_cancle btn btn_mini btn_gray">取消订单</a>
        	{else}
        	<a order_id="{$order.order_id}" href="javascript:;" data-sn="{$order.order_sn}" class="btn_paynow btn btn_mini">支付运费</a>
        	<a order_id="{$order.order_id}" href="javascript:;" class="btn_cancle2 btn btn_mini btn_gray">取消退换</a>
        	{/if}
        {elseif $order['status']==1}
        	{if $order.relation_order_id==''}
	        	<a order_id="{$order.order_id}" href="javascript:;" class="btn_tuikuan btn btn_mini">申请退款</a>
	        	{if $order['location'] == '' && $order['is_issuing']==1}
	        	<a order_id="{$order.order_id}" href="javascript:;" class="btn_address btn btn_mini">填写收货地址</a>
	        	{/if}
        	{else}
        		{if $order['relation_reout']!='expback'}
        		<a order_id="{$order.relation_order_id}" href="javascript:;" class="btn_shipreturn btn btn_mini">寄回商品,填写快递单号</a>
        		{/if}
        	{/if}
        {elseif $order['status']==2}
        	<a href="javascript:;" class="btn_checkwuliu btn btn_mini">查看物流</a>
        	<a order_id="{$order.order_id}" href="javascript:;" class="btnconfirm btn btn_mini">确认收货</a>
        	{if $order.relation_order_id=='' && $order['is_seller'] != 2}
        	<a order_id="{$order.order_id}" href="javascript:;" class="btn_tuihuan btn btn_mini btn_gray">申请退换</a>
        	{/if}
        {elseif $order['status']==5}
        	<a href="javascript:;" class="btn_checkwuliu btn btn_mini btn_gray">查看物流</a>
        	{if $order['return_num']==4}
        	<a order_id="{$order.order_id}" href="javascript:;" class="btn_shipreturn btn btn_mini">寄回商品</a>
        	{/if}
        	{if $order['return_num']==5}
        	<a order_id="{$order.order_id}" href="javascript:;" class="btnconfirm btn btn_mini">确定交易</a>
        	{if $history['condition']=='again' && $order.relation_order_id==''}
        	<a order_id="{$order.order_id}" href="javascript:;" class="btn_tuihuan btn btn_mini btn_gray">重新申请</a>
        	{/if}
        	{/if}
        {elseif $order['status']==7}
        	<a href="javascript:;" class="btn_checkwuliu btn btn_mini btn_gray">查看物流</a>
        	{if $order['return_num']==2}
	        	{if $relation_order['pay_status']==0}
	        	<a order_id="{$relation_order.order_id}" href="javascript:;" data-sn="{$relation_order.order_sn}" class="btn_paynow btn btn_mini">支付运费</a>
	        	<a order_id="{$relation_order.order_id}" href="javascript:;" class="btn_cancle2 btn btn_mini">取消退换</a>
	        	{elseif $order['reout'] != 'expback'}
        		<a order_id="{$order.order_id}" href="javascript:;" class="btn_shipreturn btn btn_mini">寄回商品</a>
	        	{/if}
        	{/if}
        	{if $order['return_num']==3 && $order['reout']!='expback'}
        	<a order_id="{$order.order_id}" href="javascript:;" class="btnconfirm btn btn_mini">确定交易</a>
        	{if $history['condition']=='again' && $order.relation_order_id==''}
        	<a order_id="{$order.order_id}" href="javascript:;" class="btn_tuihuan btn btn_mini btn_gray">重新申请</a>
        	{/if}
        	{/if}
        {elseif ($order['status']==3 || $order['status']==4 || $order['status']==8) && $order['ship_id']}
        	<a href="javascript:;" class="btn_checkwuliu btn btn_mini">查看物流</a>
    	{/if}
    </div>

</div>
<link rel="stylesheet" type="text/css" href="/statics/css/account.css?v={$version}">
<script type="text/javascript">
var order_id = "{$order.order_id}";
var wuliuinfo='';
var ship_com='{$order.ship_com}';
var ship_sn='{$order.ship_sn}';
var current_model = 'order';
{if $kuaidi_json}
wuliuinfo = {$kuaidi_json};
{/if}
var account_balance = '{$account_balance}';
</script>
{include file="public/js.tpl" title=js}
<script src="/statics/js/account_order_detail.js?v={$version}"></script>

{include file="public/footer.tpl" title=footer}