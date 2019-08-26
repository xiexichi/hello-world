var app = getApp();

Page({
  data:{
    user:[],
    order_id:'',
    order_sn:'',
    popLayerClass: 'hide',
    popBagLayerClass:'hide',
    fillinLayerClass: 'hide',
    order:[],
    // 是否换货订单
    isSA:false,
    modalHide: true,
    userAddress:[]
  },
  onLoad:function(options){
    var that = this,
        order_id = options.id ? options.id : '',
        order_sn = options.sn ? options.sn : ''

    // 检查登录
    app.checkLogin(function(res){
        if(res.sessionId != undefined && res.sessionId != ''){
            var user = res
            if((order_id != undefined && order_id > 0) || (order_sn != undefined && order_sn != '')){
                wx.showToast({
                  title: '读取中',
                  icon: 'loading',
                  duration: 10000,
                  mask:true
                })

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


  onShow: function(){
      var order = this.data.order;
      if(app.UTIL.isNull(order.order) == false && app.UTIL.isNull(order.order.location)){
          // 选择收货地址
          var userAddress = wx.getStorageSync('userDefaultAddress');
          if(app.UTIL.isNull(userAddress.id) == false){
              this.setData({
                  modalHide: false,
                  userAddress:userAddress
              });
          }
      }
  },


  // api获取订单信息
  getOrder:function(){
      var that = this,
          order_id = this.data.order_id,
          order_sn = this.data.order_sn

      app.API.getJSON({c:'Order',a:'getOrder',order_id:order_id,order_sn:order_sn,sessionId:that.data.user.sessionId},function(res){
          // 隐藏loading
          wx.hideToast()
          wx.stopPullDownRefresh()

          if(res.data.code == 0){
              if(res.data.rs.order.order_sn.indexOf("SA") != -1){
                  var isSA = true
              }else{
                  var isSA = false
              }
              that.setData({
                  order: res.data.rs,
                  order_id: res.data.rs.order.order_id,
                  order_sn: res.data.rs.order.order_sn,
                  isSA: isSA
              })
          }else{
              wx.showModal({
                title: '提示',
                content: '参数有误，找不到该订单。',
                confirmText: '上一页',
                showCancel: false,
                success: function(res) {
                  if (res.confirm) {
                    wx.navigateBack()
                  }
                }
              })
          }
      })
  },

  // 弹出层
  showLayer: function(e){
    var type = e.currentTarget.dataset.type
    if(type == 'fillin'){
        this.setData({
            fillinLayerClass: 'show'
        })
    }else{
        this.setData({
            popLayerClass: 'show'
        })
    }
  },
  hideLayer: function(e){
    var type = e.currentTarget.dataset.type
    if(type == 'fillin'){
        this.setData({
            fillinLayerClass: 'hide'
        })
    }else{
        this.setData({
            popLayerClass: 'hide'
        })
    }
  },

  // 隐藏层取消支付
  hideBagLayer: function(){
    this.setData({
      popBagLayerClass: 'hide'
    })
  },


  // 重新支付订单
  repayOrder:function(e){
      var that = this,
          user = this.data.user,
          order_sn = e.currentTarget.dataset.sn,
          order_id = e.currentTarget.dataset.id

      var newOrder = {
          order_sn:order_sn,
          order_id:order_id
      }
      that.setData({
          newOrder:newOrder
      })

      wx.showActionSheet({
          itemList: ['微信支付', '钱包付款', '合并付款(钱包+微信)'],
          success: function(res) {
            if (!res.cancel) {
              switch(res.tapIndex){
                  // 微信支付
                  case 0:
                    that.wxPay()
                    break;
                  // 钱包付款
                  case 1:
                    // 弹出层
                    that.setData({
                        popBagLayerClass: 'show'
                    })
                    break;
                  // 合并付款
                  case 2:
                    that.mergePay()
                    break;

              }
            }
          }
      })
  },

  // 钱包支付方式
  checkBagSubmit:function(e){
      var that = this,
          order_sn = this.data.newOrder.order_sn,
          order_id = this.data.newOrder.order_id,
          password = e.detail.value.password

      if(password == undefined || password == ""){
          wx.showModal({
            title: '请输入密码',
            content: '支付密码与登录密码一样，忘记密码可通过微信登录进入我的二五->帐户设置，直接修改密码。',
            showCancel: false
          })
          return false
      }

      wx.showToast({
        title: '正在付款',
        icon: 'loading',
        duration: 10000,
        mask:true
      })


      // 获取签名sign
      app.API.getJSON({sn:order_sn,sessionId:that.data.user.sessionId},function(rs){

          if(rs.data.code == 0){
              var timeStamp = rs.data.rs.timeStamp
              var salt = rs.data.rs.sign

              // 发起支付
              app.API.postDATA({sn:order_sn,password:password,timeStamp:timeStamp,salt:salt,sessionId:that.data.user.sessionId},function(res){
                  wx.hideToast()
                  if(res.data.code == 0){
                      wx.redirectTo({
                          url: '/pages/order/complete?order_sn='+order_sn+'&order_id='+order_id+'&method=weixin'
                      })
                  }else if(res.data.code == "-2"){
                      wx.showModal({
                        title: '提示',
                        content: res.data.msg,
                        showCancel: false,
                        success:function(rs){
                            if(rs.confirm){
                                wx.redirectTo({
                                    url: '/pages/order/complete?order_sn='+order_sn+'&order_id='+order_id+'&method=weixin'
                                })
                            }
                        }
                      })
                  }else{
                      wx.showModal({
                        title: '提示',
                        content: res.data.msg,
                        showCancel: false
                      })
                  }
              },'index.php/?c=Payment&a=bag')

          } //获取签名sign
      },'index.php?c=Payment&a=getSign')

  },

  // 微信付款
  wxPay:function(){
      var that = this,
          order_sn = this.data.newOrder.order_sn,
          order_id = this.data.newOrder.order_id

      wx.showToast({
        title: '请稍候',
        icon: 'loading',
        duration: 10000,
        mask:true
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
                 complete:function(rs){
                    wx.redirectTo({
                        url: '/pages/order/complete?order_sn='+order_sn+'&order_id='+order_id+'&method=weixin'
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

      },'index.php?c=Payment&a=weixin')
  },

  // 合并付款方式
  mergePay:function(){
      var that = this,
          order_sn = this.data.newOrder.order_sn,
          order_id = this.data.newOrder.order_id

      wx.showModal({
        title:'合并付款须知',
        content:'您正在使用合并付款，若该订单需要退换货，退款将退回帐户余额，不能提现与转赠。',
        cancelText:'取消',
        confirmText:'同意使用',
        success: function(res){
            if (res.confirm) {
                wx.showToast({
                  title: '请稍候',
                  icon: 'loading',
                  duration: 10000,
                  mask:true
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
                              console.log('------success------')
                              console.log(rs)
                           },
                           fail:function(rs){
                              console.log('------fail------')
                              console.log(rs)
                           },
                           complete:function(rs){
                              console.log('------complete------')
                              console.log(rs)
                              wx.redirectTo({
                                  url: '/pages/order/complete?order_sn='+order_sn+'&order_id='+order_id+'&method=weixin'
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
            }
        }
      })

      
  },

  // 取消订单
  cancelOrder:function(e){
      var that = this,
          order_id = e.currentTarget.dataset.id

      wx.showModal({
          title:'取消订单',
          content:'您确认要取消订单吗？',
          confirmText:'确认取消',
          cancelText:'点错了',
          success:function(rs){
              if(rs.confirm){
                  wx.showToast({
                    title: '请稍候',
                    icon: 'loading',
                    duration: 10000,
                    mask:true
                  })
                  app.API.getJSON({order_id:order_id,sessionId:that.data.user.sessionId},function(res){
                      wx.hideToast()
                      if(res.data.code == 0){
                          wx.showToast({
                            title: '已取消',
                            icon: 'success',
                            duration: 1500,
                            complete:function(){
                                that.getOrder()
                            }
                          })
                      }else{
                        wx.showModal({
                          title: '提示',
                          content: res.data.msg,
                          showCancel: false
                        })
                      }
                  },'index.php?c=Order&a=cancelOrder')
              }
          }
      })
  },

  // 申请退款
  applyRefund:function(e){
      var that = this,
          order_id = e.currentTarget.dataset.id

      wx.showModal({
          title:'申请退款',
          content:'款项会原路退回，您确认不买了吗？',
          confirmText:'不买了',
          cancelText:'点错了',
          success:function(rs){
              if(rs.confirm){
                  wx.showToast({
                    title: '请稍候',
                    icon: 'loading',
                    duration: 10000,
                    mask:true
                  })
                  app.API.getJSON({order_id:order_id,sessionId:that.data.user.sessionId},function(res){
                      wx.hideToast()
                      if(res.data.code == 0){
                          wx.showToast({
                            title: '申请成功',
                            icon: 'success',
                            duration: 1500,
                            complete:function(){
                                that.getOrder()
                            }
                          })
                      }else{
                          wx.showModal({
                            title: '提示',
                            content: res.data.msg,
                            showCancel: false
                          })
                      }
                  },'index.php?c=Order&a=refundOrder')
              }
          }
      })
  },

  // 确认收货
  comfrimOrder:function(e){
      var that = this,
          order_id = e.currentTarget.dataset.id

      wx.showModal({
          title:'完成交易',
          content:'确认收货后，不能申请退换货了，您确认操作吗？',
          confirmText:'确认收货',
          cancelText:'点错了',
          success:function(rs){
              if(rs.confirm){
                  wx.showToast({
                    title: '请稍候',
                    icon: 'loading',
                    duration: 10000,
                    mask:true
                  })
                  app.API.getJSON({order_id:order_id,sessionId:that.data.user.sessionId},function(res){
                      wx.hideToast()
                      if(res.data.code == 0){
                          wx.showToast({
                            title: '交易完成',
                            icon: 'success',
                            duration: 1500,
                            complete:function(){
                                that.getOrder()
                            }
                          })
                      }else{
                          wx.showModal({
                            title: '提示',
                            content: res.data.msg,
                            showCancel: false
                          })
                      }
                  },'index.php?c=Order&a=comfrimOrder')
              }
          }
      })
  },

  // 填写快递单号
  inExpressNumber:function(e){
      var that = this,
          shipCom = e.detail.value.shipCom,
          shipNo = e.detail.value.shipNo,
          order_id = this.data.order_id
      
      if(shipCom == '' || shipCom == undefined || shipNo == '' || shipNo == undefined){
          wx.showModal({
            title: '输入错误',
            content: '请填写物流公司和快递单号',
            showCancel: false
          })
      }else{

          // 如果是换货订单，取原订单order_id 
          if(that.data.isSA){
              order_id = this.data.order.relation_order.order_id
          }

          wx.showToast({
            title: '请稍候',
            icon: 'loading',
            duration: 10000,
            mask:true
          })
          app.API.postDATA({order_id:order_id,shipCom:shipCom,shipNo:shipNo,sessionId:that.data.user.sessionId},function(res){
              wx.hideToast()
              if(res.data.code == 0){
                  wx.showToast({
                    title: '成功',
                    icon: 'success',
                    duration: 1500,
                    complete:function(){
                        // 伪造event给hideLayer()
                        var fakeEvent = {currentTarget:{dataset:{type:'fillin'}}}
                        that.hideLayer(fakeEvent)
                        that.getOrder()
                    }
                  })
              }else{
                  wx.showModal({
                    title: '提示',
                    content: res.data.msg,
                    showCancel: false
                  })
              }
          },'index.php?c=Order&a=shipReturn')

      }
  },


  // 申请退换货
  returnOrder:function(e){
      var id = e.currentTarget.dataset.id;
      wx.redirectTo({
        url: '/pages/users/orderReturn?id='+id
      })
  },


  // 拨打电话
  makeCall:function(){
    wx.makePhoneCall({
        phoneNumber: '4008250830'
    })
  },

  // o2o订单填写收货地址
  setAddress: function(){
      wx.navigateTo({
        url: '/pages/public/address?type=select'
      });
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
                  that.getOrder();
              }, 2000);
          }
      },'index.php?c=order&a=setAddress');
  },

  // 隐藏模态框
  cancelModal: function(){
      this.setData({
          modalHide: true
      });
  },

  // 显示操作状态提示框
  showOrderTip:function(e){
      var title = e.currentTarget.dataset.title
      var content = e.currentTarget.dataset.content
      wx.showModal({
        title: title,
        content: content,
        showCancel: false
      })
  },

  // 下拉刷新
  onPullDownRefresh: function() {
      this.getOrder()
  }
  
})