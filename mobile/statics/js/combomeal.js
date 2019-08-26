
var winH = 0;
var loadingcontent = false;
var btnaction = 0;
var working=false;

// 选中数组
var chooseArr = {};
// 选择属性商品id
var chooseProductId = 0;

// 是否微信
if(iswx){
    login_btn = '绑定账号';
    login_remark = '你还没登录或绑定微信号<br>登录绑定微信号可享购物免运费！';
    login_url = '/?m=login&a=weixin.bind';
}else{
    login_btn = '马上登录';
    login_remark = '你还没登录<br>登录绑定微信号可享购物免运费！';
    login_url = 'javascript:;';

}

// 初始化
$(function(){
    $('.swipebox').swipebox({
        hideCloseButtonOnMobile : true
    });
    $('.bxslider').bxSlider({controls:false});

    

    $('.btn_group').slideDown();
    $('.event_item i.more').on('click',function(){
        $(this).parent('.event_item').find('.events_more').slideToggle();
    });
    /*$('.btn_group').slideUp();
    var options = [{ "url": "/ajax/check.product.quantity.php", "data":{id:pid}, "type":"GET", "dataType":"json"}]
    Load(options, function(json){
    });*/

    // 关闭商品属性选择
	$('.close-choose-btn').on('click',function(){
		$('#choose-attr').hide();
	})

    // 选择尺寸
    $('.sizeitem').on('click',function(event){
        // 阻止事件冒泡
        event.stopPropagation();

        // 设置样式
    	$(this).parent().find('.sizeitem').removeClass('selected');
    	$(this).addClass('selected');

        // 设置尺码库存数量
        // 商品id
        var productid = $(this).attr('productid');
        // 设置选择尺码为显示数量
        $('.size_quantity[productid="'+productid+'"]').html($(this).attr('quantity'));

        // 设置选中尺码
        $('.size[productid="'+productid+'"]').html($(this).html());  

        // 保存选中数据
        if (!chooseArr[productid]) {
            chooseArr[productid] = {};
        }
        chooseArr[productid].size = $(this).html();

        if (!chooseArr[productid]['color']) {
            // 隐藏颜色和尺码
            $('.size[productid="'+productid+'"]').hide()
            $('.color[productid="'+productid+'"]').hide()
            // 设置选中label
            $('.choose-label[productid="'+productid+'"]').html('请选择颜色')
        } else {
            // 设置全部选中
            $('.choose-label[productid="'+productid+'"]').html('已选择: ')
            $('.size[productid="'+productid+'"]').show()
            $('.color[productid="'+productid+'"]').show()
        }

        // 验证选择
        // checkChoose();
    })

    // 选择颜色
    $('.coloritem').on('click',function(event){
        // 阻止事件冒泡
        event.stopPropagation();
        // 设置样式
    	$(this).parent().find('.coloritem').removeClass('selected');
    	$(this).addClass('selected');

        // 颜色尺码库存
        var propid = $(this).attr('propid');

        // 商品id
        var productid = $(this).attr('productid');

        // 隐藏
        $('.prop-size[productid="'+productid+'"]').hide();

        // 显示
        $('.prop-size[propid="'+propid+'"]').show();

        // 设置选中颜色
        $('.color[productid="'+productid+'"]').html($(this).html());

        // 更换选中颜色图片
        $('img[productid="'+productid+'"]').attr('src',$(this).attr('src'))

        // 保存选中数据
        if (!chooseArr[productid]) {
            chooseArr[productid] = {};
        }
        chooseArr[productid].color = $(this).html();

        if (!chooseArr[productid]['size']) {
            // 隐藏颜色和尺码
            $('.size[productid="'+productid+'"]').hide()
            $('.color[productid="'+productid+'"]').hide()
            // 设置选中label
            $('.choose-label[productid="'+productid+'"]').html('请选择尺寸')
        } else {
            // 获取尺码
            var sizeitem = $('.prop-size[propid="'+propid+'"]').find('.sizeitem');
            var hasSize = false;
            for (var i = 0; i < sizeitem.length; i++) {
                if (chooseArr[productid]['size'] == $(sizeitem[i]).html()) {
                    hasSize = true;
                    // 清除其他兄弟元素样式
                    $('.sizeitem[productid="'+productid+'"]').removeClass('selected');
                    // 设置选中样式
                    $(sizeitem[i]).addClass('selected');
                    break;
                }
            }

            if (hasSize) {
                // 设置全部选中
                $('.choose-label[productid="'+productid+'"]').html('已选择: ');
                $('.size[productid="'+productid+'"]').show();
                $('.color[productid="'+productid+'"]').show();
            } else {
                // 隐藏颜色和尺码
                $('.size[productid="'+productid+'"]').hide();
                $('.color[productid="'+productid+'"]').hide();
                // 设置选中label
                $('.choose-label[productid="'+productid+'"]').html('请选择尺寸');
            }
        }
        
        
    })

    // 打开选择颜色、尺寸div
    $('.select-cs').on('click',function(){
        // 商品id
        var productid = $(this).attr('productid');

        chooseProductId = productid;

    	// 选择面板显示
        $('#choose-attr').show();
        // 显示确认按钮
        $('#sure-choose-btn').show();

        // 隐藏所有商品选择属性
        $('.product-attr').hide();
        // 选择商品显示
        $('.product-attr[productid="'+productid+'"]').show();

        // 设置选中label
        // $('.choose-label[productid="'+productid+'"]').html('已选择: ');
    })


    // 关闭提示信息
    $('#choose-attr').on('click', function(){
        // 如果提示信息显示则隐藏
        if($('#tips').css('display') == 'block'){
            $('#tips').hide();
            $(this).hide();
        }
    })

    // 选择商品属性确认按钮点击事件
    $('#sure-choose-btn').on('click',function(event){
        // 阻止事件冒泡
        event.stopPropagation();

        if(!chooseArr[chooseProductId]){
            showTips('请选择颜色和尺码');
            return;
        } else {
            if(!chooseArr[chooseProductId].color){
                showTips('请选择颜色');
                return;
            }
            if(!chooseArr[chooseProductId].size){
                showTips('请选择尺码');
                return;
            }
        }
        // 关闭选择面板
        $('#choose-attr').hide();
    })

    // 立即购买
    $('#botton-buy-btn').on('click',function(){
        // combomeal-form
        if(checkChoose()){
            // 通过验证
            // 设置表单数据
            $('input[name="products"]').val(JSON.stringify(chooseArr));

            console.log(123)
            console.log(chooseArr)
            console.log(JSON.stringify(chooseArr))
            console.log($('input[name="products"]').val())

            // 提交数据
            $('#combomeal-form').submit();
        }
    })

});


