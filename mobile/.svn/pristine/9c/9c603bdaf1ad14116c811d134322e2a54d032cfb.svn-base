{include file="public/head.tpl" title=head}
<link rel="stylesheet" type="text/css" href="/statics/css/new_home.css?v={$version}">
{include file="public/page_header.tpl" title=page_header}

    <div id="bodybox">
        <section class="main home">

            <!-- <div class="new_home_top">
                <a href="/" class="logo"><img src="/statics/img/new_home/logo.png" /></a>
            </div> -->

            <div class="new_home_banner">
                <ul class="home_banner">
                    {foreach from=$banner_slide item=item }  
                    <li><a href="/?m=call&go={$item.ad_id}" title="{$item.adname}"><img src="{$item.srcurl}!w640" alt="{$item.adname}"></a></li>
                    {/foreach}
                </ul>
            </div>

            <div class="new_home_class">
                <!-- <b class="icon_hot"></b> -->
                <span><a href="/?m=category"><img src="/statics/img/new_home/icon-all.png" alt="二五仔 全部大码男装">全部产品</a></span>
                <span><a href="/?m=category&cid=60"><img src="/statics/img/new_home/icon-sale.png" alt="25BOY 大码品牌折扣">品牌折扣</a></span>
                <span><a href="/?m=share"><img src="/statics/img/new_home/icon-daren.png" alt="潮胖时尚 达人晒图 送潮流T恤">达人晒图</a></span>
                <span><a href="/?m=account&a=balance"><img src="/statics/img/new_home/icon-prepaid.png" alt="在线充值">充值预付</a></span>
                <span><a href="/?m=about&id=15"><img src="/statics/img/new_home/icon-join.png" alt="25BOY 合作加盟">合作加盟</a></span>
                <span><a href="http://www.25boy.cn/?pc=1"><img src="/statics/img/new_home/icon-pc.png" alt="25BOY 电脑版">电脑版</a></span>
                <span><a href="/?m=hd&a=promoteApply"><img src="/statics/img/new_home/icon-promote.png" alt="二五仔 返佣推广">分享返佣</a></span>
                <span><a href="https://25boy.kf5.com/kchat/30287?from=%E5%9C%A8%E7%BA%BF%E6%94%AF%E6%8C%81" target="_blank"><img src="/statics/img/new_home/icon-service.png" alt="二五仔 联系客服">联系客服</a></span>
                <!-- <span><a href="/h5/subscribe.html"><img src="/statics/img/new_home/icon-shipping.png" alt="二五仔 绑定包邮">绑定包邮</a></span> -->
                <!-- <span><a href="javascript:void(0);" class="show_QRcode"><img src="/statics/img/new_home/icon-public.png" alt="25BOY 关注公众号">公众号</a></span> -->
                <!-- <span><a href="/?m=sales"><img src="/statics/img/new_home/icon-lottery.gif" alt="二五仔 免费抽奖">免费抽奖</a></span> -->
                <!-- <span><a href="http://a.app.qq.com/o/simple.jsp?pkgname=com.sh25boy.mzv" target="_blank"><img src="/statics/img/new_home/icon-app.png" alt="二五仔 免费抽奖">客户端</a></span> -->
            </div>

           

            <!-- {if $is_holiday} -->
            <img src="{$holiday_pic}" alt="网站公告">
            <!-- {/if} -->
            <!-- {if $activity_banner} -->
            <span class="blank10 pagegrey"></span>
            <div class="new_sale_banner">
                {foreach from=$activity_banner item=item}
                {if $item['type'] == 'flash'}
                <video width="100%" src="{$item.contxt}" controls="controls" preload="preload" poster="{$item.srcurl}"></video>
                {else}
                <a href="{$item.url}" class="img_box"><img src="{$item.srcurl}" alt="{$item.adname}" /></a>
                {/if}
                {/foreach}
            </div>
            <!-- {/if} -->

            <!-- {if $product_new_list|count>0} -->
            <span class="blank10 pagegrey"></span>
            <div class="new_home_banner product_banner">
                {$new_banner}
                <ul class="product_banner_ul">
                    {section name=item loop=$product_new_list}
                    <li>
                    <a href="/?m=category&a=product&id={$product_new_list[item].product_id}" title="{$product_new_list[item].brand_name} {$product_new_list[item].product_name}" class="img_box"><img src="{$product_new_list[item].product_img}!w232" alt="{$product_new_list[item].brand_name} {$product_new_list[item].product_name}"></a>
                    <div class="txt">
                        <p class="title">{$product_new_list[item].product_name}</p>
                        <p class="price">{$product_new_list[item].price}</p>
                        <p class="tags"><b>新品价</b></p>
                    </div>
                    </li>
                    {/section}
                </ul>
            </div>
            <span class="blank10 pagegrey"></span>
            <!-- {/if} -->


            <!-- {if $index_categorys_items} -->
            {$category_banner}
            <ul class="product_list_gird_2 categorys-product">
                {foreach from=$index_categorys_items item=item}
                <li>
                    <div class="item">
                        <a href="{$item.url}" class="img_box"><img src="{$item.srcurl}" alt="{$item.adname}" /></a>
                    </div>
                </li>
                {/foreach}
            </ul>
            <span class="blank10 pagegrey"></span>
            <!-- {/if} -->


            <!-- {if $brands_list} -->
            {$brand_banner}
            <ul class="product_list_gird_3 categorys-accessory">
                {foreach from=$brands_list item=item}
                <li>
                    <div class="item">
                        <a href="{$item.url}" class="img_box" title="{$item.adname}"><img src="{$item.srcurl}" alt="{$item.adname}" /></a>
                    </div>
                </li>
                {/foreach}
            </ul>
            <span class="blank10 pagegrey"></span>
            <!-- {/if} -->

            <!-- {if $recommend_share} -->
            <div class="shareList">
                <div class="shareList-title">－ 精选晒图 －</div>
                <div class="shareList-main">
                    <ul class="share-photos">
                        {foreach from=$recommend_share item=item}
                        <li>
                            <a href="/?m=share&a=view&id={$item.share_id}" class="img_box" title=""><img src="{$item.image}!w640" alt="25boy-{$item.username}的晒图"></a>
                            <div class="share-txt">
                                <span class="share-avatar"><img src="{$item.userimg}" /><i>{$item.username}：</i></span>
                                {$item.content}
                            </div>
                        </li>
                        {/foreach}
                    </ul>
                </div>
            </div>
            <span class="blank10 pagegrey"></span>
            <!-- {/if} -->

            <!-- {if $banner_item|count>0} -->
            <div class="home_banner_item">
                {foreach from=$banner_item item=item}
                <p><a href="/?m=call&go={$item.ad_id}" title="{$item.adname}" class="img_box"><img src="{$item.srcurl}!w640" alt="{$item.adname}"></a></p>
                {/foreach}
            </div>
            <!-- {/if} -->


            <div class="hot_sale_list">
                <div class="new_home_row">
                    <div class="title">－ 人气热卖 －</div>
                </div>
                <ul class="product_list_gird_2" id="hot_sale_datalist"> </ul>
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
            </div>
            <span class="blank20"></span>
            {include file="public/bottom.tpl" title=bottom}
        </section>
    </div>


    {include file="public/js.tpl" title=js}
    <script src="/statics/js/root.js?v={$version}"></script>
    <link rel="stylesheet" href="statics/css/jquery.bxslider.css?v={$version}" type="text/css" />
    <script src="statics/js/jquery.bxslider.min.js"></script>
    <script src="statics/js/home.js?v={$version}"></script>
    <script>
        {if $is_weixin}
            var title = "25BOY!二五仔";
            var link  = "{$promote_link}";
            var url   = "http://m.25boy.cn/statics/img/logo_128x128.png";
            var desc  = "25BOY!二五仔 | 大码男装潮牌,一家专做大码的网站";
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

{include file="public/footer.tpl" title=footer}