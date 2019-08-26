// 改版首页
var app = getApp()

Page({

  /**
   * 页面的初始数据
   */
  data: {
    // 左右滑动单个项目宽度
    goodsItemWidth: 2.8,
    // 返回顶部图标
    backTopIcon: false,
    tabActive: 0,
    tabs: [{
      id: 5,
      name: 'HEA',
      list: [],
      page: 0
    },
    {
      id: 4,
      name: '银鳞堂',
      list: [],
      page: 0
    },
    {
      id: 3,
      name: 'HE75 DENIM',
      list: [],
      page: 0
    }],
    // 加载中
    reload: false,
    // 图片轮播
    bannerList : '',
    // 放假公告
    holiday : '',
    // 首页活动广告区
    activity_banner : '',
    // 首页熱門类目
    index_categorys_items : '',
    // 精选晒图
    shareList : '',
    // 首页弹出广告
    activityPopup: '',
    // 新品广告图
    boyChosen: ''
  },

  // 获取首页数据
  getHomeData () {
    let _this = this
    // loading
    wx.showLoading({
      title: '加载中',
    })
    app.API.getJSON({c:'Index',a:'v3'},function(res){
      if(res.data.code == 0) {
        _this.initData(res.data.rs.data)
        wx.hideLoading()
        wx.stopPullDownRefresh()
      }
    })
  },

  initData (datalist) {
    var that = this

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
    for (var i=0; i<datalist.index_categorys_items.length; i++) {
        var url = app.UTIL.parseURL(datalist.index_categorys_items[i].url)
        var newUrl = app.UTIL.newUrl(url)
        datalist.index_categorys_items[i].newUrl = newUrl
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
    for (var i=0; i<datalist.boyChosen.length; i++) {
      var group = datalist.boyChosen[i]
      if(app.UTIL.isNull(group.banner) == false){
      }
      for (var j=0; j<group.list.length; j++) {
        var url = app.UTIL.parseURL(group.list[j].url)
        var newUrl = app.UTIL.newUrl(url)
        group.list[j].newUrl = newUrl
      }
      datalist.boyChosen[i] = group
    }
    that.setData({
        bannerList : datalist.bannerList,
        holiday : datalist.holiday,
        activity_banner : datalist.activity_banner,
        index_categorys_items : datalist.index_categorys_items,
        shareList : datalist.shareList,
        activityPopup : datalist.activityPopup,
        boyChosen : datalist.boyChosen
    });

    // 保存缓存12小时
    wx.setStorage({
      key:"homeDataCache",
      data:datalist
    });
    wx.setStorage({
      key:"homeDataCacheTimeOut",
      data:new Date().getTime()+43200*1000     // 缓存12小时
    });
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
    if(app.UTIL.isNull(key) == false){
      wx.navigateTo({
        url:'/pages/products/list?keyword='+key
      });
    }
  },

  // 商品列表
  getProductList: function() {
    var that = this
    var active = that.data.tabActive
    var tab = that.data.tabs[active]
    var list = tab.list
    var page = tab.page

    // 检查是否可以加载
    if(that.data.reload == true){
        return false
    }else{
        page+=1
    }
    // loading
    that.setData({
        reload: true
    })

    // loading
    wx.showLoading({
      title: '加载中',
    })

    // ajax获取产品列表
    app.API.getJSON({
        brand_id: tab.id,
        pageNo: page
      },function(res){
        if(res.data.code == 0 && res.data.rs.data.length>0){
          tab.list = list.concat(res.data.rs.data)
          tab.page = page
          that.setData({
            reload: false,
            ['tabs['+active+']']: tab
          })
        }else{
          that.setData({
            reload: false,
            ['tabs['+active+'].page']: page
          })
        }
        wx.hideLoading()
    },'index.php?c=Product&a=productList')
  },

  // 切换标签
  changeTab: function(e) {
    var index = e.detail.index
    var tab = this.data.tabs[index]
    this.setData({
      tabActive: index
    })
    if(tab.list.length == 0){
      this.getProductList()
    }
  },

  // 滚动到顶部
  backTop:function(){
    // 控制滚动
      wx.pageScrollTo({
        scrollTop: 0
      })
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var that = this;
    // 临时码跳转页面
    app.queryScene(options);
    // 检查登录
    app.checkLogin(function(res){
      if(app.UTIL.isNull(res.sessionId) == false){
          that.setData({
              user: res
          });
      }
    });
    // 记录来源
    app.markFromSource(options,'pages/index/index')

    // 读取缓存
    var viewData = wx.getStorageSync('homeDataCache')
    var viewDataTimeOut = wx.getStorageSync('homeDataCacheTimeOut')
    var nowTime = new Date().getTime()
    if (viewDataTimeOut > nowTime && viewData) {
      that.setData({
        bannerList : viewData.bannerList,
        holiday : viewData.holiday,
        activity_banner : viewData.activity_banner,
        index_categorys_items : viewData.index_categorys_items,
        shareList : viewData.shareList,
        activityPopup : viewData.activityPopup,
        boyChosen : viewData.boyChosen
      });
    }else{
      that.getHomeData()
    }
    this.getProductList()
  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function(){
    wx.removeStorageSync('homeDataCache')
    wx.removeStorageSync('homeDataCacheTimeOut')
    this.getHomeData()
  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {
    this.getProductList()
  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function(){
    var path = '/pages/home/index';
    if(app.UTIL.isNull(this.data.user.promote_id) == false){
      path = path+'?pid='+this.data.user.promote_id;
    }
    console.log(path)
    return {
      title: '25BOY国潮男装',
      desc: '本土原创潮牌，结合传统醒狮元素打造中国本土原创潮牌，Happy Easy Anyway!',
      path: path
    }
  },

  // 监听滚动条坐标
  onPageScroll: function (e) {
    var that = this
    var scrollTop = e.scrollTop
    var backTopIcon = scrollTop > 1000 ? true : false
    that.setData({
      backTopIcon: backTopIcon
    })
  }
})