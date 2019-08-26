{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}
<div id="bodybox">
    <section class="main" style="padding-bottom:10px;">
        <div id="topBanner">
            {foreach from=$adset item=item}
            <p class="img_box"><a href="/?m=call&go={$item.ad_id}"><img src="{$item.srcurl}!w640" alt="{$item.adname}"></a></p>
            {/foreach}   
        </div>

        <ul class="product_list_gird_2" style="margin-top:10px;">
            {foreach from=$prolist item=item}
            <li>
                <div class="item">
                    <a href="/?m=category&a=product&id={$item.product_id}" title="{$item.brand_name} {$item.product_name}"><img src="{$item.product_img}" alt="{$item.brand_name} {$item.product_name}" /></a>
                    <span class="brand">{$item.brand_name}</span>
                    <span class="productname">{$item.product_name}</span>
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
    </section>
    
    <a class="btn btn_gray" href="/" style="display: block;margin:30px 100px 10px 100px;line-height: 2.6em;">进入网站首页</a>
</div>
{include file="public/js.tpl" title=js}
<script src="/statics/js/root.js?v={$version}"></script>

{include file="public/footer.tpl" title=footer}