/**
 * Created by mosiliang on 15/8/27.
 */
$(function(){
    $('.spinner').spinner();
    $("button.increase").click(function(){
        rebuildOrderList()
    });
    $("button.decrease").click(function(){
        rebuildOrderList()
    });
    rebuildOrderList()
    $("#sconfirmbuybtn").click(function(){
        submitToPay()
    })
});


function rebuildOrderList(){
    var totalprice = 0;
    var numbertext = "";
    $(".orderlistbox li").each(function() {
        var number = Number($(this).find("input.spinner").val());
        var price = 0;
        if($(this).find("span.newprice").length>0){
            price = $(this).find("span.newprice").html()*100;
        }else{
            price = $(this).find("p.price").html()*100;
        }
        numbertext += number + ",";
        //console.log(number)
        var newprice = price*number/100;
        totalprice += newprice
        newprice = newprice.toFixed(2);
        //console.log(newprice)

        $(this).find("span.total").html("共 "+newprice)
    });
    totalprice = totalprice.toFixed(2);
    $("#pricecount").html("￥"+totalprice)

    $("#pnumber").val(numbertext.substring(0,numbertext.length-1))
}

function submitToPay(){
    // 将options传给ajaxForm
    $('#orderform').submit();
}