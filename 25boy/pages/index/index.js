var app = getApp();

Page({
  data: {
    user:[],
    loadmore: 'display-none',
    loadmore_line: 'display-none',
    displayPromoteTips:'hide',
    page:0,
    reload: false,
    inputShowed: false,
    inputVal: "",
    // 功能图标
    iconList: [],
    // 图片轮播
    bannerList : [],
    // 放假公告
    holiday : [],
    // 首页活动广告区
    activity_banner : [],
    // 合作联名区
    joint_name: [],
    // 首页新品栏目图
    new_banner : [],
    // 新品上架商品列表
    product_new_list : [],
    // 首页类目栏目图
    category_banner : [],
    // 首页熱門类目
    index_categorys_items : [],
    // 首页品牌栏目图
    brand_banner : [],
    // 首页品牌展示
    brands_list : [],
    // 首页单品图区
    banner_item : [],
    // 精选晒图
    shareList : [],
    // 人气热卖
    hot_productList: [],
    // 首页弹出广告
    activityPopup: []
  },


  onLoad: function(options){
        var that = this;

        // 临时码跳转页面
        app.queryScene(options);

        // 检查登录
        app.checkLogin(function(res){
            if(app.UTIL.isNull(res.sessionId) == false){
                that.setData({
                    user: res
                });
                /*if(app.UTIL.isNull(res.promote_cash) == false && res.promote_cash > 0){
                    // 佣金提现提示
                    wx.getStorage({
                        key:"not_again",
                        complete: function(res){
                            if(app.UTIL.isNull(res.data)){
                                that.setData({
                                    displayPromoteTips: 'show'
                                });
                            }
                        }
                    });
                }*/
            }
            // 查询导航栏是否有动态
            app.setNavBarStatus(res.sessionId);
        });

        // 记录来源
        app.markFromSource(options,'pages/index/index')

        // 读取缓存
        var viewData = wx.getStorageSync('homeDataCache')
        var viewDataTimeOut = wx.getStorageSync('homeDataCacheTimeOut')
        var nowTime = new Date().getTime()
        // console.log('-----------缓存有效时间：' + (viewDataTimeOut-nowTime)/1000/60 + ' 分钟----------------')
        if (viewDataTimeOut > nowTime && viewData) {
            that.setData({
                iconList : viewData.iconList,
                bannerList : viewData.bannerList,
                holiday : viewData.holiday,
                activity_banner : viewData.activity_banner,
                joint_name : viewData.joint_name,
                new_banner : viewData.new_banner,
                product_new_list : viewData.product_new_list,
                category_banner : viewData.category_banner,
                index_categorys_items : viewData.index_categorys_items,
                brand_banner : viewData.brand_banner,
                brands_list : viewData.brands_list,
                banner_item : viewData.banner_item,
                shareList : viewData.shareList,
                activityPopup : viewData.activityPopup
            })
        }else{

            // loading
            wx.showToast({
                title: '加载数据',
                icon: 'loading',
                duration: 10000
            })
            app.API.getJSON({c:'Index',a:'weapp2018'},function(res){
                if(res.data.code == 0){
                    var datalist = res.data.rs.data;
                    // 转换广告url
                    for (var i=0; i<datalist.bannerList.length; i++) {
                        var url = app.UTIL.parseURL(datalist.bannerList[i].url)
                        var newUrl = app.UTIL.newUrl(url)
                        datalist.bannerList[i].newUrl = newUrl
                    }
                    for (var i=0; i<datalist.activity_banner.length; i++) {
                      if(datalist.activity_banner[i].url.indexOf('openMiniProgram') === 0){
                        var param = datalist.activity_banner[i].url.split(':');
                        datalist.activity_banner[i].weapp = {
                            appid: param[1],
                            path: param[2]
                        }
                      }else{
                        var url = app.UTIL.parseURL(datalist.activity_banner[i].url)
                        var newUrl = app.UTIL.newUrl(url)
                        datalist.activity_banner[i].newUrl = newUrl
                      }
                    }
                    for (var i=0; i<datalist.joint_name.length; i++) {
                        if(datalist.joint_name[i].url.indexOf('/pages') === 0){
                          var newUrl = datalist.joint_name[i].url;
                        }else{
                          var url = app.UTIL.parseURL(datalist.joint_name[i].url)
                          var newUrl = app.UTIL.newUrl(url)
                        }
                        datalist.joint_name[i].newUrl = newUrl
                    }
                    for (var i=0; i<datalist.new_banner.length; i++) {
                        var url = app.UTIL.parseURL(datalist.new_banner[i].url)
                        var newUrl = app.UTIL.newUrl(url)
                        datalist.new_banner[i].newUrl = newUrl
                    }
                    for (var i=0; i<datalist.category_banner.length; i++) {
                        var url = app.UTIL.parseURL(datalist.category_banner[i].url)
                        var newUrl = app.UTIL.newUrl(url)
                        datalist.category_banner[i].newUrl = newUrl
                    }
                    for (var i=0; i<datalist.index_categorys_items.length; i++) {
                        var url = app.UTIL.parseURL(datalist.index_categorys_items[i].url)
                        var newUrl = app.UTIL.newUrl(url)
                        datalist.index_categorys_items[i].newUrl = newUrl
                    }
                    for (var i=0; i<datalist.brand_banner.length; i++) {
                        var url = app.UTIL.parseURL(datalist.brand_banner[i].url)
                        var newUrl = app.UTIL.newUrl(url)
                        datalist.brand_banner[i].newUrl = newUrl
                    }
                    for (var i=0; i<datalist.brands_list.length; i++) {
                        var url = app.UTIL.parseURL(datalist.brands_list[i].url)
                        var newUrl = app.UTIL.newUrl(url)
                        datalist.brands_list[i].newUrl = newUrl
                    }
                    for (var i=0; i<datalist.banner_item.length; i++) {
                        var url = app.UTIL.parseURL(datalist.banner_item[i].url)
                        var newUrl = app.UTIL.newUrl(url)
                        datalist.banner_item[i].newUrl = newUrl
                    }
                    if(app.UTIL.isNull(datalist.activityPopup) == false){
                      if(datalist.activityPopup.url.indexOf('openMiniProgram') === 0){
                        var param = datalist.activityPopup.url.split(':');
                        datalist.activityPopup.weapp = {
                            appid: param[1],
                            path: param[2]
                        }
                      }else{
                        var url = app.UTIL.parseURL(datalist.activityPopup.url)
                        var newUrl = app.UTIL.newUrl(url)
                        datalist.activityPopup.newUrl = newUrl
                      }
                      datalist.activityPopup.srcurl += '!w390';
                    }else{
                      datalist.activityPopup = '';
                    }
                    that.setData({
                        iconList : datalist.iconList,
                        bannerList : datalist.bannerList,
                        holiday : datalist.holiday,
                        activity_banner : datalist.activity_banner,
                        joint_name : datalist.joint_name,
                        new_banner : datalist.new_banner,
                        product_new_list : datalist.product_new_list,
                        category_banner : datalist.category_banner,
                        index_categorys_items : datalist.index_categorys_items,
                        brand_banner : datalist.brand_banner,
                        brands_list : datalist.brands_list,
                        banner_item : datalist.banner_item,
                        shareList : datalist.shareList,
                        activityPopup : datalist.activityPopup
                    });

                    wx.hideToast()

                    // 保存缓存12小时
                    wx.setStorage({
                      key:"homeDataCache",
                      data:datalist
                    });
                    wx.setStorage({
                      key:"homeDataCacheTimeOut",
                      data:new Date().getTime()+43200*1000     // 缓存12小时
                    });
                }
            })
        }

        // 人气热卖
        that.loadMore();
  },


  // 加载更多
  loadMore: function() {
      var that = this
      var page = that.data.page
      var productList = that.data.hot_productList


      // 检查是否可以加载
      if(that.data.reload == true){
          return false
      }else{
          page+=1
      }

      // loading
      that.setData({
          loadmore:'disply-block',
          reload: true
      })

      // ajax获取产品列表
      app.API.getJSON({c:'Picshow',a:'getlist',posid:7,pageNo:page,rowNo:8},function(res){
          if(res.data.code == 0 && res.data.rs.data.length>0){
              that.setData({
                  reload: false,
                  loadmore: 'display-none',
                  hot_productList: productList.concat(res.data.rs.data),
                  page:page
              })
          }else{
              that.setData({
                  loadmore_line: 'disply-block',
                  loadmore: 'display-none',
                  reload: true,
                  page:page
              })
          }
      })
  },


  // 隐藏提现申请提示层
  hidePromoteTipsLayer:function(e){
    var tag = e.target.dataset.tag;
    if(tag != 'img'){
        if(tag == 'cancel'){
            // 不再提示
            wx.setStorage({
                key:"not_again",
                data:"promoteTips"
            });
        }
        this.setData({
            displayPromoteTips:'hide'
        })
    }
  },

  hidePopupLayer: function(e){
    var name = e.target.dataset.name;
    this.setData({
        [name]:''
    })
  },

  // 搜索框
  showInput: function () {
      this.setData({
          inputShowed: true
      });
  },
  hideInput: function () {
      this.setData({
          inputVal: "",
          inputShowed: false
      });
  },
  clearInput: function () {
      this.setData({
          inputVal: ""
      });
  },
  inputTyping: function (e) {
      this.setData({
          inputVal: e.detail.value
      });
  },
  // 提交搜索
  searchbarForm: function(e){
      var key = e.detail.value
      console.log(key)
      if(key !== '' && key != undefined){
        wx.navigateTo({
            url:'/pages/products/list?keyword='+key
        });
      }
  },


  // 重新设置图片大小
  resetImage: function(e){
      var that = this,
          idx = e.currentTarget.dataset.idx,
          type = e.currentTarget.dataset.type,
          zoom = e.detail.width/(app.systemInfo.windowWidth);    // 计算缩放比例
      var obj = that.data[type];

      var array = [];
      for (var i = 0; i < obj.length; i++) {
          array[i] = obj[i]
          if(i == idx){
              array[i].width = (e.detail.width/zoom)+'px';
              array[i].height = (e.detail.height/zoom)+'px';
          }
      }

      // 新数据
      var newData = {};
      newData[type] = array;
      that.setData(newData);
  },


  // 分享页面
  onShareAppMessage:function(){
        var path = '/pages/index/index';
        if(this.data.user.promote_id != undefined && this.data.user.promote_id != ""){
            path = path+'?pid='+this.data.user.promote_id;
        }

        return {
            title: '25BOY国潮男装',
            desc: '本土原创潮牌，结合传统醒狮元素打造中国本土原创潮牌，Happy Easy Anyway!',
            path: path
        }
  },

  // 下拉刷新
  onPullDownRefresh: function(){
      wx.removeStorageSync('homeDataCache')
      wx.removeStorageSync('homeDataCacheTimeOut')
      this.onLoad({})
      wx.stopPullDownRefresh()
  }

})