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
      titleName: '全部订单',
      statusItems: {
          "nopay": '未付款',
          "pack": '等待发货',
          "wait": '等待收货',
          "refund": '退款中',
          "refunded": '已退款',
          "return": '退换货',
          "all": '全部订单',
          // "complete": '交易成功',
          // "close": '交易关闭'
      },
      waiting:false,  //等待中
      lastY:0,
      layerTop:0,
      hasOrder: false, // 是否收到订单
      isReady: false, // 是否第一次加载完成
      // // 关注公众号组件兼容判断
      canIUse: wx.canIUse('official-account')
  },

  onLoad: function(options){
      var fromMark = app.getFromMark()
      var that = this,
          type = options.type ? options.type : '',
          fr = fromMark.fr ? fromMark.fr : '',
          // 主要为商户代码 
          ch = fromMark.ch ? fromMark.ch : '',
          // 表示是需要连接websocket的标识
          ws = options.ws ? options.ws : false,
          // 是否倒计时查询订单
          timer = options.timer ? options.timer : false,
          title = that.data.statusItems[type];

      if(app.UTIL.isNull(title) == true){
          title = '全部订单'
      }

      that.setData({
          type: type,
          titleName: title,
          ws: ws,
          timer: timer,
          waiting: (ws||timer),
          business_code: ch
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

              /* 2018-07-22 文杰
              * 因不稳定因素，弃用websocket，增加使用定时器查询未付款订单
              */
              if (fr == 'business' && ch != '') {
                // websocket连接
                if(ws) that.connectWebSocket(ch);
                // 检查待付款的o2o订单
                if(timer) app.checkPayingOrder(res.sessionId, ch);
              }
              
              // ajax加载第一页
              that.loadMore(false);

          }
      })
  },

  onReady: function(){
      this.setData({
        isReady: true
      })
  },

  onShow: function(){
    if(this.data.isReady == true){
      // 重新链接websocket
      if(this.data.ws) this.connectWebSocket(this.data.business_code);
      // 重新查询未付款订单
      if(this.data.timer) app.checkPayingOrder(this.data.user.sessionId, this.data.business_code);
    }
  },

  onHide: function(){
    if(this.data.ws && this.data.hasOrder == false){
        // 离开排队，关闭websocket
        wx.closeSocket();
    }
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
        itemList: ['未付款','等待发货','等待收货','退款中','退换货','全部订单'],
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
  },

  // 连接websocket
  connectWebSocket: function (business_code) 
  {
    var that = this,
        user = this.data.user,
        websocket_url = app.API.WEBSOCKET_URL,
        url = websocket_url+'?business_code='+business_code+'&phone='+user.phone
        console.log(url)

    // 连接
    wx.connectSocket({
      url: url
    })

    wx.onSocketOpen(function(res) {
      console.log('ws链接成功')
    })

    wx.onSocketError(function(res){
      console.log('ws链接失败')
      console.log(res)
    })

    // 监听接受消息
    wx.onSocketMessage(function(res) {
        console.log('收到服务器内容')
        console.log(res.data)
        if (res.data != undefined) {
            var json = JSON.parse(res.data)
            console.log(json)
            if (json == undefined || json.data == undefined) return
            that.setData({
                hasOrder: true
            });
            switch(json.type) {
              case 'order':
                wx.navigateTo({
                    url: '/pages/users/o2oOrderDetail?id='+json.data.order_id
                })
                break;
              case 'paid':
                // 断开
                wx.closeSocket();
                wx.redirectTo({
                    url: '/pages/o2o/complete?order_id='+json.data.order_id
                })
                break;
            }
        }
    })

    wx.onSocketClose(function(res) {
      console.log('WebSocket 已关闭！')
    })

  },

  handletouchmove: function (e) {
    var lastY = this.data.lastY;
    var clientY = e.touches[0].clientY;
    var diff = Math.abs(lastY) - Math.abs(clientY);
    if (diff > 0) { //向上滑动
      this.setData({
        layerTop:-diff
      })
    }
  },
  // 触摸开始
  handletouchtart: function (e) {
    this.setData({
      lastY:e.touches[0].clientY
    })
  },
  // 触摸结束
  handletouchend: function (e) {
    var that = this;
    var layerTop = Math.abs(this.data.layerTop);
    var winHeight = app.systemInfo.windowHeight;
    if(layerTop < 100){
      // 滑动小于100，复原
      this.setData({
        layerTop:0
      })
    }else{
      // 大于100完全打开
      var num = layerTop;  
      var t = setInterval(function(){  
        num = num+100;
        // 放飞自由
        if(num >= winHeight){
          clearInterval(t);
          that.setData({
            waiting:false
          })
        }
        that.setData({
          layerTop:-num
        })        
      },100);  
    }
  }
})
