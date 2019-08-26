var app = getApp();

Page({
  data:{
      user:[],
      categorys:[],
  },
  
  onLoad:function(options){
      var that = this;

      // 读取缓存
      var cacheData = wx.getStorageSync('categorysDataCache')
      var cacheDataTimeOut = wx.getStorageSync('homeDataCacheTimeOut')
      var nowTime = new Date().getTime()
      if (cacheDataTimeOut > nowTime && cacheData) {
          this.setData({
              categorys:cacheData
          })
      }else{
          this.getCategories()
      }

      // 检查登录
      app.checkLogin(function(res){
          if(res.sessionId != undefined && res.sessionId != ''){
              that.setData({
                  user: res
              })
          }
          // 查询导航栏是否有动态
          app.setNavBarStatus(res.sessionId);
      })

      // 记录来源
      app.markFromSource(options,'pages/categorys/index')
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
              that.setData({
                  categorys:res.data.rs
              })
          }
      })
  },

  // 分享页面
  onShareAppMessage:function(){
      var path = '/pages/categorys/index';

      if(this.data.user.promote_id != undefined && this.data.user.promote_id != ""){
          path = path+'?pid='+this.data.user.promote_id;
      }

      return {
        title: '男装分类 - 25BOY国潮男装',
        desc: '潮牌新国货，原创设计品牌。',
        path: path
      }
  },


  // 下拉刷新
  onPullDownRefresh: function(){
      wx.removeStorageSync('homeDataCache')
      wx.removeStorageSync('homeDataCacheTimeOut')
      this.getCategories()
      wx.stopPullDownRefresh()
  }
  
})