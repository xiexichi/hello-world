var app = getApp();

Page({
  data:{
    loadmore: 'display-none',
    loadmore_line: 'display-none',
    page: 0,
    datalist:[],
    reload: false,
    // scrollHeight:'100%'
  },

  onLoad: function(options){
    var that = this

    // 检查登录
    app.checkLogin(function(res){
      if(res.sessionId == undefined || res.sessionId == ''){
          wx.redirectTo({
            url:'/pages/public/login?gourl='+escape('/pages/users/share')
          })
          return false
      }else{
          that.setData({
            user:res
          })

          // ajax加载第一页
          that.loadMore();
      }
    })

    
    // 修改scroll-view高度
    /*wx.getSystemInfo({
      success: function (res) {
        that.setData({
            scrollHeight: (res.windowHeight+60)+'px'
        });
      }
    })*/

  },

  // 页面上拉触底事件的处理函数
  onReachBottom:function(){
      this.loadMore()
  },

  // 加载更多数据
  loadMore: function(refresh) {
    var that = this
    var page = that.data.page
    if(refresh === true){
      var datalist = []
    }else{
      var datalist = that.data.datalist
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
    })

   // ajax获取产品列表
    app.API.getJSON({c:'Share',a:'getMyShares',pageNo:page,sessionId:that.data.user.sessionId},function(res){
        wx.stopPullDownRefresh()
        if(res.data.code == 0){
          that.setData({
            reload: false,
            loadmore: 'display-none',
            datalist: datalist.concat(res.data.rs.data),
            page: page
          })
        }else{
          that.setData({
            loadmore_line: 'disply-block',
            loadmore: 'display-none',
            reload: true,
            page: page
          })
        }
    })

  },


  // 删除自己的晒图
  delShareTap:function(e){
      var that = this,
          share_id = e.currentTarget.dataset.id

      // ajax获取产品列表
      app.API.getJSON({c:'Share',a:'delShare',share_id:share_id,sessionId:that.data.user.sessionId},function(res){
          if(res.data.code == 0){
              wx.showToast({
                title: '删除成功',
                icon: 'success',
                duration: 2000
              })
              this.setData({
                  page: 0,
                  reload: false
              })
              this.loadMore(true)
          }else{
              wx.showModal({
                  title:'提示',
                  content:res.data.msg,
                  showCancel:false
              })
          }
      })
  },

  // 转到添加晒图页面
  gotoAddShare:function(){
      wx.redirectTo({
        url:'/pages/share/add'
      })
  },

  // 下拉刷新
  onPullDownRefresh: function() {
      this.setData({
          page: 0,
          reload: false
      })
      this.loadMore(true)
  }

})