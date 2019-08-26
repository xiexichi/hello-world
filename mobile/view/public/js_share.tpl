<script>
    $(function(){
        $("body").css("display" , "block");
    });
    var model="{$model}";
    var category = [{section name=item loop=$category} {ldelim} "category_id":"{$category[item].category_id}", "category_name":"{$category[item].category_name}", "status":"{$category[item].status}", "childrens":[{section name=_item loop=$category[item].childrens} {ldelim} "category_id":"{$category[item].childrens[_item].category_id}", "category_name":"{$category[item].childrens[_item].category_name}", "status":"{$category[item].childrens[_item].status}"{rdelim}, {/section} ] {rdelim}, {/section} ];
</script>
{if $is_weixin}
<script src="//res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript">
var configarray = {
    "title":wxconfigarray.title?wxconfigarray.title:document.title,
    "link":wxconfigarray.link?wxconfigarray.link:window.location.href,
    "imgUrl":wxconfigarray.imgUrl?wxconfigarray.imgUrl:"http://m.25boy.cn/statics/img/logo_128x128.png",
    "desc":wxconfigarray.desc?wxconfigarray.desc:'25BOY国潮男装'
};
wx.config({
    debug: false, appId: '{$signPackage.appId}', timestamp:"{$signPackage.timestamp}", nonceStr: '{$signPackage.nonceStr}', signature: '{$signPackage.signature}', jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage', "onMenuShareQQ", "onMenuShareWeibo", "onMenuShareQZone"]
});
wx.ready(function () {
    /*获取“分享到朋友圈”按钮点击状态及自定义分享内容接口*/
    wx.onMenuShareTimeline({
        title: configarray['title'],
        link: configarray['link'],
        imgUrl: configarray['imgUrl'],
        success: function () {
            $.getJSON('/ajax/share.callback.php?id='+share_id,function(res){
                if(res['ms_code']=='success' || res['ms_code']=='repeat'){
                    var notice_btn=[];
                    if(res['ms_code']=='success'){
                        notice_btn = [{
                            'title':'我的卡券',
                            'url':'/?m=account&a=coupon'
                        }]
                    }
                    shownotice({
                        "icon":"success",
                        "title":"分享成功",
                        "remark":res['ms_msg'],
                    }, notice_btn, null);
                }
            });
        },
        cancel: function () {
            alert('你已取消分享。');
        }
    });
    /*获取“分享给朋友”按钮点击状态及自定义分享内容接口*/
    wx.onMenuShareAppMessage({
        title: configarray['title'],
        desc: configarray['desc'],
        link: configarray['link'],
        imgUrl: configarray['imgUrl'],
        type: '',
        dataUrl: ''
        /*success: function () {
            alert("分享成功，感谢你的支持！");
        },
        cancel: function () {
            alert("你已经取消分享，感谢你的支持！");
        }*/
    });
    /*获取“分享到QQ”按钮点击状态及自定义分享内容接口*/
    wx.onMenuShareQQ({
        title: configarray['title'],
        desc: configarray['desc'],
        link: configarray['link'],
        imgUrl: configarray['imgUrl']
        /*success: function () {
            alert("分享成功，感谢你的支持！");
        },
        cancel: function () {
            alert("你已经取消分享，感谢你的支持！");
        }*/
    });
    /*获取“分享到腾讯微博”按钮点击状态及自定义分享内容接口*/
    wx.onMenuShareWeibo({
        title: configarray['title'],
        desc: configarray['desc'],
        link: configarray['link'],
        imgUrl: configarray['imgUrl']
        /*success: function () {
            alert("分享成功，感谢你的支持！");
        },
        cancel: function () {
            alert("你已经取消分享，感谢你的支持！");
        }*/
    });
    /*获取“分享到QQ空间”按钮点击状态及自定义分享内容接口*/
    wx.onMenuShareQZone({
        title: configarray['title'],
        desc: configarray['desc'],
        link: configarray['link'],
        imgUrl: configarray['imgUrl']
        /*success: function () {
            alert("分享成功，感谢你的支持！");
        },
        cancel: function () {
            alert("你已经取消分享，感谢你的支持！");
        }*/
    });
});
/*wx.error(function(res){
     config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
});*/
</script>
{/if}
{literal}
<script>
    var timer_keyboard, windowInnerHeight;
    var u = navigator.userAgent, app = navigator.appVersion;
    var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Linux') > -1;
    function keyboardFix(){
        $("input").on("focus",function(){
            if(isAndroid){
                eventCheck($(this));
            }
        });
        $("input").on("blur",function(event){
            if(isAndroid){
                CheckBlur($(this));
            }
        });
    }
    function eventCheck(obj) {
        setTimeout(function () {
            windowInnerHeight = window.innerHeight;
            $("#user_pannel").css({"top":"-"+(obj.position().top-60)+"px"});
            /*console.log(obj.position().top)
            obj.position().top
            timer_keyboard = setInterval(function () { eventCheck(obj) }, 100);*/
        }, 500);
        /*if (window.innerHeight > windowInnerHeight) {
            clearInterval(timer_keyboard);
            $('#dv').html('android键盘隐藏--通过点击键盘隐藏按钮');
        }*/
    }
    function CheckBlur(obj){
        $("#user_pannel").css({"top":0 });
    }
</script>

<script>
    var showheadersearchbox = false;
    $(function(){
        $(window).scroll(function() {
            var s = $(window).scrollTop();
            if(s>50){
                $("nav#menu").addClass("shadow");
            }else{
                $("nav#menu").removeClass("shadow");
            }
        });
        $("header .searchbox button").click(function() {
            if(!showheadersearchbox){
                showheadersearchbox = true;
                $("header .searchbox .inbox span").show();
                /*$("header .searchbox .inbox").animate({"border-radius":24});*/
                $("header .searchbox .inbox input").show().animate({width:480},function(){
                    $("header .searchbox .inbox button").css("margin-right","5px");
                    $("header").addClass("shadow");
                    $("header .searchbox .inbox input").focus();
                });
            }else{
                var k = $("header .searchbox .inbox input").val();
                if(k.length<2){
                    return;
                }
                window.location.href = "/index.php?m=search&k="+escape(k);
            }
        });
        $("header .searchbox .inbox span").click(function() {
            if(showheadersearchbox){
                showheadersearchbox = false;
                $("header").removeClass("shadow");
                $("header .searchbox .inbox span").hide();
                /*$("header .searchbox .inbox").animate({"border-radius":8});*/
                $("header .searchbox .inbox input").animate({width:10},function(){
                    $("header .searchbox .inbox input").hide();
                    $("header .searchbox .inbox button").css("margin-right","0");
                    $("header .searchbox .inbox input").blur();
                });
            }
        });
    });
</script>
{/literal}
<script src="/statics/js/layer/layer.js?v={$version}"></script>
<script src="/statics/js/jquery.cookie.js?v={$version}"></script>
{if $check_user_info == 'unok'}
<script type="text/javascript">
layer.open({
    content:'为保证您的帐户正常使用，请先完善资料！',
    btn:['好的'],
    end:function(){
        window.location.href = "/?m=account&a=setting";
    }
});
</script>
{/if}
<script src="/statics/js/cart.js?v={$version}"></script>
<script src="/statics/js/user.js?v={$version}"></script>
<!-- <script>
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?80360653ae2f506a1a158ec1bc5a5f20";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script> -->