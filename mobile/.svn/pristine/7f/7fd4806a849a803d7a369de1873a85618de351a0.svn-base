{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<div id="bodybox">
    <section class="main" id="page_hd_cz">
	    {foreach from=$adset item=item}
	    <p><a href="/?m=call&go={$item.ad_id}" title="{$item.adname}"><img src="{$item.srcurl}!w640" alt="{$item.adname}"></a></p>
	    {/foreach}

	    {foreach from=$prolist item=set}
	    {if $set.list|count>0}
	    <h3 class="hd" style="color:#fff;font-size:2em;background-color: #000;text-align: center;line-height: 2em;margin-bottom: 5px;margin-top:30px;">{$set.posname}</h3>
        <ul class="product_list_gird_2">
            {foreach from=$set.list item=item}
            <li>
                <div class="item">
                    <a href="/?m=category&a=product&id={$item.product_id}" title="{$item.brand_name} {$item.product_name}"><img src="{$item.product_img}" alt="{$item.brand_name} {$item.product_name}" /></a>
                    <span class="brand">{$item.brand_name}</span>
                    <span class="productname"><a href="/?m=category&a=product&id={$item.product_id}">{$item.product_name}</a></span>
                    <span class="price">
                    {if $item.miao_price}
                        <sup>￥</sup>{$item.miao_price} <span class='f-c-999 f-w-n'><sup>￥</sup>{$item.market_price}</span>
                    {elseif $item.market_price>$item.price}
                        <sup>￥</sup>{$item.price} <span class='f-c-999 f-w-n'><sup>￥</sup>{$item.market_price}</span>
                    {else}
                        <sup>￥</sup>{$item.price}
                    {/if}</span>
                </div>
            </li>
            {/foreach}
        </ul>
        {/if}
        {/foreach}
    </section>
</div>
{include file="public/js.tpl" title=js}
<script src="/statics/js/root.js?v={$version}"></script>

{include file="public/footer.tpl" title=footer}