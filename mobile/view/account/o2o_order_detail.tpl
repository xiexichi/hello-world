{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<link rel="stylesheet" type="text/css" href="/statics/css/o2o/common.css">

<style type="text/css">
	.refund-substatus{
		position: absolute;
		right: 1rem;
		top: 1rem;
		color: #848484;
	}
</style>

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
		        	{elseif $order['status']==4}
		        		<i class="iconfont refund">&#xe621;</i>
		        		<p>退款处理</p>
		        		<p class="subcon">审核通过后，资金将会原路退回</p>
		        	{elseif $order['status']==5}
		        		<i class="iconfont refund">&#xe621;</i>
		        		<p>退款成功</p>
		        		<p class="subcon">资金已原路退回您的帐户中</p>

		        	{elseif $order['status']==3}
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

					
					<!-- 如果是线上代发 -->
					{if $order.order_type == 'issuing'}
						
						{if $default_address|count>0}
			            <div class="default_address" 
			            {if $order['status'] == 0}id="address_review"{/if}
			            address="{$default_address.address}"
						state_name="{$default_address.state_name}"
						city_name="{$default_address.city_name}"
						district_name="{$default_address.district_name}"
						receiver_name="{$default_address.receiver_name}"
						receiver_phone="{$default_address.receiver_phone}"
			            >
			                <div class="title">收货地址 
			                	{if $order['status'] == 0}<span>></span>{/if}
			                </div>
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
					{else}
						<div class="con">门店自提</div>
					{/if}

		        </div>
		        <div class="order_items">
		        	{section name=item loop=$order_items}
		        	<div class="innerbox" style="position: relative;">
	                    <div class="imgbox"><a href="/?m=category&a=product&id={$order_items[item].product_id}"><img src="{$order_items[item].color_photo}!w200" alt="{$order_items[item].product_name}"></a></div>
	                    <div class="itemsummary">
	                        <p class="title">{$order_items[item].product_name}</p>
	                        <p class="model">SKU：{$order_items[item].sku_sn}</p>
	                        <p class="model">规格：{$order_items[item].color_prop}, {$order_items[item].size_prop}</p>
		                    <p class="model">单价：<sup>￥</sup>{$order_items[item].price} x {$order_items[item].quantity}</p>
	                        {$order_items[item].presale_date}
	                    </div>

	                    <!-- 
	                    申请退换=1 
						同意申请，待寄回=2 
						已寄出，待确认=3 
						确认退换=4 
						 -->
	                    {if $order_items[item].reorder }
							<div class="refund-substatus">{$reorder_substatus[$order_items[item].reorder['substatus']]}</div>
	                    {/if}

	                </div>
	                {/section}
		        </div>
		        <div class="order_prices">
		        	<dl> <dt>运费</dt><dd>{$order.ship_price}</dd> </dl>
		        	{if $order['discount'] > 0}
		        	<dl> <dt>优惠</dt><dd>-<sup>￥</sup>{$order.discount}</dd> </dl>
		        	{/if}
		        	<dl> <dt>订单总价</dt><dd><sup>￥</sup>{$order.order_total}</dd> </dl>
		        	<dl> <dt>实付款（含运费）</dt><dd style="color:#ff6400"><sup>￥</sup>
		        		<span id="pay_total">{$order.pay_total}</span></dd> </dl>
		        </div>
		        <div class="order_services">
		        	<a class="tel" href="http://user.25boy.cn/about/contact" target="_blank"><i class="iconfont">&#xe691;</i> 联系客服</a>
		        </div>
		        <div class="order_sninfo">
		        	<p>订单编号:{$order.order_sn}</p>
		        	{if $order['is_pay']}<p>{$order.pay_sn}</p>{/if}
		        	<p>创建时间:{$order.create_date}</p>
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
    	{/if}

    	{if $refundable}
			<!-- 判断是否允许退换 -->
    		{if $order['is_returned'] == 1}
				<!-- 是否可申请退换 -->
				{if $hasReorders == false }
		        	<a order_id="{$order.order_id}" href="javascript:;" class="btn_tuihuan btn btn_mini btn_gray">申请退换</a>
		        {/if}
	        {/if}
			
			<!-- 是否显示寄回商品 -->
			{if $sentBackReorders}
			<!-- 将第一个退换单，设置操作单 -->
        	<a order_id="{$sentBackReorders[0].reorder_id}" href="javascript:;" class="btn_shipreturn btn btn_mini">寄回商品</a>
			{/if}
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

// o2o订单xiangq
var o2oOrderDetial = true;

</script>
{include file="public/js.tpl" title=js}
<!-- o2o公共js -->
<script src="/statics/js/o2o/common.js?v={$version}"></script>
<script src="/statics/js/account_o2o_order_detail.js?v={$version}"></script>

{include file="public/footer.tpl" title=footer}