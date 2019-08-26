var app = getApp();
// pages/combomeal/index.js
Page({

    /**
    * 页面的初始数据
    */
    data: {
        // 商品套餐数据
        combomeal: [],
        popLayerClass: 'hide',
        // 当前商品规格
        stockprops : [],
        // 当前选择的颜色下面的尺码列表
        sizes: [],
        // 当前商品图片
        defaultimg: '',
        // 选择套餐商品的颜色列表
        selectProps: {},
        // 选择套餐商品的尺码列表
        // selectsizes: [],
        // 当前选择的商品ID
        product_id: '',
        // 当前商品套餐价
        combomeal_price: '',
        // 当前库存
        stockTotal: 0,
        // 当前商品套餐数量
        combomeal_num: 1,
    },

    /**
    * 生命周期函数--监听页面加载
    */
    onLoad: function (options) {
        this.getCombomeal(options.combomeal_id);
    },

    getCombomeal: function(combomeal_id) {
        var that = this
        app.API.getJSON({combomeal_id: combomeal_id},function(res){
            if(res.data.code == 0){
                console.log(res)
                that.setData({
                    combomeal: res.data.rs
                })
            }else {
              wx.showModal({
                  title: '失败提示',
                  content: '没有套餐数据！',
                  showCancel: false,
                  success: function(res) {
                      if (res.confirm) {
                          wx.navigateBack()
                      } else if (res.cancel) {
                      }
                  }
              })

            }
        },'index.php?c=combomeal&a=getCombomeals')
    },

    /**
    * 显示选择尺码层
    **/
    selectPropLayer: function(e){
        var that = this,
            productList = that.data.combomeal.item,
            product_id = e.currentTarget.dataset.id,
            props = [],
            defaultimg = '',
            combomeal_price = '',
            combomeal_num = '',
            stockTotal = '',
            selectProps = this.data.selectProps,
            currentSize = [],
            stockTotal = ''

        for (var i=0; i<productList.length; i++) {
            if (productList[i]['product_id'] == product_id) {
                defaultimg = productList[i].product_img
                combomeal_price = productList[i].combomeal_price
                combomeal_num = productList[i].combomeal_num
                stockTotal = productList[i].stock_total
                props = productList[i].prop;
                break;
            }
        }

        // 重新打开，要显示已选择的颜色
        if(selectProps[product_id] != undefined && selectProps[product_id].color_prop != ''){
            for (var i = 0; i < props.length; i++) {
                if(props[i].color_prop == selectProps[product_id].color_prop){
                    props[i]['class'] = 'text-hover'
                    currentSize = props[i].stock
                    for (var j = 0; j < currentSize.length; j++) {
                        if (currentSize[j].photo_prop != undefined && currentSize[j].photo_prop != '')
                            defaultimg = currentSize[j].photo_prop
                        if(currentSize[j].size_prop == selectProps[product_id].size_prop) {
                            currentSize[j]['class'] = 'text-hover'
                            stockTotal = currentSize[j].quantity
                        }
                        else {
                            currentSize[j]['class'] = 'text-none'
                        }
                    }
                }else{
                    props[i]['class'] = 'text-none'
                }
            }
        }

        that.setData({
            popLayerClass : 'show',
            stockprops: props,
            sizes: props[0].stock,
            product_id: product_id,
            defaultimg: defaultimg,
            combomeal_price: combomeal_price,
            stockTotal: stockTotal,
            combomeal_num: combomeal_num
        })
    },

    hidePropLayer: function(){
        this.setData({
            popLayerClass: 'hide'
        })
    },

    // 选择颜色
    selectColor: function(e){
        var val = e.currentTarget.dataset.val,
            stockprops = this.data.stockprops,
            currentSize = this.data.sizes,
            defaultimg = this.data.defaultimg,
            product_id = this.data.product_id,
            selectProps = this.data.selectProps,
            stockTotal = ''

        if(selectProps[product_id] == undefined || val != selectProps[product_id].color_prop){
            for (var i = 0; i < stockprops.length; i++) {
                if(stockprops[i].color_prop == val){
                    stockprops[i]['class'] = 'text-hover'
                    stockTotal = stockprops[i]['stock_total']
                    currentSize = stockprops[i].stock
                    for (var j = 0; j < currentSize.length; j++) {
                        if (currentSize[j].photo_prop != undefined && currentSize[j].photo_prop != '') {
                            defaultimg = currentSize[j].photo_prop
                            break;
                        }
                    }
                }else{
                    stockprops[i]['class'] = 'text-none'
                }
            }
            selectProps[product_id] = {};
            selectProps[product_id].color_prop = val;
            this.setData({
                stockprops: stockprops,
                sizes: currentSize,
                defaultimg: defaultimg,
                selectProps: selectProps,
                stockTotal: stockTotal
            })

            // this.setParams('color',val)
        }
    },

    // 选择尺码
    selectSize: function(e){
        var val = e.currentTarget.dataset.val,
            stockprops = this.data.stockprops,
            currentSize = this.data.sizes,
            defaultimg = this.data.defaultimg,
            product_id = this.data.product_id,
            selectProps = this.data.selectProps,
            sku_prop = '',
            stockTotal = ''

        if(selectProps[product_id] == undefined || val != selectProps[product_id].size_prop){
            for (var i = 0; i < currentSize.length; i++) {
                if(currentSize[i].size_prop == val){
                    currentSize[i]['class'] = 'text-hover'
                    sku_prop = currentSize[i].sku_prop
                     stockTotal = currentSize[i].quantity
                    // defaultimg = stockprops[i].img
                }else{
                    currentSize[i]['class'] = 'text-none'
                }
            }
            if (selectProps[product_id] == undefined)
                selectProps[product_id] = {};
            selectProps[product_id].size_prop = val;
            selectProps[product_id].sku_prop = sku_prop;
            this.setData({
                stockprops: stockprops,
                sizes: currentSize,
                // defaultimg: defaultimg,
                selectProps: selectProps,
                stockTotal: stockTotal
            })

            // this.setParams('color',val)
        }
    },

    // 检查一下是否有选规格
    checkProp: function(e) {
        var product_id = this.data.product_id,
            selectProps = this.data.selectProps
        if (selectProps[product_id] == undefined || selectProps[product_id].color_prop == undefined) {
            wx.showModal({
                title: '失败提示',
                content: '请选择颜色！',
                showCancel: false
            })
            return false
        }
        if (selectProps[product_id].size_prop == undefined) {
            wx.showModal({
                title: '失败提示',
                content: '请选择尺码！',
                showCancel: false
            })
            return false
        }
        this.hidePropLayer()
    },

    // 立即购买
    buyCombomealNow: function() {
        var productList = this.data.combomeal.item,
            selectProps = this.data.selectProps,
            product_id = '',
            combomeal = this.data.combomeal
        if (productList == undefined) {
            wx.showToast({
                title: '加载中',
                icon: 'loading',
                duration: 1000,
                mask: true
            })
            return false
        }
        if (productList.length == 0) {
            wx.showModal({
                title: '失败提示',
                content: '缺少商品数据',
                showCancel: false
            })
            return false
        }

        wx.hideToast();
        for (var i=0; i<productList.length; i++) {
            product_id = productList[i].product_id
            if (app.UTIL.isNull(selectProps[product_id]) || app.UTIL.isNull(selectProps[product_id].color_prop) || app.UTIL.isNull(selectProps[product_id].size_prop)) {
                wx.showModal({
                    title: '失败提示',
                    content: '请选择所有套餐商品的规格',
                    showCancel: false
                })
                return false
            }
        }

        // 提交到结算页面
        var combomeal_id = combomeal.combomeal_id
        var selectProps = JSON.stringify(selectProps)
        wx.navigateTo({
          url: '../order/order?combomeal_id='+combomeal_id+'&combomeal='+selectProps
        })
    },

    // 预览图片
    previewImage: function(e){
        var type = e.currentTarget.dataset.type,
            img = e.currentTarget.dataset.img,
            combomeal_img = this.data.combomeal.img,
            imgurls = [];

        for (var i=0; i < combomeal_img.length; i++) {
            imgurls.push(combomeal_img[i].url);
        }
        
        wx.previewImage({
            current: img ? img : '',
            urls: imgurls
        })
    },

    /**
    * 页面相关事件处理函数--监听用户下拉动作
    */
    onPullDownRefresh: function () {

    },

    /**
    * 页面上拉触底事件的处理函数
    */
    onReachBottom: function () {

    },

    /**
    * 用户点击右上角分享
    */
    onShareAppMessage: function () {

    }
})