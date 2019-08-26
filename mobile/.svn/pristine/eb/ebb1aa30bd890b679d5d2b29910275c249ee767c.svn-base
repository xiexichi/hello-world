var winH = 0;
var loadingcontent = false;
var btnaction = 0;
var working=false;

if(iswx){
    login_btn = '绑定账号';
    login_remark = '你还没登录或绑定微信号<br>登录绑定微信号享会员专有福利！';
    login_url = '/?m=login&a=weixin.bind';
}else{
    login_btn = '马上登录';
    login_remark = '你还没登录<br>登录绑定微信号享会员专有福利！';
    login_url = 'javascript:;';

}

$(function(){
    $('.swipebox').swipebox({
        //initialIndexOnArray : 2, // which image index to init when a array is passed
        hideCloseButtonOnMobile : true
    });
    $('.bxslider').bxSlider();
    eventup()
});
function eventup(){

    $(".btn_follow").click(function(){
        var product_id = $(this).attr("rel")

        var favorite = $(this).hasClass("btn_followed") ? 0 : 1;

        var options = [{ "url": "/ajax/set.product.favorite.php", "data":{id:product_id,favorite:favorite}, "type":"GET", "dataType":"text"}]
        Load(options, function(text){
            if(text=="nologin"){
                shownotice(
                    {
                        "icon":"nologin",
                        "title":"添加失败",
                        "remark":login_remark
                    },
                    [{"title":login_btn,"url":login_url}],null
                )
                user.by_btn("#systemnoticebox .btn_white","by",false);
            }else if(text==0){
                $(".btn_follow").removeClass("btn_followed")
            }else{
                $(".btn_follow").addClass("btn_followed")
            }
        },function(){})
    });

    $(".btn_share").click(function(){
        var body = document.body;
        var div = document.createElement("div");
        div.id = "mcover";
        div.className = "mcover"
        div.innerHTML = '<img src="statics/img/guide.png" /><img src="statics/img/ani_arrow.gif" class="ani" />';
        body.appendChild(div);
        $("#mcover").click(function(){
            $(this).remove()
        })
    })
    $(".moredetail").click(function(){
        if(!loadingcontent){

            loadingcontent = true
            $(".moredetail").html("正在加载数据<br/><img src=\"statics/img/loader.gif\" width=\"40\" />")
            var options = [{ "url": "/ajax/get.product.detail.php", "data":{id:pid}, "type":"GET", "dataType":"text"}]
            Load(options, function(text){
                //console.log(text)

                $(".productcontent .content").html(text)
                $(".productcontent").show()
                $(".productcontent").find("img").css("width","100%")
                $(".moredetail").hide()
                ScrollTo(".productcontent",10)

            },function(){})
        }
    })
    $(".btn_addcart").click(function(){
        if($("input#color").val() && $("input#size").val()){
            $("input#quantity").val(1)
            check(1);
        }else{
            if(btnaction!=1){
                if(!working) {
                    hideprop()
                    getrealproduct(1);
                    btnaction = 1;
                }
            }
        }
    });
    $(".btn_buynow").click(function(){
        if($("input#color").val() && $("input#size").val()){
            $("input#quantity").val(1)
            check(2);
        }else{
            if(btnaction!=2){
                if(!working){
                    hideprop()
                    getrealproduct(2);
                    btnaction = 2;
                }

            }
        }
    });
    //$(".btn_buygo").click(function(){
    //    var pid = $("#product_id").val()
    //    if(pid!=0){
    //        var options = [{ "url": "/ajax/check.product.quantity.html", "data":{id:pid}, "type":"GET", "dataType":"json"}]
    //        Load(options, function(json){
    //
    //        },function(){})
    //    }
    //});

    $(".layoutmasker").click(function(){
        if(!working) {
            hideprop()
        }
    })

    // 选择颜色尺码
    $('.productparams .coloritem').click(function(){
        $(".productparams .coloritem").removeClass("selected")
        $(this).addClass("selected")
        $("input#color").val($(this).attr("rel"))
    })
    $('.productparams .sizeitem').click(function(){
        $(".productparams .sizeitem").removeClass("selected")
        $(this).addClass("selected")
        $("input#size").val($(this).attr("rel"))
    })
}
function getrealproduct(action){
    working = true
    if(action==1){
        $(".btn_addcart").html("<img src='/statics/img/loader2.gif' style='position: relative;top: 10px;' />")
    }else{
        $(".btn_buynow").html("<img src='/statics/img/loader2.gif' style='position: relative;top: 10px;' />")
    }
    var pid = $("#product_id").val()
    if(pid!=0){
        var options = [{ "url": "/ajax/check.product.quantity.php", "data":{id:pid}, "type":"GET", "dataType":"json"}]
        Load(options, function(json){
            $(".btn_addcart").html("加入购物车")
            $(".btn_buynow").html("立即购买")
            working = false
            switch (json.status){
                case "noid":
                    showerror(
                        {title:"不存在的产品ID",remark:"产品可能已经删除，系统将跳转到产品列表页！"},
                        "quantity",
                        [{title:"查看其它产品",url:"?m=category"}],
                        false,
                        "?m=category",
                        hideprop
                    )
                    break;
                case "noproduct":
                    showerror(
                        {title:"不存在的产品",remark:"产品可能已经删除，系统将跳转到产品列表页！"},
                        "quantity",
                        [{title:"查看其它产品",url:"?m=category"}],
                        false,
                        "?m=category",
                        hideprop
                    )
                    break;
                case "success":
                    showproductprop(action,json)
                    break;
                case "undercarriage":
                    showerror(
                        {title:"产品下架了",remark:"该产品在不久前下架了，我们对此感到抱歉，我们将尽快补货！"},
                        "quantity",
                        [{title:"查看其它产品",url:"?m=category"}],
                        false,
                        "",
                        hideprop
                    )
                    break;
                case "quantity":
                    showerror(
                        {title:"产品没有库存了",remark:"产品库存不足，我们对此感到抱歉，我们将尽快补货！"},
                        "quantity",
                        [{title:"查看其它产品",url:"?m=category"}],
                        false,
                        "",
                        hideprop
                    )
                    break;
            }
        },function(){})


    }
}
function showproductprop(action,json){

    if(action==1){
        $(".btn_addcart").addClass("btn_selected")
    }
    if(action==2){
        $(".btn_buynow").addClass("btn_selected")
    }
    $(".layoutmasker").show()

    //console.log(json);
    var htmlcolor = "";
    $(json.bycolor).each(function(i,obj){
        htmlcolor += '<div class="propbtn coloritem" quantity="'+obj.quantity+'" rel="'+obj.colorname+'"><img src="'+obj.colorimg+'!w200" /><br/>'+obj.colorname+'</div>'
    });
    var htmlsize = "";
    $(json.bysize).each(function(i,obj){
        htmlsize += '<div class="propbtn" quantity="'+obj.quantity+'" rel="'+obj.sizename+'">'+obj.sizename+'</div>'
    });


    $(".productpropbox #colorbox .content").html(htmlcolor)
    $(".productpropbox #sizebox .content").html(htmlsize)
    $("#quantitybox input").attr("maxvalue",1)
    $("#quantitybox input").val("1")
    $(".productpropbox").slideDown();

    propevent(action,json)
}

