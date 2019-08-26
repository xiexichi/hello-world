/**
 * Created by mosiliang on 15/9/2.
 */
$(function() {
    $("#addressid").val($(".addresslistbox li .selected").parent().parent().attr("val"))
    lievent()
    $("#addaddress").click(function(){
        showaddresseditbox()
    })
    $(".selecttobuybtn").click(function(){

        submitToPay()
    })
})
function lievent(){
    $(".addresslistbox li").click(function(){
        if(!$(this).find(".selectbtn").hasClass("selected")){
            $(".addresslistbox li").find(".selectbtn").removeClass("selected")
            $(this).find(".selectbtn").addClass("selected")
            $("#addressid").val($(this).attr("val"))
        }
    })
}

function showaddresseditbox(){
    var _this = this
    $(".popeditbox").css("left","100%").show()
    $(".popeditbox").animate({left:0})
    $(".popeditbox .title a").click(function(){
        $(".popeditbox").animate({left:"100%"},function(){
            $(".popeditbox").hide()
        })
    })
    $(".popeditbox .btn").click(function(){
        _this.check()
    })

    this.check=function(){
        var byname = $(".popeditbox #byname").val()
        var mobile = $(".popeditbox #mobile").val()
        var city = $(".popeditbox #city").val()
        var address = $(".popeditbox #address").val()

        if(byname==""){
            alert("请填写收货人！")
            $(".popeditbox #byname").focus()
            return;
        }
        if(mobile==""){
            alert("请填写手机！")
            $(".popeditbox #mobile").focus()
            return;
        }
        if(city==""){
            alert("请填写城市！")
            $(".popeditbox #city").focus()
            return;
        }
        if(address==""){
            alert("请填写详细地址！")
            $(".popeditbox #address").focus()
            return;
        }
        _this.saveaddress()

    },
    this.saveaddress=function(){
        $(".popeditbox #byname").attr("readonly",true)
        $(".popeditbox #mobile").attr("readonly",true)
        $(".popeditbox #city").attr("readonly",true)
        $(".popeditbox #address").attr("readonly",true)

        var byname = $(".popeditbox #byname").val()
        var mobile = $(".popeditbox #mobile").val()
        var address = $(".popeditbox #city").val()+","+$(".popeditbox #address").val()

        $(".popeditbox .btn").html("正在保存...")

        showMessage(1,1,1,1);
        //deletediv();
        var options = [{ "url": "/ajax/add.address.php", "data":{byname:byname,mobile:mobile,address:address}, "type":"GET", "dataType":"json"}]
        Load(options, function(json){
            _this.success(json)
            $("#masker").remove()
        },function(){
            _this.reset()
            $("#masker").remove()
            alert("网络出错，请稍候再试")
        })
    },
    this.reset=function(){
        $(".popeditbox #byname").attr("readonly",false)
        $(".popeditbox #mobile").attr("readonly",false)
        $(".popeditbox #city").attr("readonly",false)
        $(".popeditbox #address").attr("readonly",false)
        $(".popeditbox .btn").html("保存地址")
    },
    this.success=function(json){
        if(json.status==1){
            var html = '<li val="'+json.id+'">'+
                '<div class="name">'+json.byname+
                '<span class="mobile">'+json.mobile+'</span>'+
                '<div class="address">'+json.address+'</div>'+
                '</div>'+
                '<div class="selectbox">'+
                '<span class="selectbtn selected">选定</span>'+
                '</div>'+
            '</li>'

            $(".addresslistbox li").find(".selectbtn").removeClass("selected")
            $(".addresslistbox").append(html)
            _this.reset()
            $(".popeditbox").animate({left:"100%"},function(){
                $(".popeditbox").hide()
            })
            lievent()
            $('html, body').animate({scrollTop: $(document).height()}, 300);
            $("#addressid").val($(".addresslistbox li .selected").parent().parent().attr("val"))
    }else{
            _this.reset()
            $("#masker").remove()
            alert("网络出错，请稍候再试")
        }
    }
}

function submitToPay(){
    // 将options传给ajaxForm
    if($("#addressid").val()!=0&&$("#addressid").val()!=""){
        $('#orderform').submit();
    }
}