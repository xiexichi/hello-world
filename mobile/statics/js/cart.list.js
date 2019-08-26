/*load news*/
var cart_action_working = false
var loading = false;
var open_win="sort";
var winH = 0;
var reload = false

var first = false;
$(function () {
    //初始化加载数据
    getData();
});


//$(".layoutmasker").show()
//根据页数读取数据
function getData() {
    if(!loading){
        loading = true
        $.ajax({
            type: "get",
            url: "/ajax/get.cart.list.php",
            dataType: "json",
            success: function (data) {
                //console.log(data)
                loading = false
                $(".loaddiv").hide();
                if (data.status != 'nomore') {
                    if(data.status=="success"){
                        if(data.activitys_title) {
                            $(".alert-events").html(data.activitys_title).show();
                        }
                        //console.log(data.list)
                        if(data.list.length>0){
                            var jsonObj = data.list;
                            insertDiv(jsonObj);
                        }
                    }
                    if(data.status=="nologin"){
                        user.auto()
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
    var $mainDiv = $(".cartlistbox ul");
    
    var html = '';
    var presale = '';
    var words = '';
    for (var i = 0; i < json.length; i++) {
        if(json[i].stock==0 || json[i].siglequantity==0) {
            html += '<li class="ycenter important_mask" product_id="' + json[i].product_id + '" cart_id="' + json[i].cart_id + '" quantity="' + json[i].total_quantity + '" stock="' + json[i].stock + '" siglequantity="' + json[i].siglequantity + '">';
        }else{
            if(json[i].total_quantity==0){
                html += '<li class="ycenter important_mask" cart_id="' + json[i].cart_id + '" product_id="' + json[i].product_id + '" quantity="' + json[i].total_quantity + '" stock="' + json[i].stock + '" siglequantity="' + json[i].siglequantity + '">';
            }else if(json[i].total_quantity<10){
                html += '<li class="ycenter mask" cart_id="' + json[i].cart_id + '" product_id="' + json[i].product_id + '" quantity="' + json[i].total_quantity + '" stock="' + json[i].stock + '" siglequantity="' + json[i].siglequantity + '">';
            }else{
                html += '<li class="ycenter" cart_id="' + json[i].cart_id + '" product_id="' + json[i].product_id + '" quantity="' + json[i].total_quantity + '" stock="' + json[i].stock + '" siglequantity="' + json[i].siglequantity + '">';
            }
            html += '<div class="checkbox checkbox_checked"></div>';
        }
        html += '<div class="productitembox">';
        html += '<div class="imgbox">';
        html += '<a href="?m=category&a=product&id='+json[i].product_id+'"><img src="'+json[i].thumb+'" /></a>';
        html += '</div>';
        html += '<div class="detailbox">';
        html += '<div class="product_name_box">';
        html += ' <a href="?m=category&a=product&id='+json[i].product_id+'">'+json[i].product_name+'</a></div>';
        html += '<div class="prop_box">'+json[i].color_prop+'，'+json[i].size_prop+'</div>';
        if(json[i].presale=="1"){
            html += '<div class="presale_box"><font color="red">[预售,'+json[i].presale_date+'发货]</font></div>';
        }
        
        var siglequantity = '，';
        if(json[i].stock==0){
            html += '<div class="prop_box"><span class="important">产品已下架</span></div>';
        }else if(json[i].siglequantity==0){
            html += '<div class="prop_box"><span class="important">缺货中</span></div>';
        }else{
            if(json[i].siglequantity<10){
                siglequantity += '<span class="important">库存紧张（'+json[i].siglequantity+'）</span>';
            }else{
                siglequantity += '<span class="normal">库存（'+json[i].siglequantity+'）</span>';
            }
            if(json[i].miao_price > 0){
                html += '<div class="price_box" data-val="'+json[i].miao_price+'"><del><sup>￥</sup>'+json[i].re_price+'</del> <sup>￥</sup>'+json[i].miao_price+siglequantity+'</div>';
            }else{
                html += '<div class="price_box" data-val="'+json[i].re_price+'">';
                if(parseFloat(json[i].product_price) > parseFloat(json[i].re_price)){
                    html += '<del><sup>￥</sup>'+json[i].product_price+'</del>';
                }
                html += '<sup>￥</sup>'+json[i].re_price+siglequantity;
                html += '</div>';
            }
            html += '<div class="quantity_box">';
            html += '<div class="mid"></div>';
            var realquantity = Math.floor(json[i].siglequantity)<Math.floor(json[i].quantity) ? json[i].siglequantity : json[i].quantity;
            html += "<div class=\"quantity\"><input type=\"number\" maxvalue=\""+json[i].siglequantity+"\" value=\""+realquantity+"\" onkeyup=\"this.value=this.value.replace(/\D/g,'')\" id=\"start_price\" /></div>";
            html += '<div class="add"></div>';
            words = [];
            if(json[i].is_seller) {
                if(json[i].seller_discount != '' && json[i].seller_discount > 0 && json[i].seller_discount < 10)
                    words = ['专享'+json[i].seller_discount+'折'];
            }else {
                words = json[i].event;
            }
            html += '<div style="float:right;line-height:150%;padding-right:0.875em;font-size:0.875em;color:#f60;">';
            for(key in words) {
                html += '<p style="text-align:left;">'+words[key]+'</p>';             
            }
            html += '</div>';
            html += '</div>';
        }
        html += '</div>';
        html += '</div>';
        html += '</li>'
    }
    if(reload){
        reload = false
        $mainDiv.html(html);
    }else{
        $mainDiv.append(html);
    }
    eventup()

}



function eventup(){
    //console.log("11111")
    $("nav#cartbottom .selectall").unbind("click")
    $(".cartlistbox ul li .checkbox").unbind("click")
    $(".cartlistbox ul li .add").unbind("click")
    $(".cartlistbox ul li .mid").unbind("click")
    $(".cartlistbox ul li .input").unbind("blur")
    $(".cartlistbox .btn_recyclebin").unbind("click")
    $(".cartlistbox .btn_buynow").unbind("click")

    $(".cartlistbox ul li .checkbox").click(function(){

            if ($(this).hasClass("checkbox_checked")) {
                $(this).removeClass("checkbox_checked")
            } else {
                $(this).addClass("checkbox_checked")
            }
            checkselected()

    })

    $("nav#cartbottom .selectall").click(function(){
        if($(this).find("i").hasClass("selected")){
            $(".cartlistbox ul li .checkbox").removeClass("checkbox_checked")
        }else{
            $(".cartlistbox ul li .checkbox").addClass("checkbox_checked")
        }

        checkselected()
    });

    $(".cartlistbox ul li .mid").click(function(){
        var input = $(this).parent().find(".quantity input")
        var inputvalue = Math.floor(input.val())
        var lastvalue = Math.floor(input.val())-1
        if(inputvalue>1){
            var cart_id = $(this).parent().parent().parent().parent().attr("cart_id")
            input.val(lastvalue)
            setquantity(cart_id,lastvalue)
        }
    })
    $(".cartlistbox ul li .add").click(function(){

        var input = $(this).parent().find(".quantity input")
        var inputvalue = Math.floor(input.val())
        var maxvalue = Math.floor(input.attr("maxvalue"))
        var lastvalue = Math.floor(input.val())+1
        //console.log(inputvalue)
        if(inputvalue<maxvalue){
            var cart_id = $(this).parent().parent().parent().parent().attr("cart_id")
            input.val(lastvalue)
            setquantity(cart_id,lastvalue)
        }
    })
    $(".cartlistbox ul li input").blur(function(){
        var input = $(this)
        var inputvalue = Math.floor(input.val())
        var maxvalue = Math.floor(input.attr("maxvalue"))
        var cart_id = $(this).parent().parent().parent().parent().parent().attr("cart_id")
        if(maxvalue<inputvalue){
            inputvalue = maxvalue
            input.val(inputvalue)
        }
        if(!inputvalue || inputvalue==0){
            inputvalue = 1
            input.val(inputvalue)
        }
        setquantity(cart_id,inputvalue)
    })

    $("#cartbottom .btn_recyclebin").click(function(){
        if(!cart_action_working){
            cart_action("delete")
            cart_action_working = true
        }

    })
    $("#cartbottom .btn_buynow").click(function(){
        if(!cart_action_working) {
            cart_action("buy")
            cart_action_working = true
        }
    })

    checkselected()
}

function checkselected(){


    $("nav#cartbottom .btngroupbox").removeClass("movefromright")

    var checkall = true;
    var checkcount = 0;
    var total_price = 0;
    $(".cartlistbox ul li").each(function(){
        if(!$(this).find(".checkbox").hasClass("checkbox_checked")){
            checkall = false
        }else{
            checkcount ++;
            if($(this).find(".price_box").length>0){
                total_price += $(this).find("input").val()*parseFloat($(this).find(".price_box").data('val'));
            }
        }
    })
    if(checkcount!=0){
        $("nav#cartbottom .btngroupbox").removeClass("movetoright")
        $("nav#cartbottom .btngroupbox").addClass("movefromright")
        //$("nav#cartbottom .btngroupbox").removeClass("hideInright")
    }else{
        $("nav#cartbottom .btngroupbox").addClass("movetoright")
        $("nav#cartbottom .btngroupbox").removeClass("movefromright")
    }

    if(checkall){
        $("nav#cartbottom .selectall i").addClass("selected")
    }else{
        $("nav#cartbottom .selectall i").removeClass("selected")
    }
    $("header .pagetitle").html("购物车 (<strong>"+totalcart+"</strong>)")
    // $("nav#cartbottom .selectstatus").html("总价:<sup>￥</sup>"+total_price+"<br/><span>已选 "+checkcount+" (不含运费)</span>")
    $("nav#cartbottom .selectstatus").html("总价:<sup>￥</sup>"+total_price.toFixed(2)+"<br><span>不含运费</span>");
}


function cart_action(action){
    //check
    if($(".cartlistbox ul li .checkbox_checked").length<1){
        shownotice({
            "icon":"notice",
            "title":"咦！！！！",
            "remark":"好像没选择到任何东西哦"
        },[])
        return;
    }


    if(action=="delete"){
        shownotice({
            "icon":"notice",
            "title":"会反悔不？",
            "remark":"亲，您确定要删除购物车的产品吗？"
        },[{"title":"删了吧！","url":"javascript:;"}])
        $("#systemnoticebox .btn_white").unbind("click")
        $("#systemnoticebox .btn_white").click(function(){
            delete_cart()
        })

    }else{
        buy_cart()
    }
}

function delete_cart(){
    var idlist = "";
    var liid = "";
    $(".cartlistbox ul li .checkbox_checked").each(function(){
        idlist += $(this).parent().attr("cart_id")+","
        liid += $(this).parent().index()+","
    })
    if(idlist==""){
        shownotice({
            "icon":"notice",
            "title":"咦！！！！",
            "remark":"好像没选择到任何东西哦，你是要删除我吗？"
        },[])
        return;
    }else{
        idlist = idlist.substring(0,idlist.length-1)
        liid = liid.substring(0,liid.length-1)
    }

    var midnumber = idlist.split(",")
    midnumber = midnumber.length
    $("#systemnoticebox").remove()
    $("#cartbottom .btn_recyclebin").addClass("btn_recyclebin_ing")
    //return;
    var options = [{ "url": "/ajax/cart.delete.php", "data":{id:idlist,liid:liid}, "type":"get", "dataType":"json"}]
    Load(options, function(json){
        switch(json.status){
            case "success":
                totalcart -= midnumber
                shownotice({
                    "icon":"success",
                    "title":"删除成功",
                    "remark":"本窗口将在2秒后关闭"
                },[],function(){
                    // window.setTimeout(function(){
                        // window.location.reload();
                        $("#systemnoticebox").remove()
                        var lipack = json.liid.split(",")
                        $(lipack).each(function(i,obj){
                            $(".cartlistbox ul li[cart_id='"+obj+"']").slideUp(300,function(){
                                $(".cartlistbox ul li[cart_id='"+obj+"']").remove()
                                checkselected()
                            })
                        })
                        cart_action_working = false;
                        $("#cartbottom .btn_recyclebin").removeClass("btn_recyclebin_ing")
                    // },2000)
                })
                reload = true
                getData()
                break;
            case "nologin":
                shownotice({
                    "icon":"nologin",
                    "title":"删除失败",
                    "remark":"你还没登录或绑定微信号，登录绑定微信号开启免登录。"
                },[{"title":"绑定账号","url":"/?m=login&a=weixin.bind"}],null);
                user.by_btn("#systemnoticebox .btn_white","by",false);
                cart_action_working = false;
                $("#cartbottom .btn_recyclebin").removeClass("btn_recyclebin_ing")
                break;
            case "getrror":
                shownotice({
                    "icon":"posterror",
                    "title":"致命错误",
                    "remark":"传送了不符合规则的数据"
                },[],null);
                cart_action_working = false;
                $("#cartbottom .btn_recyclebin").removeClass("btn_recyclebin_ing")

                break;

        }
    },function(){
        cart_action_working = false;
        $("#cartbottom .btn_recyclebin").removeClass("btn_recyclebin_ing")
    })

    //console.log(idlist+"+"+liid)
}


function buy_cart(){
    error = "";

    $(".cartlistbox ul li .checkbox_checked").each(function(){
        var productname = $(this).parent().find(".product_name_box a").html()
        if($(this).parent().attr("stock")==0){
            error += "产品 "+productname+" 已经下架；<br/>"
        }
        if(Math.floor($(this).parent().attr("siglequantity"))<Math.floor($(this).parent().find("input").val())){
            error += "产品 "+productname+" 库存不足；"
        }
    })

    if(error!=""){
        shownotice({
            "icon":"notice",
            "title":"订单有误，错误如下",
            "remark":errorcode
        },[],null)
        return;
    }else{
        var cart_id = ""
        $(".cartlistbox ul li[stock=1] .checkbox_checked").each(function(){
            cart_id += $(this).parent().attr("cart_id")+","
        })

        cart_id = cart_id.substring(0,cart_id.length-1)
        $("#orderform input#cart_id").val(cart_id)


        $("#orderform").submit()
    }
}


function setquantity(cart_id, quantity){
    checkselected()
    var options = [{ "url": "/ajax/cart.set.php", "data":{quantity:quantity,cart_id:cart_id}, "type":"get", "dataType":"json"}]
    Load(options, function(json){
        reload = true
        getData()
    })
}