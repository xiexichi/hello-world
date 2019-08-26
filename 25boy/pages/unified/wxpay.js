var app = getApp();

Page({

    /**
     * 页面的初始数据
     */
    data: {
        params: {},
        info: {},
        code: '',
        errorMsg: ''
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function (options) {
        const that = this;
        if(!options.params){
            wx.showToast({
                title: '参数错误',
                icon: 'none',
                mask: true,
                duration: 2000,
                complete: function(){
                    // 返回上一个小程序
                    wx.navigateBackMiniProgram();
                }
            })
        }else{
            // 如果没有绑定25boy帐户，服务器可以用code获取openid
            var params = JSON.parse(options.params);
            that.setData({
                params: params
            });
            this.getPayInfo();
        }
    },

    // 获取支付信息
    getPayInfo: function() {
        const that = this;
        wx.showToast({
          title: '加载中',
          icon: 'loading',
          duration: 10000,
          mask: true
        });
        app.API.request(this.data.params, function(json){
            wx.hideToast();
            if( json.data.code == 0 ){
                that.setData({
                    info: json.data.data
                });
            }else{
                that.setData({
                    errorMsg: json.data.msg
                })
            }
        },'wx/pay/getPayInfo');
    },

    // 发起微信支付
    payment: function() {
        const that = this;
        wx.login({
            success: function(res) {
                that.setData({
                    code: res.code
                });
                that.requestPayment();
            }
        });       
    },

    // 请求支付
    requestPayment: function(){
        var extraData = app.globalData.extraData;
        let params = this.data.params;
        params.code = this.data.code;

        wx.showToast({
          title: '处理中',
          icon: 'loading',
          duration: 10000,
          mask: true
        });
        app.API.request(params, function(json){
            wx.hideToast();
            if(json.data.code == 0){
                wx.requestPayment({
                    'timeStamp': json.data.data.timeStamp,
                    'nonceStr': json.data.data.nonceStr,
                    'package': json.data.data.package,
                    'signType': 'MD5',
                    'paySign': json.data.data.paySign,
                    'success':function(res){
                        wx.showToast({
                            title: '支付成功',
                            icon: 'success',
                            mask: true,
                            duration: 2000,
                            complete: function(){
                                // 返回上一个小程序
                                if(app.UTIL.isNull(extraData.thirdapp) == false){
                                    wx.navigateBackMiniProgram({
                                        extraData: {
                                            type: 'consume',
                                            payStatus: 'complete'
                                        }
                                    });
                                }else{
                                    wx.navigateBack();
                                }
                            }
                        })
                    },
                    'fail':function(res){
                    }
                })
            }else{
                 wx.showToast({
                    title: json.data.msg,
                    icon: 'none',
                    mask: true,
                    duration: 2000
                })
            }
        },'wx/pay/consume');

    }
})