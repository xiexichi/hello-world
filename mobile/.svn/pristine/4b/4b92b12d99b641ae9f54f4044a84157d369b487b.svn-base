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

            <div class="activityBody">

                <!-- {if $is_holiday} -->
                <img src="{$holiday_pic}" alt="网站公告">
                <!-- {/if} -->
                <!-- {if $activity_banner} -->
                <span class="blank10 pagegrey"></span>
                <div class="new_sale_banner">
                    {foreach from=$activity_banner item=item}
                    {if $item['type'] == 'flash'}
                    <span class="blank5 pagegrey"></span>
                    <video width="100%" src="{$item.contxt}" controls="controls" poster="{$item.srcurl}"></video>
                    {else}
                    <a href="{$item.url}" class="img_box"><img src="{$item.srcurl}" alt="{$item.adname}" /></a>
                    {/if}
                    {/foreach}
                </div>
                <!-- {/if} -->

                <!-- 合作联名区 -->
                <!-- {if $joint_name} -->
                    <span class="blank10 pagegrey"></span>
                    <div class="jointName">
                        {foreach from=$joint_name item=group key=key}
                        <div class="joint-group joint-group{$key}">
                            {foreach from=$joint_name[$key] item=item key=k}
                            <div class="joint-item joint-item{$k}">
                                <a href="{$item.url}">
                                <image src="{$item.srcurl}!w390" class="joint-img" />
                                </a>
                            </div>
                            {/foreach}
                        </div>
                        {/foreach}
                    </div>
                <!-- {/if} -->

            </div>

            <!-- {if $product_new_list|count>0} -->
            <div class="new_home_banner product_banner">
                {foreach from=$new_banner item=item}
                <span class="blank10 pagegrey"></span>
                <a href="{$item.url}" class="img_box"><img src="{$item.srcurl}" alt="{$item.adname}" /></a>
                {/foreach}
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


            <!-- 首页类目栏目图 -->
            <!-- {if $index_categorys_items} -->
            <div class="block-title">[分类]</div>
            <!-- data:index_categorys_items  -->
            {$category_banner}
            <div class="categoryList">
                <!-- data:index_categorys_items，一行两列分类图片，广告形式 -->
                {foreach from=$index_categorys_items item=item}
                <div class="categoryItem">
                    <a href="{$item.url}" class="img_box">
                        <image src="{$item.srcurl}!w390" class="categoryItem-img"></image>
                    </a>
                </div>
                {/foreach}
            </div>
            <span class="blank30 pagegrey"></span>
            <!-- {/if} -->


             <!-- 精选晒图 -->
            <!-- {if $recommend_share} -->
            <div class="shareList">
                <a class="share-more" href="/?m=share">
                    <image class="share-more-icon" src="https://api.25boy.cn/Public/images/camera-icon.png"></image>
                    <span class="share-more-text">[更多晒图]</span>
                </a>
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


            <!-- {if $brands_list} -->
            <div class="block-title">[品牌]</div>
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

            <!-- {if $banner_item|count>0} -->
            <div class="home_banner_item">
                {foreach from=$banner_item item=item}
                <p><a href="/?m=call&go={$item.ad_id}" title="{$item.adname}" class="img_box"><img src="{$item.srcurl}!w640" alt="{$item.adname}"></a></p>
                {/foreach}
            </div>
            <!-- {/if} -->


            <div class="hot_sale_list">
                <div class="new_home_row">
                    <div class="block-title">[热卖新品]</div>
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
            var title = "25BOY国潮男装";
            var link  = "{$promote_link}";
            var url   = "http://m.25boy.cn/statics/img/logo_128x128.png";
            var desc  = "25boy潮牌新国货，7天无理由退换货。复古,古着,原创,中国风T恤，高桥石尚运动服，潮牌与运动完美结合";
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