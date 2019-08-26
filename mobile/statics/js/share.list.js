$(function(){ 
   /* var $container = $('#listBox');
    $container.imagesLoaded( function() {
        $container.masonry({
            itemSelector: 'li',
            gutter: 0,
            isFitWidth:false,
            isAnimated: true,
            columnWidth: '.grid-sizer',
        });
    });*/

    /*load news*/
    var gPageSize = 10;
    var loading = false;
    var currentpage = 1; //设置当前页数，全局变量
    var winH = 0;

    $(function () {
        getData(currentpage);
        winH = $(window).height(); //页面可视区域高度
        var scrollHandler = function () {
            var pageH = $(document.body).height()*$("body").css("zoom");

            var scrollT = $(window).scrollTop(); //滚动条top
            var aa = (pageH - winH - scrollT) / winH;
            if (aa < 0.1) {
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
                tag:tag,
                pagesize: gPageSize,
                page: pagenumber,
                sort: sort
            }
            currentpage++; //页码自动增加，保证下次调用时为新的一页。
            loading = true
            $.ajax({
                type: "get",
                url: "/ajax/get.share.list.php",
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
        var $mainDiv = $(".share-list ul");
        var html = '';
        for (var i = 0; i < json.length; i++) {
            //console.log(json[i].id)
            html += '<li>';
            html += '<div class="item">';
            html += '<a href="/?m=share&a=view&id='+json[i].share_id+'" class="img_box">';
            html += '<div class="flex share-img-border"><img src="'+json[i].img_url+'!w640" alt="'+json[i].content+'" /></div>';
            html += '</a>';
            html += '<p class="txt">';
            html += '<span class="view"><i class="iconfont">&#xe603;</i>'+json[i].click+' <span class="like pr"><i class="iconfont">&#xe60c;</i><b>'+json[i].share_comment_count+'</b>&nbsp;<span class="like_'+json[i].share_id+'" onclick="voteset(\'share\','+json[i].share_id+')" style="display:inline;"><i class="iconfont">&#xe602;</i><b class="likenum">'+json[i].vote+'</b></span></span></span>';
            html += '<span class="name"><img class="avatar" src="'+json[i].userimg+'" alt="'+json[i].username+'" />'+json[i].username+'</span>';
            html += '<span class="title">'+json[i].content+'</span>';
            html += '</p>';
            html += '</div>';
            html += '</li>';

            console.info(json[i].share_comment_count)
        }
        $mainDiv.append(html);
        /*if(html != ''){
            var $boxes = $(html);
            $container.append($boxes).masonry('appended',$boxes);
            $container.imagesLoaded().progress( function() {
              $container.masonry('layout');
            });
        }*/
    }
});
