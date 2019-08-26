$(function(){
    $("header .pagesearchbox").hide()
    $(".homebtn").show()
})


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

        var scrollT = $(window).scrollTop(); //滚动条top
        var aa = (pageH - winH - scrollT) / winH;
        //console.log(winH + scrollT)
        //console.log(pageH)
        //console.log(aa)

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
            page: pagenumber
        }
        currentpage++; //页码自动增加，保证下次调用时为新的一页。
        loading = true
        $.ajax({
            type: "get",
            url: "/ajax/get.integral.list.php",
            data: params,
            dataType: "json",
            success: function (data) {
                //console.log(data)
                loading = false
                $(".loaddiv").hide();
                //if (data.length > 0) {

                    if(data.status=="success"){
                        //console.log(data.list)
                        if(data.list.length>0){
                            var jsonObj = data.list;

                            insertDiv(jsonObj);
                        }


                    }
                    if(data.status=="nologin"){
                        user.auto()
                    }

                //}
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

function insertDiv(json) {



    loading = false
    var $mainDiv = $("ul.balance_list");
    var html = '';
    for (var i = 0; i < json.length; i++) {

        //console.log(json[i].id)

        html += '<li>';
            html += '<div class="fl">';
                html += '<p>'+json[i].context+'</p>';
                html += '<p class="f-s-18 f-c-999">'+json[i].create_date+'</p>';
            html += '</div>';
            html += '<div class="fr f-s-24">';

                html += '<i class="add"></i> '+json[i].integral_value+'</div>';


        html += '</li>'


    }
    //if(reload){
        //reload = false
        //$mainDiv.html(html);
    //}else{
        $mainDiv.append(html);
    //}


}

