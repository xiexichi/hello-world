$(function(){
    $("header .pagesearchbox").hide();
    $(".homebtn").show();
});

/*load news*/
var cart_action_working = false
var gPageSize = 10;
var loading = false;
var currentpage = 1; //设置当前页数，全局变量
var open_win="sort";
var winH = 0;
var first = false;

$(function () {
    //初始化加载第一页数据
    getData(1);
    //==============核心代码=============
    winH = $(window).height(); //页面可视区域高度
    var scrollHandler = function () {
        var pageH = $(document.body).height()*$("body").css("zoom");
        var scrollT = $(window).scrollTop();
        var aa = (pageH - winH - scrollT) / winH;

        if (aa < 0.02&&!cart_action_working) {//0.02是个参数
            if (currentpage % 30 === 0) {//每10页做一次停顿！
                getData(currentpage);
                $(window).unbind('scroll');
                //$("#btn_Page").show();
            } else {
                getData(currentpage);
                //$("#btn_Page").hide();
            }
        }
    }
    //定义鼠标滚动事件
    $(window).scroll(scrollHandler);
    //==============核心代码=============
});


//$(".layoutmasker").show()
//根据页数读取数据
function getData(pagenumber) {
    if(!loading){
        var params = {
            pagesize: gPageSize,
            page: pagenumber,
            c:c
        }
        currentpage++; //页码自动增加，保证下次调用时为新的一页。
        loading = true
        $.ajax({
            type: "get",
            url: "/ajax/get.bag.list.php",
            data: params,
            dataType: "json",
            success: function (data) {
                if (data.length > 0) {loading = false }else{loading = true }
                $(".loaddiv").hide();
                if(data.status=="success"){
                    //console.log(data.list)
                    if(data.list.length>0){
                        var jsonObj = data.list;
                        insertDiv(jsonObj);
                    }
                }
                if(data.status=="nologin"){
                    user.auto();
                }
            },
            beforeSend: function () {
                $(".loaddiv").show();
            },
            error: function () {
                loading = false
                $(".loaddiv").hide();
            }
        });
    }
}

var salt = 'C'+Math.floor(Math.random()*99999999+1);
function insertDiv(json) {
    loading = false;
    var $mainDiv = $("ul.balance_list");
    var html = '';
    for (var i = 0; i < json.length; i++) {
        html += '<li>';
        html += '<div class="box">';
        if(json[i].method){
            html += '<p class="title">'+json[i].method+'</p>';
        }
        html += '<p class="f-c-999">'+json[i].pay_status+'</p>';
        if(json[i].pay_sn){
            html += '<p class="f-c-999">'+json[i].pay_sn+'</p>';
        }
        if(json[i].transaction_id){
            html += '<p class="f-c-999">'+json[i].transaction_id+'</p>';
        }
        html += '<p class="f-c-999">'+json[i].create_date+'</p>';
         if(json[i].note){
            html += '<p class="f-c-999">'+json[i].note+'</p>';
        }
            html += '<div class="fr">';
            if(json[i].plus_price>0){
                html += "<span>送"+json[i].plus_price+"</span>";
            }
            if(json[i].money<0){
                html += '<i class="mid"></i> ¥'+Math.abs(json[i].money);
            }else{
                html += '<i class="add"></i> ¥'+json[i].money;
            }
            if(json[i].pay_status == '未付款' && json[i].typeCode=='prepaid'){
                setCookie('salt',salt);
                if(iswx){
                    html += '<p><a class="btn btn_mini" href="/wxpay_charge.php?sn='+json[i].pay_sn+'&salt='+salt+'">立刻支付</a></p>';
                }else{
                    html += '<p><a class="btn btn_mini" href="/alipay_charge.php?sn='+json[i].pay_sn+'&salt='+salt+'">立刻支付</a></p>';
                }
            }else if(json[i].pay_status == '未付款' && json[i].typeCode=='goods'){
                html += '<p><a class="btn btn_mini btn_paynow" href="/?m=account&a=order&s=all&k='+json[i].pay_sn+'">查看订单</a></p>';
            }
            html += '</div>';
        html += '</div>';
        html += '</li>';
    }
    //if(reload){
        //reload = false
        //$mainDiv.html(html);
    //}else{
        $mainDiv.append(html);
    //}
}