function propevent(action,json){
    $(".productpropbox #colorbox .content .propbtn").click(function(){
        $(".productpropbox #colorbox .content .propbtn").removeClass("selected")
        $(this).addClass("selected")
        $("input#color").val($(this).attr("rel"))
        var sizename=""
        var selectsize = false;
        $(".productpropbox #sizebox .content .propbtn").each(function(){
            if($(this).hasClass("selected")){
                selectsize = true
                sizename=$(this).attr("rel")
            }
        });
        var quantity = 0
        if(selectsize){
            $(json.bycolor[$(this).index()].size).each(function(i,obj){
                if(obj.sizename==sizename){
                    quantity = obj.quantity
                }
            })
        }else{
            quantity = json.bycolor[$(this).index()].quantity;

        }
        $("#quantitybox input").attr("maxvalue",quantity);
        $("#quantitybox .title").html("请输入购买数量（ 库存 <strong>"+quantity+"</strong> ）");
        if($("#quantitybox input").val()>quantity){
            $("#quantitybox input").val(quantity)
        }

    })

    $(".productpropbox #sizebox .content .propbtn").click(function(){
        $(".productpropbox #sizebox .content .propbtn").removeClass("selected")
        $(this).addClass("selected")
        $("input#size").val($(this).attr("rel"))
        var sizename=$(this).attr("rel")
        var selectcolor = false;
        var colorindex = 0;
        $(".productpropbox #colorbox .content .propbtn").each(function(){
            if($(this).hasClass("selected")){
                selectcolor = true
                colorname=$(this).attr("rel")
                colorindex = $(this).index()
            }
        })
        var quantity = 0
        if(selectcolor){
            $(json.bycolor[colorindex].size).each(function(i,obj){
                if(obj.sizename==sizename){
                    quantity = obj.quantity
                }
            })
        }else{
            quantity = json.bysize[$(this).index()].quantity;

        }
        $("#quantitybox input").attr("maxvalue",quantity);
        $("#quantitybox .title").html("请输入购买数量（ 库存 <strong>"+quantity+"</strong> ）");
        if($("#quantitybox input").val()>quantity){
            $("#quantitybox input").val(quantity)
        }

    })

    $("#quantitybox .mid").click(function(){
        var input = $("#quantitybox input")
        var inputvalue = Math.floor(input.val())
        var lastvalue = Math.floor(input.val())-1
        if(inputvalue>1){
            $("#quantitybox input").val(lastvalue)
        }
    })
    $("#quantitybox .add").click(function(){
        var input = $("#quantitybox input")
        var inputvalue = Math.floor(input.val())
        var maxvalue = Math.floor(input.attr("maxvalue"))
        var lastvalue = Math.floor(input.val())+1

        //console.log(inputvalue)
        if(inputvalue<maxvalue){
            input.val(lastvalue)
        }
    })
    $("#quantitybox input").blur(function(){
        var input = $("#quantitybox input")
        var inputvalue = Math.floor(input.val())
        var maxvalue = Math.floor(input.attr("maxvalue"))
        if(maxvalue<inputvalue){
            input.val(maxvalue)
        }
    })

    $(".productpropbox #begin").click(function(){
        if(!working){
            check(action)
        }

    })
    $(".productpropbox #cancel").click(function(){
        if(!working){
            hideprop()
        }
    })

}


