/*load news*/
var gPageSize = 5;
var loading = false;
var currentpage = 2; //设置当前页数，全局变量
var winH = 0;

$(function () {
    winH = $(window).height(); //页面可视区域高度
    var scrollHandler = function () {
        var pageH = $(document.body).height()*$("body").css("zoom");

        var scrollT = $(window).scrollTop(); //滚动条top
        var aa = (pageH - winH - scrollT) / winH;
        if (aa < 0.1) {//0.02是个参数
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

});


//$(".layoutmasker").show()
//根据页数读取数据
function getData(pagenumber) {
    if(!loading){
        var params = {
            id:catid,
            tag:tag,
            pagesize: gPageSize,
            page: pagenumber,
        }
        currentpage++; //页码自动增加，保证下次调用时为新的一页。
        loading = true
        $.ajax({
            type: "get",
            url: "/ajax/get.trends.list.php",
            data: params,
            dataType: "json",
            success: function (data) {
                $(".loaddiv").hide();
                if (data.status != 'nomore') {
                    if(data.status=="success"){
                        //console.log(data.list)
                        if(data.list.length>0){
                            var jsonObj = data.list;
                            insertDiv(jsonObj);
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

function insertDiv(json) {

    loading = false
    var $mainDiv = $(".info-list .listBox");
    var html = '';
    for (var i = 0; i < json.length; i++) {
        //console.log(json[i].id)
        html += '<li>';
        html += '<div class="img_box"><a href="/?m=trends&a=view&id='+json[i].article_id+'" title="'+json[i].title+'"><img src="'+json[i].img_url+'" alt="'+json[i].article_id+'" /></a></div>';
        html += '<div class="txt">';
        html += '<p class="title"><a href="/?m=trends&a=view&id='+json[i].article_id+'" title="'+json[i].article_id+'">'+json[i].title+'</a></p>';
        html += '<p class="intro">'+json[i].desc+'</p>';
        html += '<p class="info"><span class="fl"><i class="iconfont">&#xe600;</i>'+json[i].date_added+'</span> <span class="fr"><i class="iconfont">&#xe603;</i>'+json[i].click+'</span></p>';
        html += '</div>';
        html += '<div class="blank10"></div>';
        html += '<div class="blank5"></div>';
        html += '</li>';
    }
    $mainDiv.append(html);
}