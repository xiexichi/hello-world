$(function() {

    /* **************************************************************
     * AJAX:拖动页面加载数据
     * **************************************************************/
    if(c != 'pp') return false;
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
                category : searchCategory,
                keywords : searchKeywords,
                pagesize : gPageSize,
                page     : pagenumber,
                when     : when
            }
            currentpage++; //页码自动增加，保证下次调用时为新的一页。
            loading = true;
            $.ajax({
                type: "get",
                url: "/ajax/get.promotion.product.php",
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
        var $mainUl = $(".product_list #listBox");
        var html = '';
        for (key in json) {
            html += '<li>';
            html += '<div class="list_left"><a href="?m=category&a=product&id='+json[key].product_id+'"><img src="'+json[key].url+'!w200" /></a></div>';
            html += '<div class="list_right">';
            html += '<dl>';
            html += '<dt class="fs1_2">'+json[key].product_name+'</dt> ';
            html += '<dd class="right_dl_left">';
            html += '<p class="promotion-click">点击数:<span class="pr1">'+json[key].click_num+'</span>付款笔数:<span>'+json[key].paid_order_num+'</span></p>';
            html += '<p class="promotion-paid-price">效果预估：<span class="fs1_3">¥'+json[key].paid_order_total+'</span></p>';
            html += '<p class="promotion-received-price">预估收入：<span class="fs1_5 C_da3335 bold">¥'+json[key].received_order_total+'</span></p>';
            html += '</dd>';
            html += '<dd class="right_dl_right">';
            if(json[key].is_valid)
                html += '<button data-url="'+json[key].url+'" data-title="'+json[key].product_name+'" data-link="'+json[key].link+'" onclick="promote_now(this,'+promote_id+',0,'+json[key].product_id+');">获取链接</button>';
            else
                html += '<button class="disabled">已失效</button>';
            html += '</dd>';
            html += '</dl>';

            html += '</div>';
            html += '</li>';
        }
        $mainUl.append(html);
    }

});
