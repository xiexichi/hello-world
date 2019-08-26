{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<link rel="stylesheet" href="/statics/css/common.css">
<link rel="stylesheet" href="/statics/css/combomeal.css">


<div id="bodybox">

    <section class="main pagemain pagegrey" style="padding-bottom:60px;">

		<div id="productbanner">
		    <ul class="bxslider">
		    	{section name=i loop=$combomeal.imgs}
		        <li>
		        	<a href="###" class="swipebox" title="25BOY">
		        	<img src="{$combomeal.imgs[i].url}" alt="25BOY"></a>
		        </li>
		        {/section}

		    </ul>
		</div>
		<!-- 套餐描述 -->
		<div class="combo-info">
			<div class="combo-title text-ellipsis">{$combomeal['combomeal_title']}</div>
			<div class="combo-desc">{$combomeal['combomeal_desc']}</div>
			<div class="fled-space-between combo-info-bottom">
				<span class="combo-price">最多可省 ￥<span class="save-money">{$combomeal['combomeal_discount_price']}</span></span>
				<span class="combo-event-time">活动至{$combomeal['end_date']}</span>
			</div>
		</div>
		<div class="divider"></div>
		
		<!-- 搭配商品 -->
		<div>
			<div class="combo-products-title">搭配商品</div>
			<div class="combo-products cartlistbox">
				<ul>
					{foreach from=$combomeal['products'] key="k" item="i"}

					<li class="product-item fled-flex-start">
						<!-- 选择图标 -->
						<div style="width: 11%;">
							<div class="checkbox checkbox_checked"></div>
						</div>
						
						<div style="width: 89%;">
							<!-- 商品右则信息 -->
							<div class="item-right fled-flex-start">
								<a href="/?m=category&a=product&id={$i.product_id}" class="combo-product-a" style="width:25%">
									<img productId="{$i.product_id}" class="combo-product-img" src="{$i.product_img}" alt="">
								</a>

								<div class="product-info" style="width:69%;margin:0 3%;">
									<div class="fled-between-flex-start">
										<!-- 名称和金额 -->
										<div style="width:75%;">
											<!-- 名称 -->
											<p class="show2row product-name" productId="{$i.product_id}">
												{$i.product_name}
											</p>
											<!-- 金额 -->
											<p>
												<span class="combo-price" style="font-size: 0.9rem;">￥{$i.combomeal_price}</span>
												&nbsp;&nbsp;<span class="color-gary">已省</span>
												<!-- <span class="combo-price">￥{($i.price - $i.combomeal_price) * $i.combomeal_num}</span> -->
												<span class="combo-price">￥{($i.price - $i.combomeal_price)}</span>
											</p>
										</div>
										<!-- 数量 -->
										<div>×{$i.combomeal_num}</div>
									</div>
									{if $i['props'] gt 0}
									<input type="hidden" class="check-choose" productId="{$i['product_id']}">
									<div class="select-cs fled-space-between color-gary" productId="{$i.product_id}">
										<div>
											<span class="choose-label" productId="{$i.product_id}">请选择: </span>
											<span class="size" productId="{$i.product_id}">尺码</span>&nbsp;
											<span class="color" productId="{$i.product_id}">颜色</span>
										</div>
										<i class="iconfont">&#xe636;</i>
									</div>
									{/if}
								</div>
							</div>
							<!-- 下划线 -->
							<div class="divider"></div>
						</div>
					</li>
					{/foreach}
				</ul>
			</div>
		</div>

	</section>

</div>


<!-- 底部购买div -->
<div id="botton-buy-div" class="fled-flex-end">
	<div class="ph-1">
		<p class="text-right">搭配价 <span class="combo-price save-money">￥{$combomeal['combomeal_price']}</span></p>
		<p class="text-right">
			<span class="">已省 <span class="combo-price">￥{$combomeal['combomeal_discount_price']}</span></span>
		</p>
	</div>
	<div id="botton-buy-btn">立即购买</div>
</div>

<!-- form表单 -->
<form id="combomeal-form" method="post" action="/?m=category&a=combomealOrder">
	<input type="hidden" name="combomeal_id" value="{$combomeal['combomeal_id']}">
	<input type="hidden" name="products">
</form>


<!-- 选择尺码颜色 -->
<div id="choose-attr">

	{foreach from=$combomeal['products'] key="k" item="i"}
	<!-- 尺码、颜色、数量 -->
	<div class="product-attr" productId="{$i.product_id}">
		<div class="fled-flex-start">
			<div class="img-div">
				<img productId="{$i.product_id}" class="product-attr-img" src="{$i.product_img}" alt="">
			</div>
			<div class="p-0-5 fs-0-9">
				<p class="pv-0-2">￥{$i.combomeal_price}</p>
				<p class="pv-0-2">库存:
					<span class="size_quantity" productId="{$i.product_id}">{$i.total_quantity}</span>
				</p>
				<p class="pv-0-2">
					<span class="choose-label" productId="{$i.product_id}">请选择: </span>
					<span class="size" productId="{$i.product_id}">尺码</span>
					&nbsp;
					<span class="color" productId="{$i.product_id}">颜色</span>
				</p>
				<!-- 关闭选择 -->
				<i class="close-choose-btn iconfont">&#xe639;</i>
			</div>
		</div>
		<div class="divider"></div>

		<!-- 颜色 -->
		<div class="pt-0-4 fs-0-9">
			<p>颜色</p>
			<p class="mt-0-5">
				{foreach from=$i['props'] key="kk" item="ii"}
				<span class="coloritem" propId="{$ii.prop_id}" productId="{$i.product_id}" src="{$ii.img}">{$ii.color_prop}</span>
				{/foreach}
			</p>
		</div>
		<div class="divider"></div>

		<!-- 尺码 -->
		<div class="pt-0-4 fs-0-9">
			<p>尺码</p>
			{foreach from=$i['props'] key="kk" item="ii"}
			<p class="prop-size mt-0-5" propId="{$ii.prop_id}" productId="{$i.product_id}" {if $kk gt 0}style="display: none;"{/if}>
				{foreach from=$ii['stocks'] key="k1" item="i1"}
					<span class="sizeitem" stockId="{$i1.stock_id}" productId="{$i.product_id}" quantity="{$i1.quantity}">{$i1.size_prop}</span>
				{/foreach}
			</p>
			{/foreach}
		</div>
		<div class="divider"></div>
		
		<!-- 数量 -->
		<div class="pt-1 fs-0-9 fled-space-between">
			<span>购买数量</span>
			<div class="select-quantity-div">
				<span>－</span>
				<span>{$i.combomeal_num}</span>
				<span>＋</span>
			</div>
		</div>
		<div class="h-4"></div>
	</div>
	{/foreach}
	
	<!-- 确认按钮 -->
	<div id="sure-choose-btn" class="pv-0-6">确认</div>
	
	<!-- 提示信息 -->
	<div id="tips"></div>
</div>

<!--  -->
<div id="tip-bg"></div>

<!-- js -->
{include file="public/js.tpl" title=js}
<link rel="stylesheet" href="statics/css/jquery.bxslider.css" type="text/css" />
<script src="statics/js/jquery.bxslider.min.js"></script>
<link rel="stylesheet" href="statics/css/swipebox.css">
<script src="statics/js/jquery.swipebox.min.js"></script>
<script src="statics/js/combomeal.js?v={$version}"></script>
<script type="text/javascript">
$(function(){
    var winW = $(window).width();
    if(winW>640) winW=640;
    $('#productbanner .bx-viewport').css('height',winW);
    $('#productbanner .bx-viewport li, #productbanner .bx-viewport img').css({
        'height':winW,'width':winW
    });
})
</script>

<!-- 微信分享 -->
<script>
    {if $is_weixin}
        var title = "{$combomeal.combomeal_title}";
        var link  = "{$promote_link}";
        var url   = "{$promote_imgurl}"; // 分享图片地址
        var desc  = "{$combomeal.combomeal_desc}";
        wx.ready(function () {
            /*获取“分享到朋友圈”按钮点击状态及自定义分享内容接口*/
            wx.onMenuShareTimeline({
                title: title,
                imgUrl  : url,
                link : link,
                success: function () {
                    layer.open({
                        content: '分享成功，感谢你的支持！',
                        time: 1
                    });
                    $('#mcover').remove();
                },
                cancel: function () {
                    layer.open({
                        content: '你已经取消分享！',
                        time: 1
                    });
                    $('#mcover').remove();
                }
            });

            /*获取“分享到好友”按钮点击状态及自定义分享内容接口*/
            wx.onMenuShareAppMessage({
                title   : title,
                desc    : desc,
                imgUrl  : url,
                link    : link,
                success: function () {
                    $('#mcover').remove();
                },
                cancel: function () {
                    layer.open({
                        content: '你已经取消分享！',
                        time: 1
                    });
                    $('#mcover').remove();
                }
            });
            /*获取“分享到QQ”按钮点击状态及自定义分享内容接口*/
            wx.onMenuShareQQ({
                title   : title,
                desc    : desc,
                imgUrl  : url,
                link    : link,
                success: function () {
                    layer.open({
                        content: '分享成功，感谢你的支持！',
                        time: 1
                    });
                    $('#mcover').remove();
                },
                cancel: function () {
                    layer.open({
                        content: '你已经取消分享！',
                        time: 1
                    });
                    $('#mcover').remove();
                }
            });
            /*获取“分享到腾讯微博”按钮点击状态及自定义分享内容接口*/
            wx.onMenuShareWeibo({
                title   : title,
                desc    : desc,
                imgUrl  : url,
                link    : link,
                success: function () {
                    layer.open({
                        content: '分享成功，感谢你的支持！',
                        time: 1
                    });
                    $('#mcover').remove();
                },
                cancel: function () {
                    layer.open({
                        content: '你已经取消分享！',
                        time: 1
                    });
                    $('#mcover').remove();
                }
            });
            /*获取“分享到QQ空间”按钮点击状态及自定义分享内容接口*/
            wx.onMenuShareQZone({
                title   : title,
                desc    : desc,
                imgUrl  : url,
                link    : link,
                success: function () {
                    layer.open({
                        content: '分享成功，感谢你的支持！',
                        time: 1
                    });
                    $('#mcover').remove();
                },
                cancel: function () {
                    layer.open({
                        content: '你已经取消分享！',
                        time: 1
                    });
                    $('#mcover').remove();
                }
            });

        });
    {/if}
</script>

<!-- 底部 -->
{include file="public/footer.tpl" title=footer}