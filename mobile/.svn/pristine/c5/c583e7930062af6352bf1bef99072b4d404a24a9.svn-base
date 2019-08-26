/**
 * Created by mosiliang on 15/8/21.
 */
$(function(){
    InitCM();
    //console.log($.cookie('cart'))
});

function InitCM(){

    $(".cartbottom #deleteselect").hide();
    $(".cartbottom #selecttobuybtn").hide();
    $(".cartbottom #selecttobuybtn").click(function(){
        submitToPay()
    })
    $(".cartbottom .selectall").click(function(){
        if($(this).find("i").hasClass("selected")){
            $(this).find("i").removeClass("selected");
            clear();
        }else{
            $(this).find("i").addClass("selected");
            all();
        }
    });

    $(".cartbottom #deleteselect").click(function(){
        if($(this).val()==0){
            deleteitem();
        }
    });
    //$(".cartlistbox li")
    $(".cartlistbox li a.select").click(function(){
        if($(this).hasClass("selected")){
            $(this).removeClass("selected");
        }else{
            $(this).addClass("selected");
        }
        reset();
    })
}


function reset(){
    var pidlist = "";
    var selectitem = 0;
    $(".cartlistbox li").each(function(i,obj){
        var id=$(this).attr("val");
        if(!$(this).find("a.select").hasClass("selected")){
            pidlist += id+",";
        }
        if($(this).find("a.select").hasClass("selected")){
            selectitem ++;
        }
    });



    if(selectitem==$(".cartlistbox li").length){
        $(".cartbottom .selectall i").addClass("selected");
        pidlist = "";
        $(".cartlistbox li").each(function(i,obj){
            var id=$(this).attr("val");
            pidlist = "all";
        });
    }else{
        $(".cartbottom .selectall i").removeClass("selected");
    }
    $(".cartbottom #selectcount").html("已选 "+selectitem);
    if(selectitem>0){
        $(".cartbottom #deleteselect").show();
        $(".cartbottom #selecttobuybtn").show();
    }else{
        $(".cartbottom #deleteselect").hide();
        $(".cartbottom #selecttobuybtn").hide();
    }
    $("#pidlist").val(pidlist);
    console.log(pidlist);

}

function clear(){
    $(".cartlistbox li").each(function(i,obj){
       $(this).find("a.select").removeClass("selected");
    })
    reset()
}
function all(){
    $(".cartlistbox li").each(function(i,obj){
        $(this).find("a.select").removeClass("selected").addClass("selected");
    })
    reset()
}


function deleteitem(){
    //console.log("1111111111111")
    showMessage(1,1,1,1);
    deletediv();
    var options = [{ "url": "/ajax/delete.cart.php", "data":{id:$("#pidlist").val()}, "type":"GET", "dataType":"text"}]
    Load(options, function(){
        $("#masker").remove()
    },function(){
        $("#masker").remove()
    })
}


function deletediv(){
    $(".cartlistbox li").each(function(i,obj){

        if($(this).find("a.select").hasClass("selected")){
            $(this).remove();
        }
    });
}

function submitToPay(){
    // 将options传给ajaxForm
    var pidlist = "";
    $(".cartlistbox li").each(function(i,obj){
        var id=$(this).attr("val");
        pidlist += id+",";
    });
    $("#pidlist").val(pidlist);

    $('#cartform').submit();
}