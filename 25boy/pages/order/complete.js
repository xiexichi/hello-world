var app = getApp();

Page({
  data:{
      user:[],
      order:[],
      order_id:'',
      order_sn:'',
      msgInfo:[]
  },
  onLoad:function(options){
      console.log(options)
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
                  that.getOrder()
              }
          }
      })
  },

  // api获取订单信息
  getOrder:function(){
      var that = this,
          order_id = this.data.order_id,
          order_sn = this.data.order_sn

      wx.showToast({
        title: '处理中',
        icon: 'loading',
        duration: 10000,
        mask:true
      })
      app.API.getJSON({c:'Order',a:'getOrderSimple',order_id:order_id,sessionId:that.data.user.sessionId},function(res){
          // 隐藏loading
          wx.hideToast()
          if(res.data.code == 0){
              that.setData({
                  order: res.data.rs
              })
              that.setMsgInfo()
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
                          url: '/pages/users/order'
                      })
                  }else{
                      wx.navigateBack()
                  }
                }
              })
          }
      })
  },


  // 设置提示信息
  setMsgInfo:function(){
      var order = this.data.order

      if(order.pay_status == 1){
          var msgInfo = {
            type:'success',
            title:'付款成功',
            content:"订单"+ order.order_sn +"已成功支付，\n我们将尽快为您发货配送！\n感谢您的支持与信赖！",
            confirmUrl:'/pages/users/orderDetail?id='+order.order_id,
          }
      }else{
          var msgInfo = {
            type:'warn',
            title:'付款失败',
            content:"订单："+ order.order_sn +" 支付失败。\n稍候可以进入我的订单重新支付，如果已经扣钱请稍等片刻。\n有疑问请联系在线客服：he75he(微信号)，3001188639(QQ)",
            confirmUrl:'/pages/users/orderDetail?id='+order.order_id
          }
      }
      this.setData({
          msgInfo:msgInfo
      })
  }

})