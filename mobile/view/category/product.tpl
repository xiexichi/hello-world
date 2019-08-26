{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<link rel="stylesheet" href="/statics/css/common.css">


    <div id="bodybox">

    <section class="main pagemain pagegrey" style="padding-bottom:60px;">
        {if $productdetail.img|count>0}
        <div id="productbanner">
            <ul class="bxslider">
                {section name=item loop=$productdetail.img}
                <li><a href="{$productdetail.img[item].full}" class="swipebox" title="25BOY {$productdetail.product_name}"><img src="{$productdetail.img[item].thumb}" alt="25BOY {$productdetail.product_name}"></a></li>
                {/section}
            </ul>
        </div>
        {/if}

        {if $productdetail.stock==1&&$productdetail.total_quantity>0}

        <div class="productbase">
            <div class="inbox">
                <!-- <a href="javascript:void(0);" class="share show_promote_layer"><i class="iconfont">&#xe65f;</i></a> -->
                <h1>{$productdetail.brand_name} {$productdetail.product_name}</h1>
                <p class="price">
                    {if $miaoing.miao_price!='' && $miaoing.miao_price>0}
                        <span class="price_label">{$miaoing.miao_title}</span>
                    {else if !empty($events)}
                        <span class="price_label">活动价</span>
                    {/if}

                    {if $miaoing.miao_price}
                        <sup>￥</sup><span class="new">{$productdetail.miao_price}</span>&nbsp;<span class="old"><sup>￥</sup>{$productdetail.market_price}</span>
                        <span class="miao_time"><i class="iconfont">&#xe600;</i> {$miaoing.end_date}&nbsp;结束</span>
                    {elseif $miao.miao_price}
                        <sup>￥</sup><span class="new">{$productdetail.miao_price}</span>&nbsp;<span class="old"><sup>￥</sup>{$productdetail.market_price}</span>
                        <span class="miao_time"><i class="iconfont">&#xe600;</i> {$miao.start_date}&nbsp;开始</span>
                    {elseif $productdetail.seller_price}
                        <span class="price_label">供货价</span>
                        <sup>￥</sup><span class="new">{$productdetail.seller_price}</span>&nbsp;<span class="old"><sup>￥</sup>{$productdetail.price}</span>
                    {else}
                        {if $productdetail.market_price==$productdetail.price}

                            {if $after_event_price < $productdetail.price}    
                            <sup>￥</sup><span class="new">{$after_event_price}</span>&nbsp;<span class="old"><sup>￥</sup>{$productdetail.price}</span>
                            {else}
                            <sup>￥</sup>{$productdetail.price}
                            {/if}
                        {else}
                            <sup>￥</sup><span class="new">{$productdetail.price}</span>&nbsp;<span class="old"><sup>￥</sup>{$productdetail.market_price}</span>
                        {/if}
                    {/if}
                </p>
                {if $productdetail.favorites>0}
                <p class="favorites"><i></i> 已有{$productdetail.favorites}人喜欢</p>
                {/if}
                <div class="ship_fee">{$product_title2}</div>
                <div class="ship_fee">运费：<a href="/h5/subscribe.html" title="绑定微信号即享包邮">{$productdetail.delivery_desc}</a></div>
            </div>

            {if $activitys}
                <div class="evens">
                    {foreach from=$activitys item=event key=key name=activitys}
                    <div class="event_item {if $key==0}first{/if}">
                        <i class="iconfont titico">&#xe62b;</i>
                        <i class="iconfont more">&#xe629;</i>
                        <p class="event_title">{$event.title}</p>
                        <p class="event_time"><i class="iconfont">&#xe600;</i> {$event.start_date}&nbsp;至&nbsp;{$event.end_date}</p>
                    </div>
                    {/foreach}
                </div>
            {/if}
        </div>

        <div class="productparams">
            <div class="row require propcolor">
                <div class="subject">颜色</div>
                <div class="content">
                    {if $productdetail.prop}
                    {foreach from=$productdetail.prop item=color key=key}
                        <div class="item icolor">
                            <div class="coloritem" data-name="{$color.name}" data-img="{$color.img}">
                                <img src="{$color.img}!w200" />
                                <span>{$color.name}</span>
                            </div>
                        </div>
                    {/foreach}
                    {/if}
                </div>
            </div>
            <div class="row require propsize">
                <div class="subject">尺码</div>
                <div class="content">
                    {if $productdetail.size_props.size}
                    {foreach from=$productdetail.size_props.size item=size key=key}
                        <span class="sizeitem" data-name="{$size.sku}" data-num="{$size.num}" data-sync="{$size.sync}">{$size.sku}</span>
                    {/foreach}
                    <div class="blank5"></div>
                    {/if}
               </div>
                <div id="presale_time"></div>
            </div>
            
            <div class="row require" id="quantitybox">
                <div class="subject">数量</div>
                <div class="content">
                    <a class="mid"></a><div class="quantity"><input type="text" maxvalue="10" value="1" onkeyup="this.value=this.value.replace(/\D/g,'')" id="start_price" /></div><a class="add"></a>
                    <span class="tips"></span>
                </div>
            </div>

            <div class="row">
                <div class="subject">库存</div>
                <div class="content" id="in_stock">{$productdetail.total_quantity}</div>
            </div>

        </div>
        
        <!-- 套餐数据 -->
        {if count($combomeals) gt 0}
        <div class="w-100p">
            <!-- <div class="divider"></div> -->
            <p class="combemeal-label">搭配套餐</p>
            <div class="divider"></div>
            <div class="combomeals">
                <div class="fled-flex-start" style="width:{count($combomeals) * 20}rem;">
                    {foreach from=$combomeals item=i key=k}
                    <div class="combomeal-item ml-1">
                        <a href="/?m=category&a=combomeal&id={$i.combomeal_id}">
                            <div class="fled-flex-start">
                                <div class="combomeal-item-img-div w-35p">
                                    <img class="combomeal-item-img" src="{$i.url}" alt="{$i.combomeal_title}">
                                </div>
                                <div class="ml-1 w-55p">
                                    <p class="combomeal-title text-ellipsis mb-0-5 fs-1">{$i.combomeal_title}</p>
                                    <p class="combomeal-discount mb-0-3 fs-0-8">可省￥{$i.discountTotal}</p>
                                    <p class="combomeal-date mb-0-3 fs-0-8">活动至{$i.end_date}</p>
                                </div>                    
                            </div>
                        </a>
                    </div>
                    {/foreach}                   
                </div>
            </div>
            <div class="divider"></div>
        </div>
        {/if}

        <div class="moredetail">
            点击显示更多产品信息
            <i class="iconfont">&#xe617;</i>
            <div class="blank10"></div>
        </div>
        <div class="productcontent">
            <div class="title">产品图文详情</div>
            <div class="content"></div>
        </div>
        {else}
            <div class="productbase">
                <div class="inbox">
                    <h1>{$productdetail.brand_name} {$productdetail.product_name}</h1>
                    <h3>产品已经下架</h3>
                </div>
            </div>
        {/if}
    </section>
    <script>
        var pid = "{$productdetail.product_id}",presale_date="{$productdetail.presale_date}",sku_sn="{$productdetail.sku_sn}",stock_json={$stock_props_json},size_props_json={$size_props_json};
    </script>
    {include file="public/product.bottom.tpl" title=nav}
    </div>
    {include file="public/js.tpl" title=js}
    <link rel="stylesheet" href="statics/css/jquery.bxslider.css" type="text/css" />
    <script src="statics/js/jquery.bxslider.min.js"></script>
    <link rel="stylesheet" href="statics/css/swipebox.css">
    <script src="statics/js/jquery.swipebox.min.js"></script>
    <script src="statics/js/product.detail.js?v={$version}"></script>
    <script type="text/javascript">
    $(function(){
        winW = $(window).width();
        if(winW>640) winW=640;
        $('#productbanner .bx-viewport').css('height',winW);
        $('#productbanner .bx-viewport li, #productbanner .bx-viewport img').css({
            'height':winW,'width':winW
        });

        var isgetcode = false;
        $('.show_promote_layer').on('click',function(){
            if(isgetcode){
                showShareLayer();
                return true;
            }
            
            /*商品推广图片*/
            getProductPreviewCode("{$productdetail.product_id}",function(res){
                if(res.code == 0){
                    isgetcode = true;
                    $('.share-photos ul').append('<li><span><img src="http://api.25boy.cn'+res.rs+'"></span></li>');

                    /*红包推广图片*/
                    getRedpackPreviewCode(function(rs){
                        if(rs.code == 0){
                            $('.share-photos ul').append('<li style="width:65%;"><span><img src="http://api.25boy.cn'+rs.rs+'"></span></li>');
                        }
                    },res.pid);
                }
                showShareLayer();
            });
        });

        function showShareLayer(){
            layer.open({
                title: [
                    '分享宝贝赚佣金',
                    'background-color: #FF4351; color:#fff;'
                ],
                content: '<div class="show_promote_layer_box">'+$('.show_promote_layer_box').html()+'</div>',
                style: 'background-color:#fff;color:#333;',
                className: 'share-layer'
            });
        }
    });

    </script>

{literal}
<style type="text/css">
.share-layer .layermcont{padding:0;}
.show_promote_layer_box{text-align:left;}
.show_promote_layer_box .share-inbox{padding:10px 20px;font-size:14px;}
.show_promote_layer_box .share-text{color:#000;}
.show_promote_layer_box .share-logintip{background:#fcf8e3;color:#8a6d3b;padding:5px 10px;font-size:12px;border-radius:6px;margin-top:5px;}
.show_promote_layer_box .share-photos{margin-top:5px;}
.show_promote_layer_box .share-photos ul{clear:both;overflow:hidden;}
.show_promote_layer_box .share-photos ul li{float:left;width:33%;}
.show_promote_layer_box .share-photos ul li span{padding-right:5px;padding-top:5px;display:block;}
.show_promote_layer_box .share-photos ul li img{display:block;}
.show_promote_layer_box .share-instro{background:#f2f2f2;border-top:1px solid #ddd;padding:10px 20px;margin-top:5px;color:#999;font-size:12px;}
.show_promote_layer_box .share-instro ol{list-style:decimal;padding-left:1em;}
</style>
{/literal}
<div class="show_promote_layer_box" style="display:none;">
    <div class="share-inbox">
        <div class="share-text">我在25BOY发现了【{$simple_title}】很不错，推荐给你看看。领50元红包后更便宜哦！</div>
        {if $promote_link}
        <div class="share-link">{$promote_link}</div>
        {else}
        <div class="share-logintip">注意：你还没有登录或者未成为推广者，不能获取推广链接，<a href="javascript:layerLoginTap();">请点击这里</a>。</div>
        {/if}
        <div class="share-photos">
            <ul>
                
            </ul>
        </div>
    </div>
    <div class="share-instro">
        <ol>
            <li>请长按保存以上图片并分享到朋友圈，了解更多返佣信息<a href="/?m=hd&a=promoteApply" style="color:#337ab7" target="_blank">请点击这里</a>。</li>
            <li>想要得到更好的推广效果，建议根据自身情况自行编辑文字描述。</li>
            <li>你也可以点击微信右上角菜单分享此页面给好友。</li>
            <li>上面的二唯码和链接是你的专属推广链接，你可以分享到任何地方。</li>
        </ol>
    </div>
</div>

{include file="public/footer.tpl" title=footer}