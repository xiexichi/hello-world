var app = getApp();
var sliderWidth = 72; // 需要设置slider的宽度，用于计算中间位置

Page({
  data:{
    user:[],
    loadmore: 'display-none',
    loadmore_line: 'display-none',
    page: 0,
    reload: false,
    type: 'goods',
    bagList:[],
    navbarClassGoods: '',
    navbarClassPrepaid: '',
    navbarClassRefund: '',
    navbarClassShipfee: '',
    method:{
        weixin:'微信支付',
        alipay:'支付宝',
        bag:'钱包支付'
    },
    activeIndex: 0,
    sliderOffset: 0,
    sliderLeft: 0
  },

  onLoad:function(options){
    var that = this
    var type = options.type ? options.type : 'goods'

    // 检查登录
    app.checkLogin(function(res){
        if(res.sessionId == undefined || res.sessionId == ''){
            wx.redirectTo({
              url:'/pages/public/login?gourl='+escape('/pages/users/wallet')
            })
            return false
        }else{
            that.setData({
              user:res,
              type:type
            })

            // ajax加载第一页
            that.setnavdata(type)
            that.loadMore(true)
        }
    });

    wx.getSystemInfo({
      success: function (res) {
        that.setData({
            sliderLeft: (res.windowWidth / 4 - sliderWidth) / 2,
            sliderOffset: res.windowWidth / 4 * that.data.activeIndex
        });
      }
    })

  },

  // 页面上拉触底事件的处理函数
  onReachBottom:function(){
      this.loadMore()
  },

  // 切换navbar
  switchNavbar: function(e){
      var type = e.currentTarget.dataset.type
      this.setnavdata(type)
      // 重置参数
      this.setData({
          type:type,
          bagList:[],
          page:0,
          reload:false,
          loadmore_line: 'display-none',
          sliderOffset: e.currentTarget.offsetLeft,
          activeIndex: e.currentTarget.id
      })
      this.loadMore(false)
  },

  // 下拉加载更多
  loadMore:function(refresh){
      var that = this,
          type = this.data.type,
          page = this.data.page

      if(refresh === true){
        var bagList = []
        // 更新本地登录缓存数据
        app.updateUserInfo(that.data.user.sessionId,function(res){
            that.setData({
              user:res
            })
        })
      }else{
        var bagList = that.data.bagList
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

      app.API.getJSON({c:'Bag',a:'logs',type:type,sessionId:that.data.user.sessionId,pageNo:page},function(res){
          wx.stopPullDownRefresh();

          if(res.data.code == 0){
            that.setData({
              reload: false,
              loadmore: 'display-none',
              bagList: bagList.concat(res.data.rs.data),
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


  // navbar高亮
  setnavdata: function(type){
      switch(type){
          case 'prepaid':
              this.setData({
                  navbarClassGoods: '',
                  navbarClassPrepaid: 'weui-bar__item_on',
                  navbarClassRefund: '',
                  navbarClassShipfee: ''
              })
              break

          case 'refund':
              this.setData({
                  navbarClassGoods: '',
                  navbarClassPrepaid: '',
                  navbarClassRefund: 'weui-bar__item_on',
                  navbarClassShipfee: ''
              })
              break

          case 'shipfee':
              this.setData({
                  navbarClassGoods: '',
                  navbarClassPrepaid: '',
                  navbarClassRefund: '',
                  navbarClassShipfee: 'weui-bar__item_on'
              })
              break

          default:
              this.setData({
                  navbarClassGoods: 'weui-bar__item_on',
                  navbarClassPrepaid: '',
                  navbarClassRefund: '',
                  navbarClassShipfee: ''
              })
              break
      }
  },


  // 发起充值支付
  repayTap:function(e){
      var that = this,
          order_sn = e.currentTarget.dataset.sn

      wx.showToast({
        title: '请稍候',
        icon: 'loading',
        duration: 10000
      })

      // 1. 请求支付
      app.API.postDATA({sn:order_sn,sessionId:that.data.user.sessionId},function(res){
          wx.hideToast()
          if(res.data.code == 0){
              var wxorder = JSON.parse(res.data.rs)

              // 保存模板消息表单凭证
              var arr = wxorder.package.split('=');
              app.saveTemplateSign(arr[1], that.data.user.sessionId, 'pay');
              
              // 2. 发起付款
              wx.requestPayment({
                 'timeStamp':wxorder.timeStamp,
                 'nonceStr':wxorder.nonceStr,
                 'package':wxorder.package,
                 'signType':wxorder.signType,
                 'paySign':wxorder.paySign,
                 success:function(rs){
                    wx.showToast({
                      title: '充值成功',
                      icon: 'success',
                      duration: 2000,
                      complete:function(){
                          // 更新本地登录缓存数据
                          app.updateUserInfo(that.data.user.sessionId,function(res){
                              wx.redirectTo({
                                  url: '/pages/users/wallet?type=prepaid'
                              })
                          })
                      }
                    })
                 },
                 fail:function(rs){
                    wx.showModal({
                        title:'付款失败',
                        content:'有疑问请联系在线客服：he75he(微信号)，3001188639(QQ)',
                        showCancel:false
                    })
                 },
                 complete:function(rs){
                    console.log('------complete------')
                    console.log(rs)
                    wx.redirectTo({
                        url: '/pages/users/wallet?type=prepaid'
                    })
                 }
              })
          }else{
              wx.showModal({
                title: '提示',
                content: res.data.msg,
                showCancel: false
              })
          }

      },'index.php?c=Payment&a=weixinCharge')
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