function check(action){
    var product_id = $("#orderform #product_id").val()
    var color = $("#orderform #color").val()
    var size = $("#orderform #size").val()
    $("#orderform #quantity").val($("#quantitybox input").val())

    var quantity = $("#orderform #quantity").val()
    var errorcode="";
    if(product_id==""||product_id==0){
        errorcode += "产品不存在；<br/>"
    }
    if(color==""||color==0){
        errorcode += "产品颜色没有选择;<br/>"
    }
    if(size==""||size==0){
        errorcode += "产品尺码没有选择;<br/>"
    }
    if(quantity==""||quantity==0){
        errorcode += "产品购买数量没有填写;<br/>"
    }
    if(errorcode!=""){
        shownotice({
            "icon":"notice",
            "title":"订单有误，错误如下",
            "remark":errorcode
        },[],null)
        return;
    }

    if(action==1){
        $(".btn_addcart").html("<img src='/statics/img/loader2.gif' style='position: relative;top: 10px;' />")
        addCart();
    }else{
        $(".btn_buynow").html("<img src='/statics/img/loader2.gif' style='position: relative;top: 10px;' />")
        buynow()
    }
}

function addCart(){

    var product_id = $("#orderform #product_id").val()
    var color = $("#orderform #color").val()
    var size = $("#orderform #size").val()
    var quantity = $("#orderform #quantity").val()

    $(".productpropbox #innerbox").hide();
    $(".productpropbox .loadingbox").show();
    working = true;
    var options = [{
        "url": "/ajax/cart.add.php",
        "type":"POST",
        data:{product_id:product_id,color:color,size:size,quantity:quantity,quick_buy:0},
        "dataType":"json"}
    ];
    Load(options, function(json){
        $(".btn_addcart").html("加入购物车")
        working = false;
        if(json.status=="success"){
            shownotice(
                {
                    "icon":"addcart",
                    "title":"添加成功",
                    "remark":"产品已经成功添加到购物车，你可以继续挑选产品一并结算。"
                },
                [{"title":"去购物车结算","url":"?m=cart"}],null
            )

            $("#orderform #color").val("")
            $("#orderform #size").val("")
            $("#orderform #quantity").val("")
            hideprop()
            InitCart()
        }

        if(json.status=="nologin"){
            shownotice(
                {
                    "icon":"nologin",
                    "title":"添加失败",
                    "remark":login_remark
                },
                [{"title":login_btn,"url":login_url}],null
            )
            user.by_btn("#systemnoticebox .btn_white","by",false);
            $(".productpropbox #innerbox").show();
            $(".productpropbox .loadingbox").hide();
            //user.userby("#systemnoticebox .btn_white");

        }
        if(json.status=="posterror"){
            shownotice(
                {
                    "icon":"posterror",
                    "title":"致命错误",
                    "remark":"传送了不符合规则的数据。"
                },
                [],null
            )
            $(".productpropbox #innerbox").show();
            $(".productpropbox .loadingbox").hide();
        }
    },function(){})
}


