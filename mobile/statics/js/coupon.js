var getting_coupon = false;
$(function(){
    $("#btn_get_coupon").click(function(){
        if(!getting_coupon){
            var pw = $("#coupon_pw").val()
            if(pw==""){
                shownotice({
                    "icon":"notice",
                    "title":"领取失败，因为：",
                    "remark":"您没有输入领取码。"
                },[])
                return;
            }
            get_coupon(pw);
        }
    })

    $('.get_lottery_code').click(function(){
        $.getJSON('/ajax/get.lottery.code.php',function(data){
            if(data['code']=='success'){
                layer.open({
                    title : '获取成功',
                    content : '获取成功，您的抽奖码是：'+data['msg'],
                    btn : ['知道了']
                });
            }else{
                layer.open({
                    content : data['msg']
                });
            }
        });
    });

})


function get_coupon(pw){
    getting_coupon = true
    $("#btn_get_coupon").html("<img src='/statics/img/loader2.gif' style='margin-top:10px' height='40' />")
    var options = [{ "url": "/ajax/get.coupon.php", "data":{pw:pw}, "type":"POST", "dataType":"json"}]
    Load(options, function(json){
        if(json.status=="success"){
            shownotice({
                "icon":"success",
                "title":"领取成功",
                "remark":"恭喜您，已成功获取代金券，请注意代金券的使用期限，切勿让机会溜走哦！"
            },[],function(){
                window.setTimeout(function(){
                    window.location.reload()
                },2000)
            })

        }else{
            var errorsummary = ""
            switch(json.status){
                case "nopw":
                    errorsummary = "您没有输入领取码"
                    break;
                case "empty":
                    errorsummary = "你输入的领取码不存在"
                    break;
                case "geted":
                    errorsummary = "你输入的领取码对应的代金券已给别人领取了"
                    break;
                case "nologin":
                    errorsummary = "没有登录，当然不能领取到任何东西"
                    break;

            }


            shownotice({
                "icon":"notice",
                "title":"领取失败，因为：",
                "remark":errorsummary
            },[])
        }
        getting_coupon = false;
        $("#btn_get_coupon").html("领取")
    },function(){})
}