// 显示单个商品选择提示
function showTips(tip){


    $('#tips').empty();
    $('#tips').append($('<p style="text-align:center;font-size:1rem;">'+tip+'</p>'));
    $('#tips').show();
    $('#tip-bg').show();
    // 两秒后隐藏
    setTimeout(function(){
        $('#tips').hide();
        $('#tip-bg').hide();
    },2000);
}

// 检查选中商品属性
function checkChoose(){
    var checkChooses = $('.check-choose');

    // 选择提示
    var chooseTips = [];
    for (var i = 0; i < checkChooses.length; i++) {
        var productid = $(checkChooses[i]).attr('productid');
        var productName = $('.product-name[productid="'+productid+'"]').html();
        
        if (!chooseArr[productid]) {
            chooseTips.push(productName+'的颜色和尺码');
        } else {
            
            if (!chooseArr[productid].size) {
                chooseTips.push(productName+'的尺码');
            }

            if (!chooseArr[productid].color) {
                chooseTips.push(productName+'的颜色');
            }
        }
    }
    // 添加提示
    $('#tips').empty();
    $('#tips').append($('<p style="padding:0.1rem 0;">请选择:</p>'));
    for (var i = 0; i < chooseTips.length; i++) {
        $('#tips').append($('<p style="padding:0.1rem 0;">'+chooseTips[i]+'</p>'));
    }

    // 添加一个算了
    //$('#tips').append($('<p>'+chooseTips[0]+'</p>'));

    // 显示提示信息
    if (chooseTips.length > 0) {
        // 隐藏所有商品属性选择面板
        $('.product-attr').hide();
        // 隐藏底部确认按钮
        $('#sure-choose-btn').hide();

        $('#choose-attr').show();
        $('#tips').show();
    }

    // 返回验证结果
    if (chooseTips.length > 0) {
        return false;
    }

    return true;
}


