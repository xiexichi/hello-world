{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<div id="bodybox">
    <section class="main" id="page_hd_cz">
	    {if $brand.img_url}
	    <p><a href="/?m=category&brand_id={$brand.ad_id}" title="{$brand.brand_name}" class="img_box"><img src="{$brand.img_url}" alt="{$brand.brand_name}"></a></p>
	    {/if}

	    <h3 class="hd" style="color:#fff;font-size:2em;background-color: #000;text-align: center;line-height: 2em;margin-bottom: 5px;margin-top:30px;">热卖商品</h3>
        <ul class="product_list_gird_2" id="productlist"> </ul>
    </section>
</div>

<div class="loaddiv">
    <div class="loading-msg">
        <span>数据加载中请稍后</span>
        <div class="loading-box">
            <div class="loading" index="0"></div>
            <div class="loading" index="1"></div>
            <div class="loading" index="2"></div>
            <div class="loading" index="3"></div>
            <div class="loading" index="4"></div>
        </div>
    </div>
</div>

<script>
    var cid = 0;
    $(function() {
        condition.default = 0;
        condition.brand = 1;
        condition.brand_id = {$brand.brand_id};
    });
</script>
<script defer src="/statics/js/product.list.js?v={$version}"></script>

{include file="public/js.tpl" title=js}
{include file="public/footer.tpl" title=footer}