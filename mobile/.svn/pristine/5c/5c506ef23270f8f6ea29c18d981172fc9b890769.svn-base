$(function(){
    // 首页图片热点定准
    var bodyWidth = $('body').width();
    var zoom = bodyWidth / 640;
    $('.map_div a').css('zoom',zoom);

    $(window).scroll(function() {
        var s = $(window).scrollTop();
        //console.log(s)
        if(s>0){
            $("header.page_header").addClass("shadow")
            // $("section.main").css("padding-top",80)
        }else{
            $("header.page_header").removeClass("shadow")
            // $("section.main").css("padding-top",0)
        }
    });
    

    $(document).on("click", ".top-downloadbar-close",function(){
        $('.top-downloadbar').remove();
    });

    // 弹出菜单
    $(document).on("click", ".dropdown-toggle",function(event){
        var id  = $(this).attr('id');
        $('.dropdown-backdrop').toggle();
        $('.dropdown-menu[aria-labelledby="'+id+'"]').toggle();
    });
    $(document).on("click",".dropdown-backdrop",function(){
        $('.dropdown-backdrop').hide();
        $('.dropdown-menu').hide();
    });
    // 双击返回顶部
    $(document).on("click",".pagetitle",function(){
        ScrollTo("body",60);
    });
    $('.nav__trigger').on('click', function(e){
      // e.preventDefault();
      $('body').toggleClass('nav--active');
      $('html').toggleClass('overflow-hidden');
      //阻止屏幕手动默认行为
      $(document).on('touchmove', function(e) {
          e.stopPropagation();
          e.preventDefault();
      });
    });
    $('.menulayoutmasker').on('click touchmove', function(){
      $('body').removeClass('nav--active');
      $('html').removeClass('overflow-hidden');
      //取消绑定的行为
      $(document).off('touchmove');
    });
    side_onresize();
    window.onresize = function () {
        side_onresize();
    }
    // 页面搜索层
    $(document).on("click",".site-search-btn",function(){
        $('#site_search').show().animate({'top':'0','opacity':'1'});
        return false;
    });
    $(document).on("click",".search-icon-back",function(){
        $('#site_search').animate({'top':'-100%','opacity':'0'});
        return false;
    });
    // 微信分享提示
    $(".wx_share_btn").click(function(){
        show_wx_share_div();
    });

    // 弹出关注公众号
    $('.show_QRcode').on('click',function() {
        layer.open({
            content: '<div class="show_QRcode_Box" style="width:230px;"><img src="http://img.25miao.com/115/1482826662.jpg!w390" alt="25BOY微信公众号" style="display:block"></div>',
            style  : 'background:#fff',
            shade : true,
            shadeClose :true,
            end:function(){
                $('.guide_line').hide();
            }
        });
    });

});

function side_onresize(){
    var side_height = $('.side-menu').height()-$('.side-menu .side-bottom').height()-40;
    $('.side-menu .side-menu-content').height(side_height);
}

