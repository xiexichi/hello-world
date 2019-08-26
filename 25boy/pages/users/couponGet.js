var app = getApp();

Page({
  data:{
    user:[],
    type:''
  },

  onLoad:function(options){
      var that = this,
          type = options.type ? options.type : ''

      // 检查登录
      app.checkLogin(function(res){
          if(res.sessionId == undefined || res.sessionId == ''){
              wx.redirectTo({
                url:'/pages/public/login?gourl='+escape('/pages/users/coupon')
              })
              return false
          }else{
              that.setData({
                type:type,
                user:res
              })
          }
      })
  },

  // api领取代金券
  formSubmit: function(e){
      var that = this,
          code = e.detail.value.code

      wx.showToast({
        title: '正在领取',
        icon: 'loading',
        duration: 10000
      })
      app.API.postDATA({coupon_pws:code,sessionId:that.data.user.sessionId},function(res){
          wx.hideToast()
          if(res.data.code == 0){
              wx.showModal({
                  title:'领取成功',
                  content:'优惠券已经静静的躺在你的帐户中，等待您使用呢。',
                  showCancel:false,
                  success: function(res) {
                  if (res.confirm) {
                      wx.redirectTo({
                          url: '/pages/users/coupon'
                      })
                  }
                }
              })
          }else{
              wx.showModal({
                  title:'提示',
                  content:res.data.msg,
                  showCancel:false
              })
          }
      },'index.php?c=Coupon&a=receive_coupon')    
  }

})