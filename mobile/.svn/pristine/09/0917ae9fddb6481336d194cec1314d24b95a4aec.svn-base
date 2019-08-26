$(document).ready(function(){
    $('.home_banner').bxSlider({
        auto: true,
        default: 5000,
        controls:false
    });
    $('.share-photos').bxSlider({
        auto: true,
        default: 5000,
        controls:false
    });
    /*brandW = $('.brand_list_ul').width();
    $('.brand_list_ul').bxSlider({
        slideWidth: brandW/4,
        minSlides: 2,
        maxSlides: 4,
        moveSlides: 1,
        slideMargin: 0,
        pager:false
    });*/
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


    //初始化加载第一页数据
    getData(1);
    //==============核心代码=============
    winH = $(window).height(); //页面可视区域高度
    var scrollHandler = function () {
        var pageH = $(document.body).height()*$("body").css("zoom");
        var scrollT = $(window).scrollTop(); //滚动条top
        var aa = (pageH - winH - scrollT) / winH;
        // console.log(aa);
        if (aa < 0.1) { //0.1是就滚动到底部
            if (currentpage % 30 === 0) {//每10页做一次停顿！
                getData(currentpage);
                $(window).unbind('scroll');
            } else {
                getData(currentpage);
            }
        }
    }

    //定义鼠标滚动事件
    $(window).scroll(scrollHandler);

});


var loading = false;
var posid = 7; //广告位id
var currentpage = 1;
var pagesize = 10;

//根据页数读取数据
function getData(pagenumber) {

    if(!loading){
        currentpage++; //页码自动增加，保证下次调用时为新的一页。
        var params = {
            posid:posid,
            page: pagenumber,
            pagesize:pagesize
        }
        $(".loaddiv").show();
        loading = true
        $.ajax({
            type: "get",
            url: "/ajax/get.picshow.list.php",
            data: params,
            dataType: "json",
            success: function (data) {
                $(".loaddiv").hide();
                insertDiv(data);
                if (data.length > 0) {loading = false }else{loading = true }
            },
            error: function () {
                loading = false
                $(".loaddiv").hide();
            }
        });
    }
}


//生成数据html,append到div中
function insertDiv(json) {
    var $mainDiv = $("#hot_sale_datalist");

    loading = false
    var html = '';
    if(json.length > 0){
        for (var i = 0; i < json.length; i++) {
            //console.log(json[i].id)
            html += '<li>';
            html += '<div class="item">';
            html += '<a href="?m=category&a=product&id='+json[i].product_id+'" title="'+json[i].brand_name + json[i].product_name+'"><img src="'+json[i].product_img+'" alt="'+json[i].brand_name + json[i].product_name+'" /></a>';
            html += '<span class="brand">'+json[i].brand_name+'</span>';
            html += '<span class="productname"><a href="?m=category&a=product&id='+json[i].product_id+'">'+json[i].product_name+'</a></span>';
            html += '<span class="price">';
            if(json[i].miao_price!=""&&json[i].miao_price!=null){
                html += '<sup>￥</sup>'+json[i].miao_price+' <span class="f-c-999 f-w-n"><sup>￥</sup>'+json[i].market_price+'</span>';
            }else if(json[i].market_price>json[i].price){
                html += '<sup>￥</sup>'+json[i].price+' <span class="f-c-999 f-w-n"><sup>￥</sup>'+json[i].market_price+'</span>';
            }else{
                html += '<sup>￥</sup>'+json[i].price+'';
            }
            html +='</span>';
            html += '</div>';
            html += '</li>';
        }
    }

    var new_data = $(html).hide();
    $mainDiv.append(new_data);
    new_data.fadeIn(200);
}