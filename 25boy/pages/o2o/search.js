var app = getApp();

Page({

    /**
    * 页面的初始数据
    */
    data: {
        business_name: '',
        stockList: [],
        hideResult: true,
        search_quantity: 'more0',
        query_params:{},
        page_rows: 30,
        total_rows: 0,
        page:1,
        activeIndex: 0,
        sliderLeft: 0
    },

    /**
    * 生命周期函数--监听页面加载
    */
    onLoad: function (options) {
        var that = this;

        // 记录来源
        app.markFromSource(options,'pages/o2o/search');
        
        // 获取商户信息
        app.getBusinessInfo(function(res){
            if(res.data.code == 0 && app.UTIL.isNull(res.data.rs.business_name) == false){
                that.setData({
                    business_name : res.data.rs.business_name
                });    
            }else{
                wx.showModal({
                    title: '提示',
                    content: '查询商户失败',
                    showCancel: false,
                    success: function(res) {
                        if (res.confirm) {
                            wx.switchTab({
                                url: '/pages/index/index'
                            })
                        }
                    }
                })
            }
        });
    },

    submitForm: function(e){
        this.resetForm();
        var params = e.detail.value;
        if(e.type == 'confirm'){
            var params = this.data.query_params;
            params.sku = e.detail.value;
        }
        params.search_quantity = this.data.search_quantity;
        params.business = app.getFromMark('ch');
        this.data.query_params = params;
        this.findStock();
    },


    /**
    * 公共查询店铺库存
    */
    findStock: function(){
        var that = this,
            page = this.data.page,
            params = this.data.query_params,
            stock_rows = this.data.stockList.rows ? this.data.stockList.rows : [];
        
        // 分页
        console.log(this.data.page_rows*page,this.data.total_rows)
        if( page != 1 && (this.data.page_rows*page) >= this.data.total_rows ){
            return false;
        }
        params.page = page;
        params.rows = this.data.page_rows;

        wx.showToast({
            title: '查询中',
            icon: 'loading',
            mask: true,
            duration: 10000
        });
        app.API.postDATA(params,function(res){
            if(res.data.code == 0){
                var data = res.data.rs.data;
                var total_rows = parseInt(that.data.total_rows) + parseInt(data.total);
                var stockList = {
                    total: total_rows,
                    rows: stock_rows.concat(data.rows)
                }
                that.setData({
                    hideResult: false,
                    stockList: stockList,
                    page: page+1,
                    total_rows: total_rows
                });
            }else{
                wx.showModal({
                    title: '提示',
                    content: res.data.msg,
                    showCancel: false
                });
            }
            setTimeout(function() {
                wx.hideToast();
            }, 1000);
        },'index.php?c=O2o&a=publicFindStock');
    },

    // 店铺库存大于0
    switchChange: function(e){
        var search_quantity = '';
        if(e.detail.value == true){
            search_quantity = 'more0';
        }
        this.data.search_quantity = search_quantity;
    },


    // 重置表单
    resetForm: function(){
        this.setData({
            page: 1,
            total_rows: 0,
            hideResult: true,
            stockList: []
        });
    },

    // 返回顶部
    gotop: function(){
        this.resetForm();
        wx.pageScrollTo({
          scrollTop: 0,
          duration: 300
        })
    },

    // 图片预览
    previewImage: function(e){
        var urls = [e.currentTarget.dataset.url];
        wx.previewImage({
            urls: urls
        });
    },

    // 分享页面
    onShareAppMessage:function(){
        var path = '/pages/o2o/search';
        var frMark = app.getFromMark();

        return {
            title: this.data.business_name + '库存查询',
            desc: '店铺库存快速查询',
            path: path + "?" + app.UTIL.parseParam(frMark)
        }
    },

    // 页面上拉触底事件的处理函数
    onReachBottom:function(){
        this.findStock()
    }

})