// 喜欢 +1
function voteset(type,id){
    var options = [{ "url": "/ajax/vote.set.php", "data":{type:type,id:id}, "type":"GET", "dataType":"json"}];
    Load(options, function(res){
        if(res.status=="success"){
            $('.like_'+id).css('color','red').find('.likenum').html(res['count']);
        }else if(res.status=='voteed'){
            shownotice({
                    "icon":"notice",
                    "title":"已经投过了",
                    "remark":'感谢您宝贵的一票<br>分享到朋友圈召集人马帮拖吧！'
                },[{"title":'分享到朋友圈',"url":'javascript:show_wx_share_div()'}],null
            );
        }else if(res.status=='iplimit'){
            shownotice({
                    "icon":"notice",
                    "title":"一个IP最多5票",
                    "remark":'感谢您宝贵的一票<br>分享到朋友圈获得更多票数吧！'
                },[{"title":'分享到朋友圈',"url":'javascript:show_wx_share_div()'}],null
            );
        }else if(res.status=='phone_unvalidate') {
            shownotice({
                    "icon":"notice",
                    "title":"手机号码未验证",
                    "remark":'投票前请先验证手机号码！'
                },[{"title":'手机验证',"url":'?m=account&a=setting'}],null
            ); 
        }else if(res.status=='timeout') {
            shownotice({
                    "icon":"notice",
                    "title":"活动核算中",
                    "remark":'8月1日－24日晒图活动正在核算期，<br>票数已锁定暂时不能投票。'
                },[],null
            ); 
        }else{
            layer.open({
                content: '您还没登录网站，请登录后操作。'
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
        }
    });
}

// 分享到微信
function show_wx_share_div(imgsrc){
    $("#systemnoticebox").remove();
    if(imgsrc=='' || imgsrc==undefined){
        imgsrc = '/statics/img/guide.png';
    }
    var body = document.body;
    var div = document.createElement("div");
    div.id = "mcover";
    div.className = "mcover wx_share_box"
    div.innerHTML = '<img src="'+imgsrc+'" /><img src="/statics/img/ani_arrow.gif" class="ani" />';
    body.appendChild(div);
    $("#mcover").click(function(){
        $(this).remove();
        $.cookie('show_wx_share_div', imgsrc);
    })
}


// 取我的专用推广链接
function myPromoteQrcode(url, cb){
    var options = [{ "url": "/ajax/get.qrcode.php", "data":{act:'byPromoteUrl',url:url,type:'weapp_temp'}, "type":"GET", "dataType":"json"}];
    Load(options, function(res){
        typeof cb == "function" && cb(res)
    });
}

// 获取商品推广预览图片
function getProductPreviewCode(id, cb){
    // 检查登录
    var loadlayer = layer.open({
        type: 2,
        content: '加载中'
    });
    var options = [{ "url": "/?m=hd&a=promoteApply", data:{do:'checkLogin'}, "type":"POST", "dataType":"json"}];
    Load(options, function(json){
        if(json.status == 'success'){
            var params = {
                c : 'Qrcode',
                a : 'productDetail',
                clent : 'wap',
                id: id,
                pid : json.res.pid
            };
            var options = [{ "url": "http://api.25boy.cn/", "data":params, "type":"GET", "dataType":"json"}];
            Load(options, function(res){
                layer.close(loadlayer);
                res.pid = params.pid;
                typeof cb == "function" && cb(res);
            });
        }else if(json.status == 'nologin'){
            layer.close(loadlayer);
            layer.open({
                content: '请登录后获取您的专属返佣链接。'
                ,btn: ['登录/注册']
                ,yes: function(){
                    if(iswx){
                        var b = new Base64();
                        var gourl = b.encode(window.location.href);
                        window.location.href='/?m=login&a=weixin.bind&gourl='+gourl;
                    }else{
                        layer.closeAll();
                        user.action('by',true);
                        user.createpannel();
                    }
                }
            });
        }else{
            layer.close(loadlayer);
        }
    });
}

// 获取商品推广预览图片
function getRedpackPreviewCode(cb, pid){
    if(pid == undefined && pid == '')
    {
        // 检查登录
        var options = [{ "url": "/?m=hd&a=promoteApply", data:{do:'checkLogin'}, "type":"POST", "dataType":"json"}];
        Load(options, function(json){
            if(json.status == 'success'){
                var params = {
                    c : 'Qrcode',
                    a : 'redpackCode',
                    clent : 'wap',
                    pid : json.res.pid
                };
                var options = [{ "url": "http://api.25boy.cn/", "data":params, "type":"GET", "dataType":"json"}];
                Load(options, function(res){
                    typeof cb == "function" && cb(res)
                });
            }else if(json.status == 'nologin'){
                layer.open({
                    content: '请登录后获取您的专属返佣链接。'
                    ,btn: ['登录/注册']
                    ,yes: function(){
                        if(iswx){
                            var b = new Base64();
                            var gourl = b.encode(window.location.href);
                            window.location.href='/?m=login&a=weixin.bind&gourl='+gourl;
                        }else{
                            layer.closeAll();
                            user.action('by',true);
                            user.createpannel();
                        }
                    }
                });
            }
        });
    }else{
        // 不检查登录
        var params = {
            c : 'Qrcode',
            a : 'redpackCode',
            clent : 'wap',
            pid : pid
        };
        var options = [{ "url": "http://api.25boy.cn/", "data":params, "type":"GET", "dataType":"json"}];
        Load(options, function(res){
            typeof cb == "function" && cb(res)
        });
    }
}


// 登录事件
function layerLoginTap(){
    if(iswx){
        var b = new Base64();
        var gourl = b.encode(window.location.href);
        window.location.href='/?m=login&a=weixin.bind&gourl='+gourl;
    }else{
        layer.closeAll();
        user.action('by',true);
        user.createpannel();
    }
}