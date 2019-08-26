<nav id="producttool" class="nav_in">
    <form id="qucikorderform" style="display: none" method="post" action="?m=order">
        <input type="hidden" id="cart_id" name="cart_id" value=""/>
    </form>
    <form id="orderform" style="display: none" method="post" action="?m=order">
        <input type="hidden" id="product_id" name="product_id" value="{$productdetail.product_id}"/>
        <input type="hidden" id="color" name="color" value=""/>
        <input type="hidden" id="size" name="size" value=""/>
        <input type="hidden" id="quantity" name="quantity" value="1"/>
    </form>

    <div class="btn_group wx" style="display:none;">
        <div class="fl">
            <!-- {if $is_weixin}<a href="javascript:;" class="item_product btn_share wx_share_btn">分享</a>{/if} -->
            <!-- <a href="javascript:;" class="item_product btn_share {if $is_weixin}wx_share_btn{else}btn_share_mobile{/if}">分享</a> -->

            <a href="javascript:;" class="item_product btn_weixin show_QRcode">公众号</a>

            {if $productdetail.favorites==1}
                <a href="javascript:;" class="item_product btn_follow btn_followed" rel="{$productdetail.product_id}" >取消收藏</a>
            {else}
                <a href="javascript:;" class="item_product btn_follow" rel="{$productdetail.product_id}" >收藏</a>
            {/if}
            <a href="?m=cart" id="pagecart" class="item_product btn_cart">购物车</a>
        </div>
        
        <div class="fr">
            {if $undercarriage==true}
                <a href="javascript:;" class="btn_text btn_addcart_dis">加入购物车</a>
                <a href="javascript:;" class="btn_text btn_buynow_dis">立即购买</a>
            {else}
                <a href="javascript:;" class="btn_text btn_addcart">加入购物车</a>
                <a href="javascript:;" class="btn_text btn_buynow">立即购买</a>
            {/if}
        </div>
    </div>

</nav>

<style>
.share_box {
    position: fixed;
    bottom:0px;
    width:100%;
    height:9em;
    background: #fff;
    z-index: 999;
    padding-right: 1em;
    display: none;
}
.share_box ul {
    margin-top: 2em;
    padding:0 20px;
}
.share_box li {
    width:25%;
    height: 5.5em;
/*    position: relative;*/
    float:left; 
    text-align: center;
}
.share_box span {
    color:#666;
    font-size: 1.2em;
    display: block;
}
.share_box a {
    background:none;
    text-indent: 0;
   /*  display: inline-block; */
    width:100%;
    height: 4.5em;
    line-height:3em;
    margin:0;
    padding:0;
}
.share_box img {
    width: 30px;
    position: absolute;
    top:-15px;
    right:2em;
}
.clicklayer {
    background:#000;width:100%;height:100%;position:fixed;opacity:.4;z-index:200;left:0;top:0;
        display: none;
}
.share_weibo:before { content: "\e625"; font-size:5em;color:#E6162D}
.share_qq:before { content: "\e624"; font-size:5em;color:#669CD3}
.share_qzone:before { content: "\e694"; font-size:5em;color:#ECB842}
.share_weixin:before { content: "\e623"; font-size:5em;color:#2DC100}
.share_ibaidu:before { content: "\e693"; font-size:5em;color:#41A7C6}

</style>

<div class="clicklayer"></div>
<div class="bdsharebuttonbox share_box" data-tag="share_1">
    <ul class="">
        <li class="">
            <a class="iconfont share_weibo" data-cmd="tsina"></a>
            <span>新浪微博</span>
        </li>
        <li class="">
            <a class="iconfont share_qq" data-cmd="sqq"></a>
            <span>QQ</span>
        </li>
        <li class="">
            <a class="iconfont share_qzone" data-cmd="qzone"></a>
            <span>QQ空间</span>
        </li>
        <li class="">
            <a class="iconfont share_weixin" data-cmd="weixin"></a>
            <span>微信</span>
        </li>
        
    </ul>
    <img src="/statics/img/close.png" class="share_box_close" data-url="" />
</div>

<script type="text/javascript" id="bdshare_js" data="type=tools&mini=1" ></script> 
<script type="text/javascript" id="bdshell_js"></script>

<script type="text/javascript">
$(function() {
    $(document).on("click",".share_box_close",function(){
        $('.share_box').slideToggle(200);
        $('.clicklayer').hide();
    });

    $(document).on("click",".btn_share_mobile",function(){
        $('.share_box').slideToggle(200);
        $('.clicklayer').show();

    });

});
</script>

<script>
{if !$is_weixin}
window._bd_share_config={
    "common":{
        "bdSnsKey":{
            "tsina":"2250175595",
            "tqq":"f7f278f9f3cb263580f20da21a0facf8"
        },
        "bdText":"","bdMini":"2","bdMiniList":false,"bdPic":"","bdStyle":"0","bdSize":"16"
    },
    "share":{}
};
with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='/statics/api/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)];

/*分享标题*/
window._bd_share_config.share.bdText='{$productdetail.brand_name} {$productdetail.product_name}';
/*分享链接*/
window._bd_share_config.share.bdUrl='{$promote_link}';
/*分享图片*/
window._bd_share_config.share.bdPic='{$productdetail.url}';
/*分享评论*/
window._bd_share_config.share.bdComment='我们的目标是，不管胖瘦都能轻松买到好衣服(M-7XL)';
/*分享摘要*/
window._bd_share_config.share.bdDesc='我在25BOY发现了【{$productdetail.product_name}】，25BOY国潮男装。';
/*抓图*/
window._bd_share_config.share.searchPic=0;

{/if}
</script>

