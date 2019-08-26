{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}
{literal}
<style type="text/css">
.product_list_gird_2 li .productname{
    text-align:left;text-overflow:ellipsis;white-space:nowrap;height:auto;
}
.product_list_gird_2 li .price{
    font-size:1.2em;color:#b01f23;
}
</style>
<script type="text/javascript">
$(document).on('click','#getCoupon',function(){
    $('#getCoupon').attr('disabled',true).addClass('disabled');
    var options = [{ "url": "/?m=hd&a=redpack", "data":{do:'get'}, "type":"POST", "dataType":"json"}];
    Load(options, function(json){
        $('#getCoupon').attr('disabled',false).removeClass('disabled');
        if(json.status=="success"){
            layer.open({
                content: '<b>领取成功，50元已入帐！</b><br><br>使用余额和合并付款，若需退换货，<br>退款将退回25BOY余额，不能提现与转赠，请知晓。'
                ,btn: ['查看余额']
                ,shadeClose:false
                ,end: function(){
                    window.location.href='/?m=account&a=balance&c=cz&showQRcode=1';
                }
            });
        }else{
            var errorsummary = '';
            switch(json.status){
                case "geted":
                    errorsummary = "每个帐号限领1次，您已经领过了！";
                    break;
                case "nologin":
                    errorsummary = "您还没登录网站，请登录后领取。";
                    layer.open({
                        content: errorsummary
                        ,btn: ['马上登录']
                        ,shadeClose:false
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
                    errorsummary = "领取失败，请刷新页面重试。";
                    break;
                default:
                    errorsummary = json.msg;
                    break;
            }
            layer.open({
                content: errorsummary
                ,btn: ['关闭']
                ,shadeClose:true
                ,end: function(){
                    window.location.href='/?showQRcode=1';
                }
            });
        }
    });
});
</script>
{/literal}
<div id="bodybox">
    <section class="main" style="padding-bottom:10px;">
        <div id="topBanner">
            {foreach from=$adset item=item}
            {if strpos($item.url,'redpack&do=get')}
            <p><a href="javascript:;" id="getCoupon"><img src="{$item.srcurl}!w640" alt="{$item.adname}" style="display:block"></a></p>
            {else}
            <p><a href="{$item.url}"><img src="{$item.srcurl}!w640" alt="{$item.adname}" style="display:block"></a></p>
            {/if}
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
    <span class="blank40"></span>
</div>
{include file="public/js.tpl" title=js}
<script src="/statics/js/root.js?v={$version}"></script>

{include file="public/footer.tpl" title=footer}