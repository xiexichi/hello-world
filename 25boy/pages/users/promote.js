var app = getApp();

Page({

  /**
   * 页面的初始数据
   */
  data: {
      user:[],
      info:[],
      is_withdrawal:true,
      history:[],
      page:0,
      reload: false,
      loadmore: 'display-none',
      loadmore_line: 'display-none'
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
      // 记录来源
      app.markFromSource(options,'pages/users/promote');
  },

  onShow: function(){
      var that = this;
      // 检查登录
      app.checkLogin(function(res){
          if(res.sessionId == undefined || res.sessionId == ''){
              wx.redirectTo({
                url:'/pages/public/login?gourl='+escape('/pages/users/promote')
              });
              return false;
          }else{
              that.setData({
                user:res,
                page: 0,
                reload: false
              });
              // 我的收益
              that.earnings();
              // 收益明细
              that.loadMore(true);
          }
      });
  },


  // 我的收益
  earnings: function(){
      var that = this;

      // loading
      wx.showToast({
          title: '加载中',
          icon: 'loading',
          duration: 10000,
          mask:true
      });
      app.API.getJSON({sessionId:that.data.user.sessionId},function(res){
          wx.hideToast();
          if(res.data.code == 0){
              that.setData({
                  info: res.data.rs,
                  is_withdrawal: !parseFloat(res.data.rs.withdrawal.is_withdrawal)
              });
          }
      },'index.php?c=promote&a=earnings');
  },

  // 收益明细
  loadMore: function(refresh) {
      var that = this,
          page = this.data.page;

      if(refresh === true){
        var history = [];
      }else{
        var history = that.data.history;
      }

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
      });

      app.API.getJSON({pageNo:page,sessionId:that.data.user.sessionId},function(res){
          wx.stopPullDownRefresh();
          if(res.data.code == 0){
              that.setData({
                  reload: false,
                  loadmore: 'display-none',
                  history: history.concat(res.data.rs.data),
                  page: page
              });
          }else{
              that.setData({
                  loadmore_line: 'disply-block',
                  loadmore: 'display-none',
                  reload: true,
                  page: page
              });
          }
      },'index.php?c=promote&a=history');
  },


  // 页面上拉触底事件的处理函数
  onReachBottom:function(){
      this.loadMore();
  },

})