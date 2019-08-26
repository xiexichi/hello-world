/* Ajax 异步请求 */
function Load(options, sCall, eCall) {
    var url = options[0].url || "";
    var type = options[0].type || "GET";
    var contentType = options[0].contentType || "application/x-www-form-urlencoded";
    var dataType = options[0].dataType || "text";
    var data = options[0].data || "none";
    var async = options[0].async || true;
    var cache = options[0].cache || true;
    var ifModified = options[0].ifModified || true;
    var request = $.ajax({
        type: type,
        cache: cache,
        url: url,
        data: data,
        contentType: contentType,
        dataType: dataType,
        ifModified: ifModified,
        statusCode: {
            404: function() { eCall("404"); },
            503: function() { eCall("503"); }
        }
    })
    request.done(function (data) {
        sCall(data)
    })
}
//#endregion
function keyPress() {
    var keyCode = event.keyCode;
    if ((keyCode >= 48 && keyCode <= 57))
    {
        event.returnValue = true;
    } else {
        event.returnValue = false;
    }
}

function showMessage(type,html,autoclose,time,callback){
    $("body").append("<div style='width:100%; height:100%; background:rgba(0,0,0,0.7); position:fixed; top:0; left:0; z-index:999999; text-align: center; display: -webkit-box; -webkit-box-pack:center; display: box; -webkit-box-pack:center; -webkit-box-align:center;' id='masker'><img src='/statics/img/loading.gif' /></div>")
}

function loadingbox(){
    return '<div class="loading-msg">'+
        '<span>正在链接服务器进行查询，请稍后</span>'+
        '<div class="loading-box">'+
        '<div class="loading" index="0"></div>'+
        '<div class="loading" index="1"></div>'+
        '<div class="loading" index="2"></div>'+
        '<div class="loading" index="3"></div>'+
        '<div class="loading" index="4"></div>'+
        '</div>'+
        '</div>'
}

function ScrollTo(id,dis){
    var target = $(id);
    target.get(0) ? $('body').animate({ scrollTop: target.offset().top*$("body").css("zoom")-dis },"slow") : null;
    return false;
}
function getAstro(m,d){
    return "魔羯水瓶双鱼白羊金牛双子巨蟹狮子处女天秤天蝎射手魔羯".substr(m*2-(d<"102223444433".charAt(m-1)- -19)*2,2);
}
function showerror(message,status,btn,reload,url,callback){

    var img = "/statics/img/icon.";
    switch (status){
        case "warning":
            img+="status.warning"
            break;
        case "error":
            img+="status.error"
            break;
        case "quantity":
            img+="status.quantity"
            break;
        case "undercarriage":
            img+="status.undercarriage"
            break;
        case "noproduct":
            img+="status.noproduct"
            break;
    }
    img+=".png";

    var html ='<section id="systemstatusbox" class="systemstatusbox">\
        <div class="box">\
        <div class="imgbox"><img src="/statics/img/'+img+'" width="50%" /></div>\
        <div class="titlebox">'+message.title+'</div>\
        <div class="remarkbox">'+message.remark+'</div>\
        <div class="btnbox">'
    if(btn.length>0){
        $(btn).each(function(i,obj){
            html += '<a href="'+obj.url+'" class="btn btn_white btn_mini">'+obj.title+'</a>&nbsp;'
        })
    }

    html += '<a class="btn btn_white btn_mini popwinbtnclose">关闭</a>\
        </div>\
        </div>\
    </section>';

    $("body").append(html)

    if(url!=""){
        setTimeout(function(){
            window.location.href = url
        },2000)
    }

    $(".popwinbtnclose").click(function(){
        if(reload){
            window.location.reload()
        }else{
            $("#systemstatusbox").remove()
            if (typeof(eval(callback)) == "function") {
                callback()
            }
        }
    })
}

function shownotice(message,btn,callback,close_btn){
    if(message.icon==""){
        img = "icon.status.notice"
    }else{
        img = message.icon
    }
    var html ='<section id="systemnoticebox" class="systemstatusbox">\
        <div class="box">\
        <div class="imgbox"><img src="/statics/img/icon.status.'+img+'.png" width="25%" /></div>\
        <div class="titlebox">'+message.title+'</div>\
        <div class="remarkbox">'+message.remark+'</div>\
        <div class="btnbox">';

    if(btn.length>0){
        $(btn).each(function(i,obj){
            html += '<a href="'+obj.url+'" class="btn btn_white btn_mini">'+obj.title+'</a>&nbsp;';
        })
    }
    if(close_btn!='hide'){
        html += '<a class="btn btn_secondary btn_mini popwinbtnclose">关闭</a>';
    }
    html += '</div>\
        </div>\
    </section>';

    $("body").append(html)

    $(".popwinbtnclose").click(function(){
        $("#systemnoticebox").remove()
        if (typeof(eval(callback)) == "function") {
            callback()
        }
    })
    if (typeof(eval(callback)) == "function") {
        callback()
    }
}

function showmask(){
    var html = "<div "
    $("body").append("")
}

function setCookie(c_name,value,expiredays) {
    var exdate=new Date()
    exdate.setDate(exdate.getDate()+expiredays)
    document.cookie=c_name+ "=" +escape(value)+
    ((expiredays==null) ? "" : ";expires="+exdate.toGMTString())
}