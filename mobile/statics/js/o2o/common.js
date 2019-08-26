
/**
 * [showLoading 显示loading1]
 * @return {[type]} [description]
 */
function showLoading(){
    $("body").append("<div style='width:100%; height:100%; background:rgba(0,0,0,0.7); position:fixed; top:0; left:0; z-index:999999; text-align: center; display: -webkit-box; -webkit-box-pack:center; display: box; -webkit-box-pack:center; -webkit-box-align:center;' id='loading-div'><img src='/statics/img/loader3.gif' /></div>")
}

/**
 * [closeLoading 删除loading]
 * @return {[type]} [description]
 */
function closeLoading(){
	$('#loading-div').remove();
}


// 错误提示
function tips(tip){
	layer.open({
        content: tip,
        skin: 'msg',
        time: 2 //2秒后自动关闭
    });
}

// 错误提示
function layerTips(tip){
	layer.open({
        content: tip,
        skin: 'msg',
        time: 2 //2秒后自动关闭
    });
}

// 确认信息
function layerInfo($content, callback){
	layer.open({
        content: $content,
        btn:['知道了'],
        end:callback
    });
}
