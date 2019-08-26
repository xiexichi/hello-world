var app = getApp();

Page({

    /**
    * 页面的初始数据
    */
    data: {
        user:[],
        datalist:{}
    },

    /**
    * 生命周期函数--监听页面加载
    */
    onLoad: function (options) {
        var that = this;

        // 检查登录
        app.checkLogin(function(res){
            if(app.UTIL.isNull(res.sessionId) == false){
                that.setData({
                    user: res
                });
            }
            that.getStores();
        });

        // 记录来源
        app.markFromSource(options,'pages/o2o/store');
    },


    // 获取广告列表
    getStores: function(){
        var that = this;
        app.API.getJSON({c:'Picshow',a:'getlist',posid:79,rowNo:20},function(res){
            wx.hideToast();
            if(res.data.code == 0){
                var datalist = res.data.rs.data;
                // 转换广告url
                for (var i=0; i<datalist.length; i++) {
                    var urlParam = app.UTIL.parseURL(datalist[i].url)
                    var newUrl = app.UTIL.newUrl(urlParam)
                    datalist[i].newUrl = newUrl;
                }
                that.setData({
                    datalist:res.data.rs.data
                })
            }
        });
    },


    /**
    * 用户点击右上角分享
    */
    onShareAppMessage: function () {
        var path = '/pages/o2o/store';
        if(this.data.user.promote_id != undefined && this.data.user.promote_id != ""){
            path = path+'?pid='+this.data.user.promote_id;
        }

        return {
            title: '25BOY线下门店列表',
            desc: '潮牌新国货，原创设计品牌。',
            path: path
        }
    }
})