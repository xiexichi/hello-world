var app = getApp();

Page({
    data:{
        user:[],
        tipHide:true
    },


    onLoad:function(options)
    {
        var that = this;

        // loading
        wx.showToast({
            title: '正在领取',
            icon: 'loading',
            duration: 10000,
            mask:true
        })

        // 检查登录
        app.checkLogin(function(res){
            if(res.sessionId == undefined || res.sessionId == ''){
                wx.hideToast();
                wx.showModal({
                    title: '未登录',
                    content: '您还没登录帐户，请登录后领取。',
                    confirmText: '马上登录',
                    showCancel: false,
                    success: function(res) {
                        if (res.confirm) {
                            wx.redirectTo({
                                url:'/pages/public/login?gourl='+escape('/pages/single/getRedpack')
                            })
                        }
                    }
                })
            }else{
                that.setData({
                    user: res
                });

                app.API.postDATA({sessionId:that.data.user.sessionId},function(res){
                    wx.hideToast();
                    if(res.data.code == 0){
                        that.setData({
                            tipHide:false
                        })
                    }else{
                        wx.showModal({
                            title: '领取失败',
                            content: res.data.msg,
                            showCancel: false,
                            success: function(res) {
                                if (res.confirm) {
                                    wx.navigateBack();
                                }
                            }
                        })
                    }
                },'index.php?c=Recharge&a=redpack');
            }
        });
    },


    confirmTip:function(){
        this.setData({
            tipHide:true
        })
        wx.redirectTo({
            url:'/pages/users/wallet?type=prepaid'
        })
    },
    hideTip:function(){
        this.setData({
            tipHide:true
        })
        wx.navigateBack();
    }


})