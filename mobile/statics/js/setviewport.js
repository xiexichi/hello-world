$(function(){
    var viewport = document.querySelector("meta[name=viewport]");
    var winWidths=$(window).width();
    var bodyWidths=$("body").width();
    var densityDpi = 720 / winWidths * window.devicePixelRatio * 160;
    var ios = browser.versions.ios
    //alert(ios);

    if(!ios){
        var scale = winWidths/720

        if(winWidths<540){
            //alert("1")
            viewport.setAttribute('content', 'initial-scale=1,user-scalable=no');
        }else{
            //alert("2")

            viewport.setAttribute('content', 'width=720,target-densityDpi='+densityDpi);
        }


    }else{
        viewport.setAttribute('content', 'width=720,user-scalable=no');
    }
    
    
})
var browser={
    versions:function(){
        var u = navigator.userAgent, app = navigator.appVersion;
        return { 
             trident: u.indexOf('Trident') > -1,
            presto: u.indexOf('Presto') > -1,
            webKit: u.indexOf('AppleWebKit') > -1,
            gecko: u.indexOf('Gecko') > -1 && u.indexOf('KHTML') == -1,
            mobile: !!u.match(/AppleWebKit.*Mobile.*/),
            ios: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/),
            android: u.indexOf('Android') > -1 || u.indexOf('Linux') > -1,
            iPhone: u.indexOf('iPhone') > -1 ,
            iPad: u.indexOf('iPad') > -1,
            webApp: u.indexOf('Safari') == -1
        };
    }(),
     language:(navigator.browserLanguage || navigator.language).toLowerCase()
}