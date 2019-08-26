var app = getApp();

Page({
    data:{
        user:[]
    },

    onLoad:function(options)
    {
        var that = this;
        // 检查登录
        app.checkLogin(function(res){
            if(app.UTIL.isNull(res.sessionId) == false){
                that.setData({
                    user: res
                })
            }
        });

        // 记录来源
        app.markFromSource(options,'pages/single/redpack');
    },

   

    // 分享页面
    onShareAppMessage:function(){
        var path = '/pages/single/alanHea';
        if(this.data.user.promote_id != undefined && this.data.user.promote_id != ""){
            path = path+'?pid='+this.data.user.promote_id;
        }

        return {
            title: '羞羞的铁拳&HEA',
            desc: '电影主角艾伦和马小同款衣服',
            path: path
        }
    }

})