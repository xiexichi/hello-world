var gPageSize = 8;
var loading = false;
var currentpage = 1; //设置当前页数，全局变量
var open_win="sort";
var winH = 0;
var condition = {
    "default":1,
    "click":0,
    "sale":0,
    "price":0,
    "start_price":0,
    "end_price":0,
    "new":0,
    "brand":0,
    "brand_id":0,
}
var condition_old = {
    "default":1,
    "click":0,
    "sale":0,
    "price":0,
    "start_price":0,
    "end_price":0,
    "new":0,
    "brand":0,
    "brand_id":0,
}
var reload = false;
$(function () {
    //初始化加载第一页数据
    getData(1);
    //==============核心代码=============
    winH = $(window).height(); //页面可视区域高度
    var scrollHandler = function () {
        var pageH = $(document.body).height()*$("body").css("zoom");
        var scrollT = $(window).scrollTop(); //滚动条top
        var aa = (pageH - winH - scrollT) / winH;
        // console.log(aa)
        if (aa < 0.1) { //0.02是就滚动到底部
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


    // 排序
    $("#sorttools .sortbtn").click(function(){
        $("#sorttools .sortbtn").removeClass("sort_selected")
        $(this).addClass("sort_selected")
        var act = $(this).attr('id');
        var priceobj = $("#sorttools #price");

        switch(act){
            case 'click':
                condition.default = 0
                condition.click = 1
                condition.sale = 0
                condition.price = 0
                condition.new = 0
                // condition.brand = 0
                priceobj.html('价格<i class="iconfont">&#xe60a;</i>')
                $('.brandSet .brandsList').animate({'top':250});
                $('.priceSet').animate({'top':0});
                break;
            case 'sale':
                condition.default = 0
                condition.click = 0
                condition.sale = 1
                condition.price = 0
                condition.new = 0
                // condition.brand = 0
                priceobj.html('价格<i class="iconfont">&#xe60a;</i>')
                $('.brandSet .brandsList').animate({'top':250});
                $('.priceSet').animate({'top':0});
                break;
            case 'new':
                condition.default = 0
                condition.click = 0
                condition.sale = 0
                condition.price = 0
                condition.new = 1
                // condition.brand = 0
                priceobj.html('价格<i class="iconfont">&#xe60a;</i>')
                $('.brandSet .brandsList').animate({'top':250});
                $('.priceSet').animate({'top':0});
                break;
            case 'price':
                condition.default = 0
                condition.click = 0
                condition.sale = 0
                condition.new = 0
                // condition.brand = 0
                if(condition.price == 2){
                    condition.price = 1
                    priceobj.html('价格<i class="iconfont desc">&#xe60d;</i>')
                }else{
                    condition.price = 2
                    priceobj.html('价格<i class="iconfont asc">&#xe60f;</i>')
                    checkcondition()
                }
                $('.brandSet .brandsList').animate({'top':250});
                $('.priceSet').animate({'top':0});
                break;
            case 'brand':
                condition.default = 0
                condition.click = 0
                condition.sale = 0
                condition.price = 0
                condition.new = 0
                condition.brand = 1
                priceobj.html('价格<i class="iconfont">&#xe60a;</i>')
                $('.brandSet .brandsList').animate({'top':0});
                $('.priceSet').animate({'top':-250});
                break;
            default :
                condition.default = 1
                condition.click = 0
                condition.sale = 0
                condition.price = 0
                condition.new = 0
                // condition.brand = 0
                priceobj.html('价格<i class="iconfont">&#xe60a;</i>')
                $('.brandSet .brandsList').animate({'top':250});
                $('.priceSet').animate({'top':0});
                break;
        }
    })
    
    if(condition.brand == 1){
        $("#sorttools .sortbtn").removeClass("sort_selected");
        $("#sorttools .sortbtn[id='brand']").addClass("sort_selected");
    }
    $('.brandsList .brandBtn').on('click',function(){
        $('.brandsList .brandBtn').removeClass('selected');
        $(this).addClass('selected');
        condition.brand_id = $(this).data('id');
    })

    $("#btn_opensorttools").click(function(){
        open_win="sort"
        $(".layoutmasker").show()
        $("#btn_opensorttools").animate({bottom:-80})
        $("#btn_opencategorytools").animate({bottom:-80})
        $("#sorttools").slideDown();

        var setH = $('.brandsList').height();
        $('.brandSet').height(setH);
        if(condition.brand == 1){
            $('.brandSet .brandsList').css('top',0);
            $('.priceSet').css({'height':setH+'px','line-height':setH+'px','top':-setH*2});
        }else{
            $('.brandSet .brandsList').css('top',setH*2);
            $('.priceSet').css({'height':setH+'px','line-height':setH+'px','top':0});
        }

        $("#cancelsort").click(function(){
            $("#sorttools").slideUp();
            $("#btn_opensorttools").animate({bottom:'2em'})
            $("#btn_opencategorytools").animate({bottom:'2em'})
            $(".layoutmasker").hide()
        })
    })

    $("#btn_opencategorytools").click(function(){
        open_win="category"
        $(".layoutmasker").show()
        $("#btn_opensorttools").animate({bottom:-80})
        $("#btn_opencategorytools").animate({bottom:-80})
        $(".categorybox").show()
        $("#categorytools").slideDown();
        category_win()
    })

    $(".layoutmasker").click(function(){
        if(open_win=="category"){
            $("#categorytools").slideUp();
        }else{
            $("#sorttools").slideUp();
        }
        $("#btn_opensorttools").animate({bottom:'2em'})
        $("#btn_opencategorytools").animate({bottom:'2em'})
        $(".layoutmasker").hide()
    })

    $("#beginsort").on('click',function(){
        reload = true;
        var start_price = $("#start_price").val()
        var end_price = $("#end_price").val()
        condition.start_price = start_price
        condition.end_price = end_price

        if(end_price!=start_price){
            if(end_price==""){
                $("#end_price").focus()
                return;
            }
            if(start_price==""){
                $("#start_price").focus()
                return;
            }
            currentpage = 1;
            getData(1)
            $("#sorttools").slideUp();
            $("#btn_opensorttools").animate({bottom:'2em'})
            $("#btn_opencategorytools").animate({bottom:'2em'})
            $(".layoutmasker").hide();
        }else{
            currentpage = 1;
            getData(1)
            $("#sorttools").slideUp();
            $("#btn_opensorttools").animate({bottom:'2em'})
            $("#btn_opencategorytools").animate({bottom:'2em'})
            $(".layoutmasker").hide();
        }
    })

});

function checkcondition(){
    if(
        !$("#sorttools #click").hasClass("sort_selected")&&
        !$("#sorttools #sale").hasClass("sort_selected")&&
        !$("#sorttools #price").hasClass("sort_selected")&&
        !$("#sorttools #new").hasClass("sort_selected")&&
        !$("#sorttools #brand").hasClass("sort_selected")
    ){
        condition.default = 1
        $("#sorttools #default").addClass("sort_selected")
    }
}

function category_win(){

    $("#beginsearch").click(function(){
        var keyword = $("#keyword").val()
        if(keyword.length>0){
            window.location.href = "/?m=category&a=search&k="+escape(keyword)
        }
    })

    $("#cancelsearch").click(function(){
        $("#categorytools").slideUp();

        $("#btn_opensorttools").animate({bottom:'2em'})
        $("#btn_opencategorytools").animate({bottom:'2em'})
        $(".layoutmasker").hide()
    })
}

//$(".layoutmasker").show()
//根据页数读取数据
function getData(pagenumber) {
    //console.log(currentpage)

    if(!loading || (condition_old.default!=condition.default || condition_old.click!=condition.click || condition_old.price!=condition.price || condition_old.brand_id!=condition.brand_id)){
        currentpage++; //页码自动增加，保证下次调用时为新的一页。
        var params = {
            pagesize: gPageSize,
            page: pagenumber,
            k:k,
            default:condition.default,
            click:condition.click,
            sale:condition.sale,
            price:condition.price,
            new:condition.new,
            start_price:condition.start_price,
            end_price:condition.end_price,
            brand:condition.brand,
            brand_id:condition.brand_id
        }

        loading = true
        $.ajax({
            type: "get",
            url: "/ajax/get.product.list.php",
            data: params,
            dataType: "json",
            success: function (data) {
                $(".loaddiv").hide();
                var jsonObj = data; insertDiv(jsonObj);
                if (data.length > 0) {loading = false }else{loading = true }
                if($("#sorttools").is(":hidden")){
                    $("#btn_opensorttools").animate({bottom:'2em'})
                    $("#btn_opencategorytools").animate({bottom:'2em'})
                }
                condition_old.default = condition.default;
                condition_old.click = condition.click;
                condition_old.sale = condition.sale;
                condition_old.price = condition.price;
                condition_old.brand_id = condition.brand_id;
            },
            beforeSend: function () {
                $(".loaddiv").show();
                $("#btn_opensorttools").animate({bottom:-80})
                $("#btn_opencategorytools").animate({bottom:-80})
            },
            error: function () {
                loading = false
                if($("#sorttools").is(":hidden")){
                    $("#btn_opensorttools").animate({bottom:'2em'})
                    $("#btn_opencategorytools").animate({bottom:'2em'})
                }
                $(".loaddiv").hide();
            }
        });
    }
}

//生成数据html,append到div中
function insertDiv(json) {
    var $mainDiv = $("#productlist");

    loading = false
    var html = '';
    for (var i = 0; i < json.length; i++) {
        //console.log(json[i].id)
        html += '<div class="gird_item productitem" data-size="6">';
        html += '<div class="innerbox">';
        html += '<a href="?m=category&a=product&id='+json[i].product_id+'" class="btnitem" title="'+json[i].product_name+'">';
        html += '<img src="'+json[i].thumb+'" alt="'+json[i].brand_name+' '+json[i].product_name+'" /></span>';
        html += '</a>';
        html += '<div class="itemsummary">';
        html += '<p class="brand">'+json[i].brand_name+'</p>';
        html += '<p class="title">'+json[i].product_name+'</p>';
        if(json[i].miao_price!=""&&json[i].miao_price!=null){
            html += '<p class="price"><sup>￥</sup><span class="new">'+json[i].miao_price+'</span> <span class="old"><sup>￥</sup>'+json[i].market_price+'</span> </p>';
        }else{
            if(json[i].market_price==json[i].price){
                html += '<p class="price"><sup>￥</sup>'+json[i].price+'</p>';
            }else{
                html += '<p class="price"><sup>￥</sup><span class="new">'+json[i].price+'</span> <span class="old"><sup>￥</sup>'+json[i].market_price+'</span> </p>';
            }
        }
        html += '</div>';
        html += '</div>';
        html += '</div>';
    }

    if(reload){
        reload = false
        $mainDiv.html(html);
        ScrollTo("#productlist");
    }else{
        $mainDiv.append(html);
    }

    var mainhtml = $mainDiv.html().replace(/(^\s*)|(\s*$)/g, "");
    if(json.length == 0 && mainhtml.length==0){
        reload = false
        $mainDiv.html('<div class="empty-content"><i class="iconfont"></i></div>');
        ScrollTo("#productlist");
        return false;
    }
}