// 活动项目
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
                if(iswx){return false;}
                user.by_btn("#systemnoticebox .btn_white","by",false);
            }else if(text==0){
                $(".btn_follow").removeClass("btn_followed")
            }else{
                $(".btn_follow").addClass("btn_followed")
            }
        },function(){})
    });

    $(".moredetail").click(function(){
        if(!loadingcontent){

            loadingcontent = true
            $(".moredetail").html("正在加载数据<br/><img src=\"statics/img/loader.gif\" width=\"40\" />")
            var options = [{ "url": "/ajax/get.product.detail.php", "data":{id:pid}, "type":"GET", "dataType":"text"}]
            Load(options, function(text){
                //console.log(text)

                $(".productcontent .content").html(text)
                $(".productcontent").show()
                $(".moredetail").hide()
                ScrollTo(".productcontent",50)

            },function(){})
        }
    })
    $(".btn_addcart").click(function(){
        if(!working) {
            getrealproduct(1);
            btnaction = 1;
        }
    });
    $(".btn_buynow").click(function(){
        if(!working){
            getrealproduct(2);
            btnaction = 2;
        }
    });
}
function getrealproduct(action){
    working = true
    if(pid!=0){
        check(action);
    }
}
// 选择颜色
$(document).on("click", ".propcolor .icolor",function(){
    var coloritem = $(this).find('.coloritem');
    var color_val = coloritem.data('name');
    // 已选或禁用
    if(coloritem.hasClass('selected') || coloritem.hasClass('dis')){
        return false;
    }
    // 改变样式
    $('.require').css('background-color','#FFF');
    $(".propcolor .coloritem").removeClass("selected");
    $(".propsize .sizeitem").removeClass("dis");
    coloritem.addClass("selected");
    // 改变参数
    var size_props = stock_json[color_val]['size'];
    size_prop_html(size_props);
});
// 选择尺码
$(document).on("click", ".propsize .sizeitem",function(){
    var sizeitem = $(this);
    var size_val = sizeitem.data('name');
    var num = sizeitem.data('num');
    var sync = sizeitem.data('sync');
    var quantity_input = $("#quantitybox input");
    var color_val = $('.propcolor .coloritem.selected').data("name");
    if(color_val==null || color_val==undefined){
        color_val = '';
    }
    // 已选或禁用
    if(sizeitem.hasClass("dis") || sizeitem.hasClass('selected')){
        return false;
    }
    // 改变样式
    $('.require').css('background-color','#FFF');
    $(".propsize .sizeitem").removeClass("selected");
    quantity_input.attr('maxvalue',num);
    sizeitem.addClass("selected");
    // 改变参数
    $("input#size").val(sizeitem.attr("rel"))
    $("#in_stock").html(num);
    if(sync==0){
        $('#presale_time').html(color_val+' '+size_val+'码为预售商品，下单后将于'+presale_date+'发货，请知晓！').show();
    }else{
        $('#presale_time').empty().hide();
    }
    if(quantity_input.val()>num){
        quantity_input.val(num);
    }else if(quantity_input.val()==0){
        quantity_input.val(1);
    }
});
// 修改size html
function size_prop_html(data){
  var size_html = '';
  $.each(data,function(j,s){
      size_html += '<span data-name="'+s['sku']+'" data-num="'+s['num']+'" data-sync="'+s['sync']+'"';
      if(s['num']==0){
        size_html += 'class="sizeitem dis"';
      }else{
        size_html += 'class="sizeitem"';
      }
      size_html += '>';
      if(s['sync']==0){
        size_html += s['sku']+'[预售]';
      }else{
        size_html += s['sku'];
      }
      size_html += '</span>';
  });
  $('.propsize .content').html(size_html);
}
// 数量-
$("#quantitybox .mid").click(function(){
    var input = $("#quantitybox input")
    var inputvalue = Math.floor(input.val())
    var lastvalue = Math.floor(input.val())-1
    if(inputvalue>1){
        $("#quantitybox input").val(lastvalue)
    }
});
// 数量+
$("#quantitybox .add").click(function(){
    var input = $("#quantitybox input")
    var inputvalue = Math.floor(input.val())
    var maxvalue = Math.floor(input.attr("maxvalue"))
    var lastvalue = Math.floor(input.val())+1
    if(inputvalue<maxvalue){
        input.val(lastvalue)
    }
});
$("#quantitybox input").blur(function(){
    var input = $("#quantitybox input")
    var inputvalue = Math.floor(input.val())
    var maxvalue = Math.floor(input.attr("maxvalue"))
    if(maxvalue<inputvalue){
        input.val(maxvalue)
    }
});
// 检查提交
function check(action){
    var color_val = $('.propcolor .coloritem.selected').data('name'),
        size_val = $('.propsize .sizeitem.selected').data('name'),
        quantity_val = $("#quantitybox input").val(),
        product_id = $("#orderform #product_id").val();
    $("#orderform #quantity").val(quantity_val);
    $("#orderform #color").val(color_val);
    $("#orderform #size").val(size_val);

    var quantity = $("#orderform #quantity").val(),
        color = $("#orderform #color").val(),
        size = $("#orderform #size").val();

    var errorcode="";
    if(product_id==""||product_id==0){
        errorcode += "产品不存在<br/>"
    }
    if(color==""||color==0){
        errorcode += "颜色没有选择<br/>"
    }
    if(size==""||size==0){
        errorcode += "尺码没有选择<br/>"
    }
    if(quantity==""||quantity==0){
        errorcode += "购买数量没有填写<br/>"
    }
    // 未选尺码提示
    if(errorcode!=""){
        ScrollTo(".productparams",80);
        layer.open({content:errorcode});
        working = false;
        return false;
    }
    var loader_img = "<img src='/statics/img/loader2.gif' style='position: relative;top: 10px;' />";
    if(action==1){
        $(".btn_addcart").html(loader_img)
        addCart(product_id,color,size,quantity);
    }else if(action==2){
        $(".btn_buynow").html(loader_img)
        buynow(product_id,color,size,quantity)
    }
}
// 添加购物车
function addCart(product_id,color,size,quantity){
    if(!product_id || !color || !size || !quantity){
        layer.open({content:'请选择颜色尺码。'});
        working = false;
        return false;
    }
    working = true;
    var options = [{
        "url": "/ajax/cart.add.php",
        "type":"POST",
        data:{product_id:product_id,color:color,size:size,quantity:quantity,sku_sn:sku_sn,quick_buy:0}, "dataType":"json"}
    ];
    Load(options, function(json){
        $(".btn_addcart").html("加入购物车")
        working = false;
        if(json.status=="success"){
            shownotice({
                    "icon":"addcart",
                    "title":"添加成功",
                    "remark":"产品已经成功添加到购物车，你可以继续挑选产品一并结算。"
                }, [{"title":"去购物车结算","url":"?m=cart"}],null);
            $("#orderform #color").val("");
            $("#orderform #size").val("");
            $("#orderform #quantity").val("");
            InitCart();

        }else if(json.status=="nologin"){
            shownotice({
                    "icon":"nologin",
                    "title":"添加失败",
                    "remark":login_remark
                },
                [{"title":login_btn,"url":login_url}],null);
            if(iswx){return false;}
            user.by_btn("#systemnoticebox .btn_white","by",false);
            $(".productpropbox #innerbox").show();
            $(".productpropbox .loadingbox").hide();

        }else if(json.status=="posterror"){
            shownotice({
                    "icon":"posterror",
                    "title":"致命错误",
                    "remark":"传送了不符合规则的数据。"
                }, [],null);
            $(".productpropbox #innerbox").show();
            $(".productpropbox .loadingbox").hide();

        }else if(json.status=='nostock'){
            shownotice({
                    "icon":"empty",
                    "title":"库存不足",
                    "remark":"您下手迟了一步，已经卖光了！"
                }, [],null);
            $(".productpropbox #innerbox").show();
            $(".productpropbox .loadingbox").hide();
        }
    },function(){});
}
// 立即购买
function buynow(product_id,color,size,quantity){
    if(!product_id || !color || !size || !quantity){
        layer.open({content:'请选择颜色尺码。'});
        working = false;
        return false;
    }

    $(".productpropbox #innerbox").hide();
    $(".productpropbox .loadingbox").show();
    working = true;
    var options = [{
        "url": "/ajax/cart.add.php",
        "type":"POST",
        data:{product_id:product_id,color:color,size:size,quantity:quantity,sku_sn:sku_sn,quick_buy:1}, "dataType":"json"}
    ];
    Load(options, function(json){
        $(".btn_buynow").html("立即购买");
        working = false;
        if(json.status=="success" && json.quick_buy=="1"){
            //console.log(json.last_insert_id)
            $("#qucikorderform input#cart_id").val(json.last_insert_id);
            $("#qucikorderform").submit();
            
        }else if(json.status=="nologin"){
            shownotice({
                    "icon":"nologin",
                    "title":"添加失败",
                    "remark":login_remark
                },
                [{"title":login_btn,"url":login_url}],null);
            if(iswx){return false;}
            user.by_btn("#systemnoticebox .btn_white","by",false);
            $(".productpropbox #innerbox").show();
            $(".productpropbox .loadingbox").hide();

        }else if(json.status=="posterror"){
            shownotice({
                    "icon":"posterror",
                    "title":"致命错误",
                    "remark":"传送了不符合规则的数据。"
                }, [],null);
            $(".productpropbox #innerbox").show();
            $(".productpropbox .loadingbox").hide();

        }else if(json.status=='nostock'){
            shownotice({
                    "icon":"empty",
                    "title":"库存不足",
                    "remark":"您下手迟了一步，已经卖光了！"
                }, [],null);
            $(".productpropbox #innerbox").show();
            $(".productpropbox .loadingbox").hide();
        }
    },function(){});
}