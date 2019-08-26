var app = getApp();

Page({

    /**
    * 页面的初始数据
    */
    data: {
        bag_id: '',
        user: {},
        info: {},
        questNum: 15,
        processing: true
    },

    /**
    * 生命周期函数--监听页面加载
    */
    onLoad: function (options) {
        this.setData({
            bag_id: options.bag_id||''
        })
        // 记录来源
        app.markFromSource(options,'pages/activity/rechargeComplete');
    },

    /**
    * 生命周期函数--监听页面显示
    */
    onShow: function () {
        var that = this;

        wx.showToast({
            title: '检查登录',
            icon: 'loading',
            duration: 10000
        });

        // 检查登录
        app.checkLogin(function(res){
            wx.hideToast();
            if( app.UTIL.isNull(res.sessionId) == true ){
                wx.navigateTo({
                    url:'/pages/public/login?gourl=close'
                })
            }else{
                that.getBagCoupon();
                that.setData({
                    user:res
                })
            }
        });  
    },

    // 获取充值赠送优惠券
    getBagCoupon: function(){
        var that = this;

        app.API.request({
            bag_id: this.data.bag_id,
            biz:'hotel',
            token:this.data.user.sessionId
        },function(json){
            if(json.data.code == 0){
                if(json.data.data.pay_status == 'paid'){
                    that.setData({
                        info: json.data.data,
                        processing: false
                    })
                }else{
                    // 循进查询充值结果
                    if(that.data.questNum > 0){
                        that.followQequest();
                    }else{
                        that.setData({
                            info: json.data.data,
                            processing: false
                        })
                    }
                }
            }
        },'event/user_coupon/getRechargeGiftCoupon');
    },

    // 循进查询充值结果
    followQequest: function(){
        var that = this;
        setTimeout(function() {
            that.getBagCoupon();
            that.setData({
                questNum: that.data.questNum-1
            })
        }, 1500);
    }
})