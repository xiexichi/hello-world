var app = getApp();

Page({

    /**
    * 页面的初始数据
    */
    data: {
        business_name: '',
        stockList: [],
        hideResult: true,
        // 选择器
        categorys: [],
        multiCategory: [],
        multiIndex: [0,0],
        // query
        category_id: 0,
        query_params:{},
        page_rows: 30,
        total_rows: 0,
        page:1,
        // navtab
        activeIndex: 1,
        sliderLeft: '375rpx'
    },

    /**
    * 生命周期函数--监听页面加载
    */
    onLoad: function (options) {
        var that = this;

        // 记录来源
        app.markFromSource(options,'pages/o2o/findItemGroup');

        // 读取缓存
        var cacheData = wx.getStorageSync('categorysDataCache')
        var cacheDataTimeOut = wx.getStorageSync('homeDataCacheTimeOut')
        var nowTime = new Date().getTime()
        if (cacheDataTimeOut > nowTime && cacheData) {
            this.arrCategorys(cacheData)
        }else{
            this.getCategories()
        }

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


    // 整理分类数据给选择器使用
    arrCategorys: function(categorys){
        var multiCategory = [];
        var subCategory = [];
        // 默认显示的第一栏
        for(var i in categorys){
            multiCategory.push(categorys[i].category_name);
        }
        // 默认显示的第二栏
        for(var i in categorys[0].subCategory){
            subCategory.push(categorys[0].subCategory[i].category_name);
        }
        this.setData({
            categorys: categorys,
            category_id: categorys[0].subCategory[0].category_id,
            multiCategory: [multiCategory,subCategory]
        })
    },

    // 改变第二栏数据
    bindMultiPickerColumnChange: function (e) {
        var data = {
            multiCategory: this.data.multiCategory,
            multiIndex: this.data.multiIndex
        };
        data.multiIndex[e.detail.column] = e.detail.value;

        // 改变第二栏数据
        if( e.detail.column == 0 ){
            var subCategory = this.data.categorys[e.detail.value].subCategory;
            var newSub = [];
            for(var i in subCategory){
                newSub.push(subCategory[i].category_name);
            }
            data.multiCategory[1] = newSub;
            data.multiIndex[1] = 0;
        }

        this.setData(data);
    },

    // 确定选择分类
    bindMultiPickerChange: function(e){
        var category_id = this.data.categorys[this.data.multiIndex[0]].subCategory[this.data.multiIndex[1]].category_id;
        this.data.category_id = category_id;
    },

    
    // api获取分类
    getCategories:function(){
        var that = this
        app.API.getJSON({c:'Category',a:'muneCategory'},function(res){
            if(res.data.code == 0){
                // 保存缓存
                wx.setStorage({
                    key:"categorysDataCache",
                    data:res.data.rs
                })
                that.arrCategorys(res.data.rs)
            }
        })
    },


    // 提交表单
    submitForm: function(e){
        this.resetForm();
        var params = e.detail.value;
        if(e.type == 'confirm'){
            var params = this.data.query_params;
            params.sku = e.detail.value;
        }
        params.business = app.getFromMark('ch');
        params.category_id = this.data.category_id;
        this.data.query_params = params;
        this.findItemGroup();
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

    /**
    * 查询店铺款式
    */
    findItemGroup: function(){
        var that = this,
            page = this.data.page,
            params = this.data.query_params,
            stock_rows = this.data.stockList.rows ? this.data.stockList.rows : [];
        
        // 分页
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
        },'index.php?c=O2o&a=findItemGroup');
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
        var path = '/pages/o2o/findItemGroup';
        var frMark = app.getFromMark();

        return {
            title: this.data.business_name + '款式查询',
            desc: '店铺商品快速查询',
            path: path + "?" + app.UTIL.parseParam(frMark)
        }
    },

    // 页面上拉触底事件的处理函数
    onReachBottom:function(){
        this.findItemGroup()
    }
    
})