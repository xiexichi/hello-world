{include file="public/head.tpl" title=head}
{include file="public/page_header.tpl" title=header}

<div id="bodybox">
    <section class="main pagemain pagegrey" style="background-color: #efefef">
        {if $session_uid==""||$session_uid==0}
            {include file="public/remind_login.tpl" title=header}
        {else}

            <!-- <section class="balance_total ycenter bg_danger">
                <div class="content" style="margin:0 auto;">
                    <div class="totaltext" style="font-size:3em">轻松推广,轻松赚钱</div>
                </div>
            </section> -->

            <section class="balance_class borderbottom nav-tags nav-tags-col3">
                <span class="item {if $c=='pp'}current{/if}"><a href="/?m=account&a=promote&c=pp">商品</a></span>
                <span class="item {if $c=='pc'}current{/if}"><a href="/?m=account&a=promote&c=pc">类目</a></span>
                <span class="item {if $c=='pw'}current{/if}"><a href="/?m=account&a=promote&c=pw">网站</a></span>
            </section>

            {if $c=='pp'}
                <!-- 单品 -->
               <section class="product_list">
                    <ul id="listBox">
                    {foreach $promote_product as $k=>$v} 
                      <li>
                            <div class="list_left"><a href="/?m=category&a=product&id={$v['product_id']}"><img src="{$v['url']}!w200" /></a></div>
                            <div class="list_right">
                                <dl>
                                   <dt class="fs1_2">
                                    {$v['product_name']}
                                    <!-- <span class="pull-right monthSale">月销量&nbsp;<b class="">{$v['monthSale']}</b></span> -->

                                   </dt> 
                                   <dd class="right_dl_left">
                                        <p class="sku-sn">编号：{$v['sku_sn']}</p>
                                        <p class="price"><span class="pr1 fs1_2">¥{$v['price']}</span>提成：<span>{$v['commission_rate']}%</span></p>
                                        <p class="two">赚: ¥<span class="fs1_2">
                                        {math equation="x * y / 100" x=$v['price'] y=$v['commission_rate'] format="%.2f"}
                                       <!--  {$v['price']*$v['commission_rate']/100} -->
                                        </span></p> 
                                   </dd>
                                   <dd class="right_dl_right"><a href="/?m=category&a=product&id={$v['product_id']}">前往推广</a></dd>
                                   <!-- <dd class="right_dl_right"><button data-url="{$v['url']}" data-title="{$v['product_name']}" data-link="{$v['link']}" onclick="promote_now(this,{$promote['promote_id']},0,{$v['product_id']});">立即推广</button></dd> -->
                                </dl>

                            </div>
                        </li>
                    {foreachelse}
                        <div  class="nodata">暂无数据！</div>
                    {/foreach}
                     </ul>

                    </section>

            {elseif $c=='pc'}
                <!-- 类目 -->
               <section class="product_list">
                    <ul id="listBox">
                    {foreach $promote_category as $k=>$v} 
                        <li>
                            <div class="list_left"><a href="?m=category&cid={$v['category_id']}"><span class="font-radius" style="background-color: {$v['bgColor']}">{$v['imgFont']}</span></a></div>
                            <div class="list_right">
                                <dl>
                                   <dt class="fs1_2">{$v['category_name']}</dt> 
                                   <dd class="right_dl_left">
                                        <p class="one mb0_3">宝贝数：{$v['product_num']}</p> 
                                        <p class="one">提成：<span class="big C_da3335">{$v['commission_rate']}%</span></p>
                                   </dd>
                                   <dd class="right_dl_right" style="padding:0;"><button data-url="/statics/img/logo_128x128.png" data-title="{$v['category_name']}" data-link="{$v['link']}" onclick="promote_now(this,{$promote['promote_id']},1,{$v['category_id']});">立即推广</button></dd>
                                </dl>
                            </div>
                        </li>
                    {foreachelse}
                        <div  class="nodata">暂无数据！</div>
                    {/foreach}
                     </ul>
                    </section>
            {elseif $c=='pw'}
                {if empty($promote_website)}
                    <div  class="nodata">暂无数据！</div>
                {else}
                  <section class="product_list">
                    <ul id="listBox">
                        <li>
                            <div class="list_left"><img src="/statics/img/logo_128x128.png" /></div>
                            <div class="list_right">
                                <dl>
                                   <dt class="website-text">25BOY国潮男装</dt> 
                                   <dd class="right_dl_left">
                                        <p class="one mb0_3">宝贝数：{$promote_website['stock_product_num']}</p> 
                                        <p class="one">提成：<span class="big C_da3335">{$promote_website['commission_rate']}%</span></p>
                                   </dd>
                                   <dd class="right_dl_right"><button data-url="/statics/img/logo_128x128.png" data-title="25BOY国潮男装" data-link="{$promote_website['link']}" onclick="promote_now(this,{$promote['promote_id']},2,0);">立即推广</button></dd>
                                </dl>
                            </div>
                        </li>
                    </ul>
                    </section>
                {/if}

            {/if}

        {/if}
    </section>
    
    <div class="loaddiv" style="display: none;">
        <div class="blank10"></div>
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

