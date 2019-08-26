var app = getApp();

Page({
  data:{
      user:[],
      order:[],
      order_id:'',
      order_sn:'',
      msgInfo:[],
      modalHide: true,
      userAddress:[]
  },
  onLoad:function(options){
      // console.log(options)
      var that = this,
          order_sn = options.order_sn ? options.order_sn : '',
          order_id = options.order_id ? options.order_id : '',
          method = options.method ? options.method : ''

      // 检查登录
      app.checkLogin(function(res){
          if(res.sessionId != undefined && res.sessionId != ''){
              var user = res
              if((order_id != undefined && order_id > 0) || (order_sn != undefined && order_sn != '')){
                  
                  // 设置数据
                  that.setData({
                      user: user,
                      order_id: order_id,
                      order_sn: order_sn
                  })

                  // 获取订单信息
                  that.getO2Order()
              }
          }
      })
  },

  onShow: function (options) 
  {
    var order = this.data.order;
    if(order.order_type != 'issuing'){
      return false;
    }
    var userAddress = wx.getStorageSync('userDefaultAddress');
    // o2o代发订单填写收货地址
    if(app.UTIL.isNull(userAddress) == false && app.UTIL.isNull(order) == false && order.is_sync == 0){
        // 选择收货地址
        if(app.UTIL.isNull(userAddress.id) == false){
          this.setData({
              modalHide: false,
              userAddress: userAddress
          })
        }
    }
  },

  // api获取订单信息
  getO2Order:function(){
      var that = this,
          order_id = this.data.order_id,
          order_sn = this.data.order_sn

      wx.showToast({
        title: '处理中',
        icon: 'loading',
        duration: 10000,
        mask:true
      })
      app.API.getJSON({c:'O2order',a:'getBasicO2Order',order_id:order_id,sessionId:that.data.user.sessionId},function(res){
          // 隐藏loading
          wx.hideToast()
          if(res.data.code == 0){
              that.setData({
                  order: res.data.rs
              })
              
              that.setMsgInfo()
              // 发送websocket
              that.paySuccessNotifyServer()
           }else{
              wx.showModal({
                title: '提示',
                content: '参数有误，找不到该订单。',
                cancelText: '上一页',
                confirmText: '我的订单',
                showCancel: false,
                success: function(res) {
                  if (res.confirm) {
                      wx.redirectTo({
                          url: '/pages/users/o2oOrder'
                      })
                  }else{
                      wx.navigateBack()
                  }
                }
              })
          }
      })
  },


  // 提交完善地址
  confirmModal: function(){
      var that = this,
          order_id = this.data.order_id,
          userAddress = this.data.userAddress;

      var params = {
          order_id: order_id,
          address_id: userAddress.id,
          sessionId: that.data.user.sessionId
      }
      wx.showToast({
          title: '正在提交',
          icon: 'loading',
          duration: 10000,
          mask: true
      });
      app.API.postDATA(params, function(res){
          wx.hideLoading();
          if(res.data.code == 0){
              that.cancelModal();
              wx.showToast({
                  title: '提交成功',
                  icon: 'success',
                  duration: 2000
              });
              setTimeout(function() {
                  wx.redirectTo({
                    url: '/pages/users/o2oOrderDetail?id='+order_id
                  })                  
              }, 2000);
          }else {
            wx.showModal({
              title: '提示',
              content: res.data.msg,
              showCancel: false
            })
          }
      },'index.php?c=O2order&a=setAddress');
  },

  // 隐藏模态框
  cancelModal: function(){
      this.setData({
          modalHide: true
      });
  },

  // 设置提示信息
  setMsgInfo:function(){
      var order = this.data.order
      if(order.pay_status == 1){
          if (order.order_type == 'issuing') {
             var msgInfo = {
               type:'success',
               title:'付款成功',
               content:"订单"+ order.order_sn +"已成功支付，\n请您立即填写收货地址以便我们发货给您！\n或者也可以稍后在订单里填写收货信息。\n感谢您的支持与信赖！",
               confirmUrl:'/pages/public/address?type=select',
               confirmText: '立即填写收货地址',
               confirmOpenType: 'navigate',
               cancelUrl:'/pages/users/o2oOrderDetail?id='+order.order_id,
               cancelText: '查看订单',
               cancelOpenType: 'redirect',
             } 
          }else {
            var msgInfo = {
              type:'success',
              title:'付款成功',
              content:"订单"+ order.order_sn +"已成功支付，\n感谢您的支持与信赖！",
              confirmUrl:'/pages/index/index',
              confirmText: '返回首页',
              confirmOpenType: 'switchTab'
            }
          }
      }else{
          var msgInfo = {
            type:'warn',
            title:'付款失败',
            content:"订单："+ order.order_sn +" 支付失败。\n稍候可以进入我的订单重新支付，如果已经扣钱请稍等片刻。\n有疑问请向门店员工提出。",
            confirmUrl:'/pages/users/o2oOrderDetail?id='+order.order_id,
            confirmText: '查看订单',
            confirmOpenType: 'redirect',
            cancelUrl:'/pages/index/index',
            cancelText: '返回首页',
            cancelOpenType: 'switchTab'
          }
      }
      this.setData({
          msgInfo:msgInfo
      })
  },

  // 支付成功后发送通知
  paySuccessNotifyServer: function () 
  {
    var that = this,
        order = this.data.order,
        websocket_url = app.API.WEBSOCKET_URL
    // 如果未支付或者没有标记websocket的，返回
    // pay_model 支付模式 1表示手动自助支付，当为自动支付才发送消息通知O2O系统响应
    if(order.pay_status == 0 || order.is_websocket == 0 || order.pay_model != 1) return;

    var json = {
      phone: order.phone,
      order_id: order.order_id,
      type: 'paid',
    }

    var msg = JSON.stringify(json)
    
    // 1.7.0版本是两个分水岭
    // 低于1.7.0为低版本
    if (this.isLowSDKVerison) {
        // 直接发送信息
        wx.sendSocketMessage({
          data: msg
        })
    }else {
        // 先关闭再说
        wx.closeSocket()

        // 连接
        wx.connectSocket({
            url: websocket_url+'?business_code='+order.business_code+'&phone='+order.phone,
        })

        wx.onSocketOpen(function(res) {
          console.log('complete页面ws链接成功')
          // 发送信息
          wx.sendSocketMessage({
            data: msg
          })
        })

        wx.onSocketClose(function(res) {
          console.log('complete页面websocket 已关闭')
        })
    }

    // 断开
    wx.closeSocket()
  },

  isLowSDKVerison: function() {
      wx.getSystemInfo({
          success: function(res) {
          if (res.SDKVersion < '1.7.0')
             return true
          else
             return false
          }
      })
  }


})