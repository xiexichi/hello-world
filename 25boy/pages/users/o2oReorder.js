var app = getApp();

Page({
  data:{
      user:[],
      loadmore: 'display-none',
      loadmore_line: 'display-none',
      page: 0,
      reload: false,
      type:'',
      orderList:[],
      titleName: '全部订单【门店】',
      statusItems: {
          "nopay": '未付款【门店】',
          "pack": '等待发货【门店】',
          "wait": '等待收货【门店】',
          "refund": '退款中【门店】',
          "refunded": '已退款【门店】',
          "return": '退换货【门店】',
          "all": '全部订单【门店】',
      }
  },

  onLoad: function(options){
      var that = this,
          type = options.type ? options.type : '',
          title = that.data.statusItems[type];

      if(app.UTIL.isNull(title) == true){
          title = '全部订单【门店】'
      }
      that.setData({
          type: type,
          titleName: title
      });

      // 检查登录
      app.checkLogin(function(res){
          if(res.sessionId == undefined || res.sessionId == ''){
              wx.redirectTo({
                url:'/pages/public/login?gourl='+escape('/pages/users/o2oOrder')
              })
              return false
          }else{
              that.setData({
                user:res
              })

              // ajax加载第一页
              that.loadMore(false);
          }
      })
  },

  // 页面上拉触底事件的处理函数
  onReachBottom:function(){
      this.loadMore(false)
  },

  // api获取订单
  loadMore:function(refresh){
      var that = this
      var page = that.data.page

      if(refresh === true){
        var orderList = []
      }else{
        var orderList = that.data.orderList
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
      app.API.getJSON({c:'O2order',a:'category',sessionId:that.data.user.sessionId,status:that.data.type,pageNo:page},function(res){
          wx.stopPullDownRefresh()
          if(res.data.code == 0){
            that.setData({
              reload: false,
              loadmore: 'display-none',
              orderList: orderList.concat(res.data.rs.data),
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

  // 弹出筛选
  filterOrderTap: function(e) {
      var statusCode = ['nopay','pack','wait','refund','return','all']

      wx.showActionSheet({
        itemList: ['未付款【门店】','等待发货【门店】','等待收货【门店】','退款中【门店】','退换货【门店】','全部订单【门店】'],
        success: function(res) {
          if (!res.cancel) {
            wx.redirectTo({
                url: '/pages/users/o2oOrder?type='+statusCode[res.tapIndex]
            })
          }
        }
      })
  },


  // 转到链接
  gourl: function(e){
      var url = e.currentTarget.dataset.url,
          target = e.currentTarget.dataset.target
      if(target == 'new'){
          wx.navigateTo({
              url: url
          })
      }else{
          wx.redirectTo({
              url: url
          })
      }
  },

  // 下拉刷新
  onPullDownRefresh: function() {
      this.setData({
          page: 0,
          reload: false,
          orderList: []
      })
      this.loadMore(true)
  }


})