function buynow(){
    var product_id = $("#orderform #product_id").val()
    var color = $("#orderform #color").val()
    var size = $("#orderform #size").val()
    var quantity = $("#orderform #quantity").val()

    $(".productpropbox #innerbox").hide();
    $(".productpropbox .loadingbox").show();
    working = true;
    var options = [{
        "url": "/ajax/cart.add.php",
        "type":"POST",
        data:{product_id:product_id,color:color,size:size,quantity:quantity,quick_buy:1},
        "dataType":"json"}
    ];
    Load(options, function(json){
        $(".btn_buynow").html("立即购买")
        working = false;
        if(json.status=="success" && json.quick_buy=="1"){
            //console.log(json.last_insert_id)
            $("#qucikorderform input#cart_id").val(json.last_insert_id);
            $("#qucikorderform").submit()
        }

        if(json.status=="nologin"){
            shownotice(
                {
                    "icon":"nologin",
                    "title":"添加失败",
                    "remark":login_remark
                },
                [{"title":login_btn,"url":login_url}],null
            )
            user.by_btn("#systemnoticebox .btn_white","by",false);
            $(".productpropbox #innerbox").show();
            $(".productpropbox .loadingbox").hide();
            //user.userby("#systemnoticebox .btn_white");

        }
        if(json.status=="posterror"){
            shownotice(
                {
                    "icon":"posterror",
                    "title":"致命错误",
                    "remark":"传送了不符合规则的数据。"
                },
                [],null
            )
            $(".productpropbox #innerbox").show();
            $(".productpropbox .loadingbox").hide();
        }
    },function(){})
}

function hideprop(){
    btnaction = 0;
    $(".productpropbox #innerbox").show();
    $(".productpropbox .loadingbox").hide();
    $("#quantitybox .add").unbind("click")
    $("#quantitybox .mid").unbind("click")
    $(".productpropbox #begin").unbind("click")
    $(".productpropbox #cancel").unbind("click")
    //$(".btn_addcart").html("加入购物车")
    //$(".btn_buynow").html("立即购买")
    $(".btn_addcart").removeClass("btn_selected")
    $(".btn_buynow").removeClass("btn_selected")
    $(".productpropbox").slideUp();

    $(".layoutmasker").hide()
}
