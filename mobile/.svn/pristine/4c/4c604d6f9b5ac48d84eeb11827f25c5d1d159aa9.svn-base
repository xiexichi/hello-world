{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}
<style type="text/css">
.product_list_gird_3 li .item{
    border:none;margin:0;line-height:1.5em;font-size:1em;
}
.product_list_gird_3 li .productname{
    text-align:left;text-overflow:ellipsis;white-space:nowrap;height:auto;
}
.product_list_gird_3 li .price{
    font-size:1.2em;color:#b01f23;
}
#topBanner{
    position:relative;
}
{if $channel == ''}
#topBanner .btn{
    position:absolute;left:30px;bottom:90px;font-family:"微软雅黑";height:40px;line-height:40px;border-radius:8px;font-size:1.2em;width:140px;
}
#topBanner #prepaid{
    margin-bottom:-60px;
}
{else}
#topBanner .btn{
    position:absolute;right:1em;top:2.8em;width:9.5em;height:4em;line-height:1000px;overflow:hidden;background:transparent;
}
#topBanner #prepaid{
    margin-top:4.6em;height:4.3em;width:13em;
}
{/if}
</style>
{literal}
<script type="text/javascript">
$(document).on('click','#getCoupon',function(){
    var options = [{ "url": "/ajax/get.coupon.php", "data":{id:39,type:'receive'}, "type":"POST", "dataType":"json"}];
    Load(options, function(json){
        if(json.status=="success"){
            layer.open({
                content: '领取成功，看中哪款赶紧下手吧！'
                ,btn: ['好的']
            });
        }else{
            var errorsummary = '';
            switch(json.status){
                case "nopw":
                    errorsummary = "您来迟了，已经领光了哦~";
                    break;
                case "geted":
                    errorsummary = "已经领过了！";
                    break;
                case "nologin":
                    errorsummary = "还没有登录，请登录后领取。";
                    layer.open({
                        content: errorsummary
                        ,btn: ['马上登录']
                        ,yes: function(){
                            if(iswx){
                                var b = new Base64();
                                var gourl = b.encode(window.location.href);
                                window.location.href='/?m=login&a=weixin.bind&gourl='+gourl;
                            }else{
                                layer.closeAll();
                                user.action('by',false);
                                user.createpannel();
                            }
                            return false;
                        }
                    });
                    return false;
                    break;
                case "empty":
                    errorsummary = "找不到优惠券，或者过期了。";
                    break;
            }
            layer.open({
                content: errorsummary
                ,btn: ['我知道了']
            });
        }
    });
});
$(document).on('click','#prepaid',function(){
    window.location.href='/?m=hd&a=cz';
});
</script>
{/literal}
<div id="bodybox">
    <section class="main" style="padding-bottom:10px;">
        <div id="topBanner">
            <button class="btn" id="getCoupon">领取50元优惠券</button>
            <button class="btn" id="prepaid">充300送200</button>
            {foreach from=$adset item=item}
            <p><a href="/?m=call&go={$item.ad_id}"><img src="{$item.srcurl}!w640" alt="{$item.adname}"></a></p>
            {/foreach}   
        </div>

        <ul class="product_list_gird_3" style="margin-top:10px;">
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