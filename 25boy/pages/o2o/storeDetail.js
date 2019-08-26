var app = getApp();

Page({

    /**
     * 页面的初始数据
     */
    data: {
        user:[],
        code: '',
        title: '25BOY线下门店',
        info: {},
        markers: [],
    },

    /**
     * 生命周期函数--监听页面加载
     */
    onLoad: function (options) {
        var that = this;
        var title = options.title ? options.title : this.data.title;
        var code = options.code ? options.code : '';

        wx.setNavigationBarTitle({
            title: title
        });

        that.setData({
            code: code,
            title: title
        })

        this.getBusinessInfo();

        // 检查登录
        app.checkLogin(function(res){
            if(app.UTIL.isNull(res.sessionId) == false){
                that.setData({
                    user: res
                });
            }
        });

        // 记录来源
        app.markFromSource(options,'pages/o2o/storeDetail');
    },


    // 获取店铺信息
    getBusinessInfo: function(){
        var that = this;
        app.API.getJSON({c:'Origin',a:'getBusiness',code:this.data.code},function(res){
            wx.hideToast();
            if(res.data.code == 0){
                var markers = [{
                    iconPath: '/images/marker.png',
                    id: 0,
                    latitude: res.data.rs.latitude,
                    longitude: res.data.rs.longitude,
                    width: 73,
                    height: 39
                }];
                that.setData({
                    markers:markers,
                    info:res.data.rs
                })
            }
        });
    },


    // 打开地图
    openMap: function(){
        var info = this.data.info;
        wx.openLocation({
            latitude: parseFloat(info.latitude),
            longitude: parseFloat(info.longitude),
            scale: 36,
            name: info.title,
            address: info.business_address
        })
    },


    /**
     * 用户点击右上角分享
     */
    onShareAppMessage: function () {
        var path = '/pages/o2o/storeDetail?code='+this.data.code;
        if(this.data.user.promote_id != undefined && this.data.user.promote_id != ""){
            path = path+'?pid='+this.data.user.promote_id;
        }

        return {
            title: this.data.title,
            desc: '潮牌新国货，原创设计品牌。',
            path: path
        }
    }
})