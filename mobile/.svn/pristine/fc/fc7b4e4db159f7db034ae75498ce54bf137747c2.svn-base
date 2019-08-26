$(function(){ 
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
            }
            currentpage++; //页码自动增加，保证下次调用时为新的一页。
            loading = true;
            $.ajax({
                type: "get",
                url: "/ajax/get.promote.product.php",
                data: params,
                dataType: "json",
                success: function (data) {
                    // console.log(data);return false;
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
        var earnings = 0;
        for (key in json) {
            html += '<li>';
            html += '<div class="list_left"><a href="/?m=category&a=product&id='+json[key].product_id+'"><img src="'+json[key].url+'!w200" /></a></div>';
            html += '<div class="list_right">';
            html += '<dl>';
            html += '<dt class="fs1_2">'+json[key].product_name+'</dt>'; 
            html += '<dd class="right_dl_left">';
            html += '<p class="sku-sn">编号：'+json[key].sku_sn+'</p>';
            html += '<p class="price"><span class="pr1 fs1_2">¥'+json[key].price+'</span>提成:<span class="">'+json[key].commission_rate+'%</span></p>';
            earnings = json[key].price * json[key].commission_rate / 100;
            html += '<p class="two">赚: ¥<span class="fs1_2">'+earnings.toFixed(2)+'</span></p>';
            html += '</dd>';
            html += '<dd class="right_dl_right"><a href="/?m=category&a=product&id='+json[key].product_id+'">前往推广</a></dd>';
            html += '</dl></div>';
            html += '</li>';
        }
        $mainUl.append(html);
    }
});