<!-- 立即推广 -->
<div class="clicklayer"></div>
<div class="share_box" >
    <ul>
        <li>
            <p class="head"><span>方法1</span>微信直接分享</p>
            <div class="body">
                <p>点击微信右上角<img src="/statics/img/dian.png" alt="" class="dian"></p>
                <p>将网站分享给好友进行推广</p>
            </div>
        </li>
        <li>
            <p class="head"><span>方法2</span>复制以下链接，转发推广</p>
            <div class="body">
                <p>好物推荐：<span class="product_name"></span></p>
                <p class="url"></p>
            </div>
        </li>
        <li>
            <p class="head"><span>方法3</span>点击右侧二维码推广</p>
            <div class="body relative">
                <p class="QR">点击右侧获取二维码, 请好友扫描</p>
                <a class="qrcode" href="/ajax/get.promote.qrcode.php" id="btn_qrcode" data-link=""><i class="iconfont">&#xe689;</i></a>
            </div>
        </li>
        <li>
            <p class="head"><span>方法4</span>商品详情页，通过分享功能推广</p>
            <div class="body">
                <p>点击推广图标<i class="iconfont icon_share">&#xe65f;</i>获取推广素材</p>
                <p class="">将商品分享给好友进行推广</p>
            </div>
        </li>
    </ul>
    <img src="/statics/img/close.png" class="share_box_close" data-url="" />
</div>

<div class="shade">
    <img src="/statics/img/ani_arrow.gif" class="shade-ani">
    <img src="/statics/img/pointer_click.png" class="shade-pointer"/>
</div>


<link rel="stylesheet" type="text/css" href="/statics/css/account.css?v={$version}">
<link rel="stylesheet" type="text/css" href="/statics/css/promote.css?v={$version}">
{include file="public/js.tpl" title=js}
<script type="text/javascript">
var c = "{$c}";
var searchCategory="{$searchCategory}";
var searchKeywords="{$searchKeywords}";
var secondly_category = {$secondly_category};
</script>
<script src="/statics/js/promote.js?v={$version}"></script>
<script src="/statics/js/promote.module.js?v={$version}"></script>
<!-- <script src="/statics/js/clipboard.min.js"></script> -->
<script>

/*
 *  弹出推广页面
 */
function promote_now(obj,promote_id,type,item_id) {
    var link = $(obj).data('link');
    var title = $(obj).data('title');
    var url = $(obj).data('url');
    var _this = obj;
    $('.clicklayer').show();
    $('.share_box').show();
    if(type == 2) {
        title = "25BOY国潮男装";
    }
    $('.share_box .product_name').text(title);
    $('.share_box .url').text(link);
    $('#btn_qrcode').data('link',link);

}
function show_wx_share_div(imgsrc){
    $("#systemnoticebox").remove();
    if(imgsrc=='' || imgsrc==undefined){
        imgsrc = '/statics/img/guide.png';
    }
    var body = document.body;
    var div = document.createElement("div");
    div.id = "mcover";
    div.className = "mcover wx_share_box";
    div.innerHTML = '<img src="'+imgsrc+'" /><img src="/statics/img/ani_arrow.gif" class="ani" />';
    body.appendChild(div);
    $("#mcover").click(function(){
        $(this).remove();
        $.cookie('show_wx_share_div', imgsrc);
    })
}

$(function() {
    var QRlayer;
    $('#btn_qrcode').on('click',function(e){
        /*阻止屏幕拖动行为*/
        $(document).on('touchmove', function(e) {
            e.stopPropagation();
            e.preventDefault();
        });
        var _html = '<p><img src="/statics/img/loader.gif" /></p><p style="color:#999;font-size:12px;">正在生成二唯码</p>';

        /*弹出层*/
        QRlayer = layer.open({
            className : 'qrcodebox',
            content : _html,
            end: function(){
                /*取消阻止屏幕拖动行为*/
                $(document).off('touchmove');
            }
        });
        var link = $(this).data('link');
        /*生成二唯码*/
        var options = [{
            "url": $(this).attr('href'),
            "data":{
                "link":link
            },
            "type":"GET", 
            "dataType":"json",
        }];
        Load(options, function(json){
            /*layer内容*/
            if(json.ms_code=='success'){
                _html = '<div class="code">'+json.qr+'</div>';
            }else{
                _html = json.ms_msg;
            }
            $('.qrcodebox .layermcont').html(_html);
        },function(){});

        return false;
    });

    $(document).on("click",".share_box_close",function(){
        $('.share_box').hide();
        $('.clicklayer').hide();
    });
});


$(function(){

    $('#listBox li:first').css('borderTop','1px solid #ddd');

    /* **************************************************************
     * 分享
     * **************************************************************/
     {if $is_weixin}
        var link = "{$promote_website['link']}";
        var title = "25BOY国潮男装";
        var url = "http://m.25boy.cn/statics/img/logo_128x128.png";
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
                desc : '我们的目标是，不管胖瘦都能轻松买到好衣服(M-7XL)',
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
                desc : '我们的目标是，不管胖瘦都能轻松买到好衣服(M-7XL)',
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
                desc : '我们的目标是，不管胖瘦都能轻松买到好衣服(M-7XL)',
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
                desc : '我们的目标是，不管胖瘦都能轻松买到好衣服(M-7XL)',
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


});

</script>

{if $session_uid==""||$session_uid==0}
{literal}
    <script>
        $(function(){
            user.by_btn("#cart_user_by","by",true);
        })
    </script>
{/literal}
{else}
<script src="/statics/js/account_order.js?v={$version}"></script>
{/if}


{include file="public/footer.tpl" title=footer}