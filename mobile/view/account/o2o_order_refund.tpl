{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<link rel="stylesheet" type="text/css" href="/statics/css/common.css">
<link rel="stylesheet" type="text/css" href="/statics/css/o2o/common.css">

<style type="text/css">
	.checkbox{
		height: 20px;
	    width: 20px;
	    background: url(/statics/img/icon.checkbox.png) no-repeat #fff;
	    background-size: cover;
	    background-position: 0 0;
		display: block;
	    margin-top: -10px;
	    border-radius: 20px;
	    top: 0.3rem;
    	right: 0.5rem;
    	border: 1px solid #bbb5b5;
	}
	.checkbox_checked {
		border: none;
	    background-position: 0 100%;
	    background-position-x: 0px;
	    background-position-y: 100%;
	}

	.title{
		font-size: 1rem;
		color: #9e9898;
	}

	.refund-reason-div{
		font-size: 0.9rem;
	}
	.refund-reason{
		background: #dcdcdc;
	}
	.refund-reason div{
		position: relative;
	}
	
	.product-checkbox{
		height: 15px;
		width: 15px;
		position: absolute;
		top: 20%;
		right: 1.5rem;
		border: 1px solid #bbb5b5; 
		border-radius: 1px;
	}
	
	.product-checkbox-checked{
		background: url(/statics/img/hook.png) no-repeat #fff;
	}

	.note{
		width: 100%;
		border: none;
		resize: none;
	}
	
	.imgs-div{
		width: 100%;
		min-height: 10rem;
		background: #DCDCDC;
	}

	#add-img{
		width: 30px;
		margin-right: 10px;
	}
	
	#clone-div{
		display: none;
	}
	.upload-files{
		display: none;
	}


	/*问题图片*/
	#problem-div-reason{
		display: none;
	}

	.problem-div{
		width: 30%;
		padding-top: 0.5rem;
		padding-left: 0.5rem;
		position: relative;
	}

	.problem-pic{
		width: 6.5rem;
		/*height: 6.5rem;*/
		overflow: hidden;
	}

	.del-problem-pic{
		position: absolute;
		top: 0.1rem;
		right: 0rem;
		display: block;
		width: 1rem;
		height: 1rem;
		border-radius: 1rem;
		color: red;
		border:1px solid red;
		font-size: 1rem;
		text-align: center;
		cursor: pointer;
	}
</style>


<div id="bodybox">
    <section class="main pagemain">
        {if $session_uid==""||$session_uid==0}
            {include file="public/remind_login.tpl" title=header}
        {else}
	        <div class="order_detail_box">
		        
		        <div class="refund-reason-div m-t-1">
		        	<div class="fled-space-between p-0-8">
		        		<span class="title">退货原因</span>
		        	</div>
		        	<div class="divider"></div>
		        	<!-- 选择退货原因 -->
		        	<div class="refund-reason">
			        	<div reason="2" class="reason fled-space-between p-0-8">
			        		<div>无理由退货</div>
			        		<div reason="2" class="checkbox checkbox_checked"></div>
			        	</div>
			        	<div class="divider"></div>
			        	<div reason="1" class="reason fled-space-between p-0-8">
			        		<div>质量问题退货</div>
			        		<div reason="1" class="checkbox"></div>
			        	</div>		        		
		        	</div>
		        </div>
				
				<div class="mt-1 divider"></div>
				<div class="p-0-5 fs-0-9">
					<span class="title">选择商品</span>
				</div>
				<div class="divider"></div>
		        <div class="order_items" style="border-top: none;">
		        	{section name=item loop=$order_items}

		        	<div class="innerbox" style="position: relative;">
	
						{if $order_items[item].can_requantity > 0 }

	                    <div class="imgbox"><a href="/?m=category&a=product&id={$order_items[item].product_id}"><img src="{$order_items[item].color_photo}!w200" alt="{$order_items[item].product_name}"></a></div>
	                    <div class="itemsummary">
	                        <p class="title">{$order_items[item].product_name}</p>
	                        <p class="model">
	                        	<!-- SKU：{$order_items[item].sku_sn} -->
								规格：{$order_items[item].color_prop}, {$order_items[item].size_prop}
	                        </p>
		                    <p class="model">单价：<sup>￥</sup>{$order_items[item].price} x {$order_items[item].quantity}</p>
	                        
	                        <p class="model m-t-0-5">
                        		<div class="fled-flex-end">
                        			<div class="right">
                        				<span class="span-operation reduce">－</span>
		                        		<input itemid="{$order_items[item].item_id}" class="input-min" type="number" min="1" max="{$order_items[item].can_requantity}" value="1">
		                        		<span class="span-operation add">＋</span>
                        			</div>
	                        	</div>
	                        </p>
							
							<!-- 选择图标 -->
	                        <div id="product-checkbox-{$order_items[item].item_id}" class="product-checkbox">
	                        </div>
	                    </div>
						{/if}

	                </div>
	                {/section}
		        </div>
				

				<!-- 留言 -->
				<div class="mt-1 divider"></div>
					<div class="p-0-5 fs-0-9">
						<span class="title">留言/说明</span>
					</div>
					<div class="divider"></div>
					<div>
						<div class="p-0-5">
							<textarea id="note" class="note" placeholder="请输入退货原因或留言" rows="3"></textarea>
						</div>
					</div>
				<div class="divider"></div>

				<!-- 退货原因是质量问题才显示 -->
				<div id="problem-div-reason">
					<div class="mt-1 divider"></div>
					<div class="p-0-5 fs-0-9 fled-space-between">
						<span class="title">商品问题图片（最多3张）</span>
						<img id="add-img" src="/statics/img/gray-add-32.png">
					</div>
					<div class="divider"></div>
					<div class="imgs-div fled-flex-start w-100p" style="align-items: flex-start">
					</div>
				</div>

				<!-- 退货原因是质量问题才显示 -->
				
				<!-- 底部占位div -->
				<div style="height: 3rem;"></div>
		    </div>
        {/if}
    </section>


    <div class="order_detail_bottom">

		<a order_id="{$order_items[0].order_id}" href="javascript:;" data-sn="{$order.order_sn}" id="sure-apply" class="btn btn_mini">提交申请</a>
    	<a order_id="{$order_items[0].order_id}" href="javascript:;" id="cancle-apply" class="btn btn_mini btn_gray">取消申请</a>

    </div>

</div>


<!-- 克隆div -->
<div id="clone-div">
	<input type="file" name="images[]" class="images">
	
	<!-- 问题图片 -->
	<div class="problem-div">
		<span class="del-problem-pic">×</span>
		<img class="problem-pic" src="">
	</div>
</div>

<!-- 存放上传文件的div -->
<form class="upload-files" number="0">
</form>


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
<script src="/statics/js/o2o/axios.min.js?v={$version}"></script>
<script src="/statics/js/account_o2o_order_refund.js?v={$version}"></script>

{include file="public/footer.tpl" title=footer}