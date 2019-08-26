$(function() {

    /* **************************************************************
     * AJAX:拖动页面加载数据
     * **************************************************************/
    /*load news*/
    var gPageSize = 10;
    var loading = false;
    var currentpage = 2; //设置当前页数，全局变量
    var winH = 0;

    $(function () {
        winH = $(window).height(); //页面可视区域高度
        var scrollHandler = function () {
            var pageH = $(document.body).height()*$("body").css("zoom");
            var scrollT = $(window).scrollTop(); //滚动条top
            var aa = (pageH - winH - scrollT) / winH;
            if (aa < 0.15) {
                getData(currentpage);
                //$("#btn_Page").hide();
            }
        }
        //定义鼠标滚动事件
        $(window).scroll(scrollHandler);

    });


    //$(".layoutmasker").show()
    //根据页数读取数据
    function getData(pagenumber) {
        if(!loading){
            var params = {
                pagesize : gPageSize,
                page     : pagenumber,
            }
            currentpage++; //页码自动增加，保证下次调用时为新的一页。
            loading = true;
            $.ajax({
                type: "get",
                url: "/ajax/get.earnings.detail.php",
                data: params,
                dataType: "json",
                success: function (data) {
                    $(".loaddiv").hide();
                    if (data.status != 'nomore') {
                        if(data.status=="success"){
                            if(data.listLength>0){
                                insertDiv(data);
                            }
                        }else{
                            loading = true
                        }
                    }else{
                        loading = true
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



    function insertDiv(jsonData) {
        loading = false;
        var json = jsonData.list;
        var promote_id = jsonData.promote_id;
        var $mainUl = $(".earnings_detail .balance_list");
        var html = '';
        for (key in json) {
            html += '<li>'
            html += '<p class="earnings_detail_header">'
            html += '<span class="earnings_detail_type">'
            if(json[key].earnings_type == 're_shopping')
                html += '购物返佣'
            else
                html += '充值返佣'
            html += '</span>'
            html += '<span class="right earnings_detail_price">¥'+json[key].earnings+'</span>'
            html += '</p>'
            html += '<p class="earnings_detail_body"> 项目： '
            if(json[key].earnings_type == 're_shopping') {
                html += json[key].product_name
            } else {
                if(json[key].method == 'alipay') {
                    html += '支付宝充值'
                }
            }
            html += '</p>'
            html += '<p class="earnings_detail_body">金额：¥'+json[key].re_price+'&nbsp;&nbsp;佣金比率：'+json[key].commission_rate+'%</p>'
            html += '<p class="earnings_detail_body">收益时间：'+json[key].received_time+' <span class="right">'
            if(json[key].is_get)
               html += '已结算' 
            else
               html += '未结算' 
            html += '</span></p>'
            html += '</li>'
        }
        $mainUl.append(html);
    }

});
