{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<span id="btn_opensorttools">筛选</span>
<span id="btn_opencategorytools">分类</span>
<section id="sorttools" class="shadow">
    <div class="row">
        <div class="title">您想要如何排序</div>
        <div class="content">
            <div class="sortbtn sort_selected" id="default">默认</div>
            <div class="sortbtn" id="click">人气</div>
            <div class="sortbtn" id="sale">销量</div>
            <div class="sortbtn" id="price">价格<i class="iconfont">&#xe60a;</i></div>
            <div class="sortbtn" id="brand">品牌</div>
        </div>
    </div>
    <div class="row brandSet">
        <div class="brandsList">
        {foreach from=$brands item=item }  
        <span><a class="brandBtn" data-id="{$item.brand_id}" re_href="/?m=category&brand_id={$item.brand_id}"><img src="/statics/img/brand/brand-{$item.brand_id}.png" alt="{$item.brand_name}" /></a></span>
        {/foreach}
        </div>
        <div class="title priceSet">您要购买的价格区间</div>
    </div>
    <div class="row">
        <div class="content">
            <div class="price"><input type="text" value="" onkeyup="this.value=this.value.replace(/\D/g,'')" placeholder="￥" id="start_price" /></div>
            <div class="price-to">至</div>
            <div class="price"><input type="text" value="" onkeyup="this.value=this.value.replace(/\D/g,'')" placeholder="￥" id="end_price" /></div>
        </div>
    </div>
    <div class="row">
        <center>
            <button type="button" id="beginsort" class="btn">确定</button>
            <button type="button" id="cancelsort" class="btn btn_secondary">取消</button>
        </center>
    </div>
</section>
<section id="categorytools" class="shadow">
    <section class="categorybox">
        <div class="link_home"><a href="/">首页</a></div>
        <ul class="category category_root">
            {section name=item loop=$category}
                <li>
                    <a href="javascript:;" index="{$smarty.section.item.index}" rel="{$category[item].category_id}" {if $smarty.section.item.index ==0}class="current"{/if}>{$category[item].category_name}</a>
                </li>
            {/section}
        </ul>
        <ul class="category category_sub">
            {section name=item loop=$category[0].childrens}
                <li><a href="?m=category&cid={$category[0].childrens[item].category_id}">{$category[0].childrens[item].category_name}</a></li>
            {/section}
        </ul>
    </section>
</section>
<section class="main pagemain pagebgwhite">
    <span class="blank5"></span>
    <div class="gird_box">
        <div class="gird_items" id="productlist">
        </div>
    </div>
    <span class="blank10"></span>
    <span class="blank10"></span>
    <span class="blank10"></span>
    <span class="blank10"></span>
    <span class="blank10"></span>
    <span class="blank10"></span>
</section>
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
{include file="public/js.tpl" title=js}
<script>
    var cid = {$cid};
    $(function() {
        $(".selectproductbtnbox").click(function(){
            if($(".selectproductbtnbox i.arrowup").css("background-position")=="-40px 0px"){
                $(".selectproductbtnbox i.arrowup").css("background-position","0px 0px");
            }else{
                $(".selectproductbtnbox i.arrowup").css("background-position","-40px 0px");
            }
            $(".submenu").slideToggle();
        });
        {if $new}
        condition.new = 1;
        condition.default = 0;
        {/if}
        {if $sale}
        condition.sale = 1;
        condition.default = 0;
        {/if}
        {if $hot}
        condition.click = 1;
        condition.default = 0;
        {/if}
        {if $brand_id}
        condition.default = 0;
        condition.brand = 1;
        condition.brand_id = {$brand_id};
        {/if}
        {if $k}
        condition.k = '{$k}';
        {/if}
    });

</script>
<script>
    {if $is_weixin}
        var title = "{$category_name}";
        var link  = "{$promote_link}";
        var url   = "http://m.25boy.cn/statics/img/logo_128x128.png";
        var desc  = "我在25BOY发现了【{$category_name}】，25BOY国潮男装";
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
<script defer src="/statics/js/product.list.js?v={$version}"></script>

{include file="public/footer.tpl" title=footer}