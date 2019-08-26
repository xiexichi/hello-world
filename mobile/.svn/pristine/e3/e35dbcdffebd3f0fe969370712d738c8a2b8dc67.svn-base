$(function(){
    // 取消弹出分类，改成页面显示 
    /*$("nav .btn1").click(function(){
        if(!$(this).hasClass("btncategory")){
            $(this).addClass("btncategory")
            $("nav .categorybox").show();
            $(".layoutmasker").show()
            if(!$("nav").hasClass("shadow")){
                $("nav").addClass("shadow")
            }
        }else{
            $(this).removeClass("btncategory")
            $("nav .categorybox").hide()
            $(".layoutmasker").hide()
            $("nav").removeClass("shadow")
        }
    });*/

    $('.layoutmasker').on('click touchmove', function(){
        $("nav .categorybox").hide()
        $("nav .btn1").removeClass("btncategory")
        $(".layoutmasker").hide()
        $("nav").removeClass("shadow")
    })

    $(".categorybox .category_root li").click(function(){
        if(!$(this).find("a").hasClass("current")){
            var index = Math.floor($(this).find("a").attr("index"));
            $(".categorybox .category_root li a").removeClass("current");
            $(this).find("a").addClass("current");
            var html = "";
            //console.log(index)
            $(category[index].childrens).each(function(i,obj){
                if(obj.status==1){
                    html+='<li><a href="?m=category&cid='+obj.category_id+'">'+obj.category_name+'</a></li>';
                }
            });
            $(".category_sub").html(html);

        }

    })

})