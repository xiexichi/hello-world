{include file="public/head.tpl" title=head}
<body class="home">
    {if $site_top_banner}
    <div class="top_banner">
      {$site_top_banner}
    </div>
    {/if}
    
    <div id="bodybox">
        <section class="main home">
            <link rel="stylesheet" type="text/css" href="/statics/css/new_home.css">

            <div class="new_home_top">
                <a href="/" class="logo"><img src="/statics/img/new_home/logo.png" /></a>
                <div class="head_search">
                    <span class="imgbtn"><i class="iconfont">&#xe613;</i></span>
                    <input type="text" id="keyword" placeholder="潮TEE, 休闲裤, 牛仔裤" value="" />
                    <span class="close"><i class="iconfont">&#xe601;</i></span>
                </div>
            </div>

            <div class="brand_list">
                <div class="brand_list_ul">
                    {foreach from=$brands item=item key=key}  
                        <div class="item"><a href="/?m=category&brand_id={$item.brand_id}"><img src="/statics/img/brand/brand-{$item.brand_id}.png" alt="{$item.brand_name}" /></a></div>
                    {/foreach}
                </div>
            </div>

            <div class="new_home_banner">
                <ul class="home_banner">
                    <li>
                        <a href="/?m=call&go=48" title="晒图送礼">
                            <img src="http://img.25miao.com/114/1458904073.jpg!w640" alt="晒图送礼"></a>
                    </li>
                    <li>
                        <a href="/?m=call&go=42" title="HARDLY EVER’S 中国风 狮子头罗纹领经典印花短袖T恤">
                            <img src="http://img.25miao.com/114/1455612716.jpg!w640" alt="HARDLY EVER’S 中国风 狮子头罗纹领经典印花短袖T恤"></a>
                    </li>
                    <li>
                        <a href="/?m=call&go=45" title="高桥石尚">
                            <img src="http://img.25miao.com/114/1455612966.jpg!w640" alt="高桥石尚"></a>
                    </li>
                    <li>
                        <a href="/?m=call&go=46" title="HARDLY EVER’S 复古民族风收脚休闲裤">
                            <img src="http://img.25miao.com/114/1455615881.jpg!w640" alt="HARDLY EVER’S 复古民族风收脚休闲裤"></a>
                    </li>
                </ul>
            </div>

            <div class="new_home_class">
                <span><a href="/?m=category"><img src="/statics/img/new_home/icon-all.png" alt="二五仔 全部大码男装">全部产品</a></span>
                <span><a href="/?m=trends"><img src="/statics/img/new_home/icon-trend.png" alt="最新潮流资讯">潮流资讯</a></span>
                <span><a href="/?m=category&a=new.product"><img src="/statics/img/new_home/icon-new.png" alt="25BOY 大码新品推荐">新品推荐</a></span>
                <span style="border-color:#E18A8A"><b class="icon_hot"></b><a href="/?m=share"><img src="/statics/img/new_home/icon-daren.png" alt="潮胖时尚 达人晒图 送潮流T恤">达人晒图</a></span>
            </div>

           <span class="blank10"></span>
            <div class="new_home_row">
                <div class="title">
                    <div class="text">
                        <span class="fl">新品上架</span>
                        <span class="fl en">NEW<br/>ARRIVAL</span>
                    </div>
                    <div class="more">
                        <a href="/?m=category&new=1">更多新品</a>
                    </div>
                </div>
            </div>
            <div class="new_home_banner product_banner">
                <ul class="product_banner_ul">
                    <li>
                        <a href="/?m=category&a=product&id=578" title="银鳞堂 前无古人货车帽">
                            <img src="http://img.25miao.com/638/1458554160.jpg" alt="银鳞堂 前无古人货车帽"></a>
                    </li>
                    <li>
                        <a href="/?m=category&a=product&id=579" title="银鳞堂 刺绣货车棒球帽">
                            <img src="http://img.25miao.com/639/1458554388.jpg" alt="银鳞堂 刺绣货车棒球帽"></a>
                    </li>
                    <li>
                        <a href="/?m=category&a=product&id=581" title="HEA 【预售】狮子头刺绣休闲短裤">
                            <img src="http://img.25miao.com/641/1458555493.jpg" alt="HEA 【预售】狮子头刺绣休闲短裤"></a>
                    </li>
                    <li>
                        <a href="/?m=category&a=product&id=580" title="HEA 【预售】狮子头刺绣原色牛仔短裤">
                            <img src="http://img.25miao.com/640/1458554915.jpg" alt="HEA 【预售】狮子头刺绣原色牛仔短裤"></a>
                    </li>
                    <li>
                        <a href="/?m=category&a=product&id=568" title="银鳞堂 【预售】年年有余短袖T恤">
                            <img src="http://img.25miao.com/628/1457936984.jpg" alt="银鳞堂 【预售】年年有余短袖T恤"></a>
                    </li>
                    <li>
                        <a href="/?m=category&a=product&id=569" title="银鳞堂 国潮印花短袖T恤">
                            <img src="http://img.25miao.com/629/1457937540.jpg" alt="银鳞堂 国潮印花短袖T恤"></a>
                    </li>
                    <li>
                        <a href="/?m=category&a=product&id=570" title="银鳞堂 【预售】志在必得印花T恤">
                            <img src="http://img.25miao.com/630/1457937980.jpg" alt="银鳞堂 【预售】志在必得印花T恤"></a>
                    </li>
                    <li>
                        <a href="/?m=category&a=product&id=571" title="银鳞堂 【预售】老子印花短袖T恤">
                            <img src="http://img.25miao.com/631/1457938378.jpg" alt="银鳞堂 【预售】老子印花短袖T恤"></a>
                    </li>
                    <li>
                        <a href="/?m=category&a=product&id=572" title="银鳞堂 【预售9折】动如脱兔短袖T恤">
                            <img src="http://img.25miao.com/632/1457938665.jpg" alt="银鳞堂 【预售9折】动如脱兔短袖T恤"></a>
                    </li>
                    <li>
                        <a href="/?m=category&a=product&id=573" title="银鳞堂 东成西就短袖T">
                            <img src="http://img.25miao.com/633/1457939477.jpg" alt="银鳞堂 东成西就短袖T"></a>
                    </li>
                    <li>
                        <a href="/?m=category&a=product&id=574" title="银鳞堂 浑天宝鉴短袖T恤">
                            <img src="http://img.25miao.com/634/1457939663.jpg" alt="银鳞堂 浑天宝鉴短袖T恤"></a>
                    </li>
                    <li>
                        <a href="/?m=category&a=product&id=575" title="银鳞堂 如虎添翼短袖T恤">
                            <img src="http://img.25miao.com/635/1457939935.jpg" alt="银鳞堂 如虎添翼短袖T恤"></a>
                    </li>
                    <li>
                        <a href="/?m=category&a=product&id=576" title="银鳞堂 三阳开泰短袖T恤">
                            <img src="http://img.25miao.com/636/1457940212.jpg" alt="银鳞堂 三阳开泰短袖T恤"></a>
                    </li>
                    <li>
                        <a href="/?m=category&a=product&id=577" title="银鳞堂 四驱兄弟短袖T恤">
                            <img src="http://img.25miao.com/637/1457940565.jpg" alt="银鳞堂 四驱兄弟短袖T恤"></a>
                    </li>
                    <li>
                        <a href="/?m=category&a=product&id=556" title="高桥石尚 恶犬日系运动开衫T恤">
                            <img src="http://img.25miao.com/612/1453865263.jpg" alt="高桥石尚 恶犬日系运动开衫T恤"></a>
                    </li>
                    <li>
                        <a href="/?m=category&a=product&id=554" title="高桥石尚 恶犬日系运动短袖T恤">
                            <img src="http://img.25miao.com/610/1453863617.jpg" alt="高桥石尚 恶犬日系运动短袖T恤"></a>
                    </li>
                    <li>
                        <a href="/?m=category&a=product&id=557" title="高桥石尚 日系运动短袖T恤">
                            <img src="http://img.25miao.com/613/1453864662.jpg" alt="高桥石尚 日系运动短袖T恤"></a>
                    </li>
                    <li>
                        <a href="/?m=category&a=product&id=540" title="高桥石尚 下克上日系运动短袖T恤">
                            <img src="http://img.25miao.com/596/1453692349.jpg" alt="高桥石尚 下克上日系运动短袖T恤"></a>
                    </li>
                    <li>
                        <a href="/?m=category&a=product&id=561" title="银鳞堂 棉麻休闲短裤">
                            <img src="http://img.25miao.com/584/1454233927.jpg" alt="银鳞堂 棉麻休闲短裤"></a>
                    </li>
                    <li>
                        <a href="/?m=category&a=product&id=560" title="银鳞堂 刺绣休闲裤短裤">
                            <img src="http://img.25miao.com/621/1454232457.jpg" alt="银鳞堂 刺绣休闲裤短裤"></a>
                    </li>
                </ul>
            </div>
            <span class="blank10"></span>

            <div class="home_banner_item">
                <p>
                    <a href="/?m=call&go=35" title="EVISU 大M牛仔裤限量抢购">
                        <img src="http://img.25miao.com/114/1454294737.jpg!w640" alt="EVISU 大M牛仔裤限量抢购"></a>
                </p>
                <p>
                    <a href="/?m=call&go=65" title="银鳞堂休闲裤">
                        <img src="http://img.25miao.com/114/1454136222.jpg!w640" alt="银鳞堂休闲裤"></a>
                </p>
                <p>
                    <a href="/?m=call&go=62" title="HARST918">
                        <img src="http://img.25miao.com/114/1455616671.jpg!w640" alt="HARST918"></a>
                </p>
                <p>
                    <a href="/?m=call&go=63" title="HART677">
                        <img src="http://img.25miao.com/114/1455616670.jpg!w640" alt="HART677"></a>
                </p>
                <p>
                    <a href="/?m=call&go=64" title="972">
                        <img src="http://img.25miao.com/114/1455616672.jpg!w640" alt="972"></a>
                </p>
                <p>
                    <a href="/?m=call&go=61" title="508">
                        <img src="http://img.25miao.com/114/1455616668.jpg!w640" alt="508"></a>
                </p>
            </div>

            <div class="new_home_row">
                <div class="title">
                    <div class="text">
                        <span class="fl">人气热卖</span>
                        <span class="fl en">HOT<br/>SELLER</span>
                    </div>
                </div>
            </div>
            <ul class="product_list_gird_2">
                <li>
                    <div class="item">
                        <a href="/?m=category&a=product&id=401" title="HARDLY EVER’S 执念印花T恤">
                            <img src="http://img.25miao.com/446/1447828984.jpg" alt="HARDLY EVER’S 执念印花T恤" />
                        </a>
                        <span class="brand">HARDLY EVER’S</span>
                        <span class="productname">执念印花T恤</span>
                        <span class="price"> <sup>￥</sup>
                            99.00
                        </span>
                    </div>
                </li>
                <li>
                    <div class="item">
                        <a href="/?m=category&a=product&id=358" title="HARDLY EVER’S 恶搞迷彩悟空头T恤">
                            <img src="http://img.25miao.com/403/1447830293.jpg" alt="HARDLY EVER’S 恶搞迷彩悟空头T恤" />
                        </a>
                        <span class="brand">HARDLY EVER’S</span>
                        <span class="productname">恶搞迷彩悟空头T恤</span>
                        <span class="price"> <sup>￥</sup>
                            99.00
                        </span>
                    </div>
                </li>
                <li>
                    <div class="item">
                        <a href="/?m=category&a=product&id=551" title="银鳞堂 麻棉七分袖衬衫">
                            <img src="http://img.25miao.com/608/1453716052.jpg" alt="银鳞堂 麻棉七分袖衬衫" />
                        </a>
                        <span class="brand">银鳞堂</span>
                        <span class="productname">麻棉七分袖衬衫</span>
                        <span class="price">
                            <sup>￥</sup>
                            178.00
                        </span>
                    </div>
                </li>
                <li>
                    <div class="item">
                        <a href="/?m=category&a=product&id=550" title="银鳞堂 中国风七分袖衬衫">
                            <img src="http://img.25miao.com/607/1453715095.jpg" alt="银鳞堂 中国风七分袖衬衫" />
                        </a>
                        <span class="brand">银鳞堂</span>
                        <span class="productname">中国风七分袖衬衫</span>
                        <span class="price">
                            <sup>￥</sup>
                            178.00
                        </span>
                    </div>
                </li>
                <li>
                    <div class="item">
                        <a href="/?m=category&a=product&id=466" title="HARDLY EVER’S 小脚潮男休闲裤">
                            <img src="http://img.25miao.com/512/1449481106.jpg" alt="HARDLY EVER’S 小脚潮男休闲裤" />
                        </a>
                        <span class="brand">HARDLY EVER’S</span>
                        <span class="productname">小脚潮男休闲裤</span>
                        <span class="price">
                            <sup>￥</sup>
                            168.00
                        </span>
                    </div>
                </li>
                <li>
                    <div class="item">
                        <a href="/?m=category&a=product&id=47" title="HARDLY EVER’S 纯色束脚休闲裤">
                            <img src="http://img.25miao.com/50/1458013342.jpg" alt="HARDLY EVER’S 纯色束脚休闲裤" />
                        </a>
                        <span class="brand">HARDLY EVER’S</span>
                        <span class="productname">纯色束脚休闲裤</span>
                        <span class="price">
                            <sup>￥</sup>
                            168.00
                        </span>
                    </div>
                </li>
                <li>
                    <div class="item">
                        <a href="/?m=category&a=product&id=527" title="银鳞堂 【部分预售】刺绣棉麻小脚休闲裤">
                            <img src="http://img.25miao.com/583/1453026523.jpg" alt="银鳞堂 【部分预售】刺绣棉麻小脚休闲裤" />
                        </a>
                        <span class="brand">银鳞堂</span>
                        <span class="productname">【部分预售】刺绣棉麻小脚休闲裤</span>
                        <span class="price">
                            <sup>￥</sup>
                            198.00
                        </span>
                    </div>
                </li>
                <li>
                    <div class="item">
                        <a href="/?m=category&a=product&id=176" title="HARDLY EVER’S 赤耳丹宁牛仔裤">
                            <img src="http://img.25miao.com/198/1458181994.jpg" alt="HARDLY EVER’S 赤耳丹宁牛仔裤" />
                        </a>
                        <span class="brand">HARDLY EVER’S</span>
                        <span class="productname">赤耳丹宁牛仔裤</span>
                        <span class="price">
                            <sup>￥</sup>
                            198.00
                        </span>
                    </div>
                </li>
                <li>
                    <div class="item">
                        <a href="/?m=category&a=product&id=446" title="银鳞堂 庐山升龙霸短袖T恤">
                            <img src="http://img.25miao.com/492/1450079378.jpg" alt="银鳞堂 庐山升龙霸短袖T恤" />
                        </a>
                        <span class="brand">银鳞堂</span>
                        <span class="productname">庐山升龙霸短袖T恤</span>
                        <span class="price">
                            <sup>￥</sup>
                            129.00
                        </span>
                    </div>
                </li>
                <li>
                    <div class="item">
                        <a href="/?m=category&a=product&id=437" title="银鳞堂 7-11印花短袖T恤">
                            <img src="http://img.25miao.com/483/1450077514.jpg" alt="银鳞堂 7-11印花短袖T恤" />
                        </a>
                        <span class="brand">银鳞堂</span>
                        <span class="productname">7-11印花短袖T恤</span>
                        <span class="price">
                            <sup>￥</sup>
                            129.00
                        </span>
                    </div>
                </li>
                <li>
                    <div class="item">
                        <a href="/?m=category&a=product&id=420" title="银鳞堂 如日中天印花T恤">
                            <img src="http://img.25miao.com/465/1450074612.jpg" alt="银鳞堂 如日中天印花T恤" />
                        </a>
                        <span class="brand">银鳞堂</span>
                        <span class="productname">如日中天印花T恤</span>
                        <span class="price">
                            <sup>￥</sup>
                            129.00
                        </span>
                    </div>
                </li>
                <li>
                    <div class="item">
                        <a href="/?m=category&a=product&id=418" title="银鳞堂 前无古人印花T恤">
                            <img src="http://img.25miao.com/463/1450074240.jpg" alt="银鳞堂 前无古人印花T恤" />
                        </a>
                        <span class="brand">银鳞堂</span>
                        <span class="productname">前无古人印花T恤</span>
                        <span class="price">
                            <sup>￥</sup>
                            129.00
                        </span>
                    </div>
                </li>
            </ul>
            <span class="blank20"></span>

            <a href="/?m=call&go=47" target="_blank">
                <img src="http://img.25miao.com/114/1448942461.gif" alt="中国风T恤 一个设计一个故事" />
            </a>
            <span class="blank30"></span>

            <ul class="product_list_gird_3">
                <li class="first">
                    <div class="item">
                        <img src="/statics/img/new_home/accessory_1.png" alt="银鳞堂 服装配饰品" />
                    </div>
                </li>
                <li>
                    <div class="item">
                        <a href="/?m=category&cid=45" title="银鳞堂 帽子"><img src="/statics/img/new_home/accessory_2.png" alt="银鳞堂 帽子" /></a>
                        <span class="productname">帽子|HAT</span>
                    </div>
                </li>
                <li>
                    <div class="item">
                        <a href="/?m=category&cid=41" title="银鳞堂 皮带"><img src="/statics/img/new_home/accessory_3.png" alt="银鳞堂 皮带" /></a>
                        <span class="productname">皮带|BELT</span>
                    </div>
                </li>
                <li>
                    <div class="item">
                        <a href="/?m=category&cid=43" title="银鳞堂 首饰"><img src="/statics/img/new_home/accessory_4.png" alt="银鳞堂 首饰" /></a>
                        <span class="productname">首饰|JEWELRY</span>
                    </div>
                </li>
                <li>
                    <div class="item">
                        <a href="/?m=category&cid=47" title="银鳞堂 箱包"><img src="/statics/img/new_home/accessory_6.png" alt="银鳞堂 箱包" /></a>
                        <span class="productname">箱包|BAG</span>
                    </div>
                </li>
                <li>
                    <div class="item">
                        <a href="/?m=category&cid=48" title="银鳞堂 鞋"><img src="/statics/img/new_home/accessory_9.png" alt="银鳞堂 鞋" /></a>
                        <span class="productname">鞋|SHOES</span>
                    </div>
                </li>
            </ul>
            {include file="public/footer.tpl" title=footer}
        </section>
        {include file="public/nav.tpl" title=nav}
    </div>
    {include file="public/js.tpl" title=js}
    <script src="/statics/js/root.js?v={$version}"></script>
    <link rel="stylesheet" href="statics/css/jquery.bxslider.css" type="text/css" />
    <script src="statics/js/jquery.bxslider.min.js"></script>
    <script>
        $(document).ready(function(){
            $('.home_banner').bxSlider({
                auto: true,
                default: 5000,
                controls:false
            });
            proW = $('.product_banner').width();
            $('.product_banner_ul').bxSlider({
                slideWidth: proW/3,
                minSlides: 2,
                maxSlides: 3,
                slideMargin: 10,
                controls:false
            });
            brandW = $('.brand_list_ul').width();
            $('.brand_list_ul').bxSlider({
                slideWidth: brandW/4,
                minSlides: 2,
                maxSlides: 4,
                moveSlides: 1,
                slideMargin: 0,
                pager:false
            });
            $('.new_home_top .imgbtn').on('click',function(){
                var keyword = $("#keyword").val();
                if(keyword.length > 0){
                    $('.imgbtn').unbind('click');
                    window.location.href = "/?m=category&a=search&k="+escape(keyword);
                }else if(keyword.length == 0 && $('.new_home_top').hasClass('search')){
                    $('.new_home_top').removeClass('search');
                }else{
                    $('.new_home_top').addClass('search');
                }
            });
            $('.new_home_top .close').on('click',function(){
                $('.new_home_top').removeClass('search');
            });
        });
    </script>
</body>
</html>
