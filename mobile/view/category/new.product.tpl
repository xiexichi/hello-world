{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<div id="bodybox">
    <section class="main pagemain pagegrey">
        <span class="blank5"></span>
        <div class="gird_box">
            <div class="gird_items" id="productlist">
                {if $productlist|count>0}
                    {section name=item loop=$productlist}
                    <div class="gird_item productitem" data-size="6">
                        <div class="innerbox">
                            <a href="?m=category&a=product&id={$productlist[item].product_id}" class="btnitem" title="{$productlist[item].product_name}">
                                <img src="{$productlist[item].thumb}" alt="{$productlist[item].brand_name} {$productlist[item].product_name}" />
                            </a>
                            <div class="itemsummary">
                                <p class="brand">{$productlist[item].brand_name}</p>
                                <p class="title">{$productlist[item].product_name}</p>
                                {if $productlist[item].miao_price!=''}
                                    <p class="price"><sup>￥</sup><span class="new">{$productlist[item].miao_price}</span> <span class="old"><sup>￥</sup>{$productlist[item].market_price}</span> </p>
                                {else}
                                    {if $productlist[item].market_price==$productlist[item].price}
                                        <p class="price"><sup>￥</sup>{$productlist[item].price}</p>
                                    {else}
                                        <p class="price"><sup>￥</sup><span class="new">{$productlist[item].price}</span> <span class="old"><sup>￥</sup>{$productlist[item].market_price}</span> </p>
                                    {/if}
                                {/if}
                            </div>
                        </div>
                    </div>
                    {/section}
                {/if}
            </div>
        </div>
    </section>
    <div class="blank30"></div>
    <div class="blank30"></div>
    <div class="blank30"></div>
    <div class="blank30"></div>
    <div class="blank10"></div>
</div>

{include file="public/js.tpl" title=js}
<script src="/statics/js/root.js?v={$version}"></script>

{include file="public/footer.tpl" title=footer}
