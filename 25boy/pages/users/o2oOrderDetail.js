var app = getApp();

Page({
  data:{
    user:[],
    order_id:'',
    order_sn:'',
    popLayerClass: 'hide',
    popBagLayerClass:'hide',
    fillinLayerClass: 'hide',
    // 多种支付选择框
    // displayMyCode: 'hide',
    order:[],
    // 是否换货订单
    isSA:false,
    modalHide: true,
    userAddress:[],
    // 填写快递单号使用的退换货单号
    reorder_id: '',
    // 选择支付方式
    paySelectLayerClass: 'hide',
    payModalHide: true,
    payTips:{}
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
                that.getO2Order()
            }
        }
    })
  },


  onShow: function(){
      var order = this.data.order;
      var userAddress = wx.getStorageSync('userDefaultAddress');
      // o2o代发订单填写收货地址
      if(app.UTIL.isNull(order.order) == false && app.UTIL.isNull(userAddress) == false && order.order.order_type == 'issuing' && order.order.status == 1 && order.order.is_sync == 0){
          // 选择收货地址
          if(app.UTIL.isNull(userAddress.id) == false){
              this.setData({
                  modalHide: false,
                  userAddress:userAddress
              });
          }
      }
  },


  // api获取订单信息
  getO2Order:function(){
      var that = this,
          order_id = this.data.order_id,
          order_sn = this.data.order_sn

      app.API.getJSON({c:'O2order',a:'getO2Order',order_id:order_id,order_sn:order_sn,sessionId:that.data.user.sessionId},function(res){
          // 隐藏loading
          wx.hideToast()
          wx.stopPullDownRefresh()
          if(res.data.code == 0){
              var order = res.data.rs;
              var payTotal = (parseFloat(order.order.pay_total)-parseFloat(order.order.bag_total)).toFixed(2);
              that.setData({
                  order: order,
                  order_id: order.order.order_id,
                  order_sn: order.order.order_sn,
                  isSA: false,
                  payTotal: payTotal
              })

              // 自动弹出支付
              // 这里只要未付款，都会自动弹出支付
              if (res.data.rs.order.status == 0)
                that.repayOrder()
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
    var type = e.currentTarget.dataset.type,
        reorder_id = e.currentTarget.dataset.id

    switch(type){
        case 'fillin':
            this.setData({
                fillinLayerClass: 'show',
                reorder_id: reorder_id
            })
            break;

        case 'payselect':
            this.setData({
                paySelectLayerClass: 'show'
            })
            break;

        default:
            this.setData({
                popLayerClass: 'show'
            })
            break;
    }
  },
  hideLayer: function(e){
    var type = e.currentTarget.dataset.type

    switch(type){
        case 'fillin':
            this.setData({
                fillinLayerClass: 'hide'
            })
            break;

        case 'payselect':
            this.setData({
                paySelectLayerClass: 'hide'
            })
            break;

        default:
            this.setData({
                popLayerClass: 'hide'
            })
            break;
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
        pay_info = this.data.order.order.pay_info,
        bag_total = this.data.order.order.bag_total

    // 调用这个函数有两种方式
    // 一个是系统自动调用，在上面
    // 一个是通过点击‘立即支付’的按钮
    // 第一种方式不能得到参数e,
    if (e == undefined) {
        var order_sn =  this.data.order_sn,
            order_id =  this.data.order_id
    }else {
        var order_sn = e.currentTarget.dataset.sn,
            order_id = e.currentTarget.dataset.id
    }

    var newOrder = {
        order_sn:order_sn,
        order_id:order_id
    }
    that.setData({
        newOrder:newOrder
    })

    // pay_info 已经计算了，如果余额足够会返回支付方式为钱包
    // pay_info 还要增加一下那个先充值后付款的选项的数据

    if (pay_info.pay_type == 'select')
        // 弹出支付选择：1.直接 2.先充后付
        this.repayOrderBySelect()
    else 
        // 直接支付 charge
        this.repayOrderByCharge()
  },

  // 当钱包余额足够时，使用钱包来支付
  // 这个函数是原生直接支付的，不动，防止以后不充值了
  repayOrderByCharge: function (e) {
    var that = this,
        user = this.data.user,
        pay_info = this.data.order.order.pay_info,
        bag_total = this.data.order.order.bag_total

    var title = '支付提示',
        content = '',
        pay_method = ''

    switch(pay_info.pay_method) {
      case 'bag':
        title = '钱包付款'
        content = '需要扣减余额'
        break;
      case 'third':
        title = '微信付款'
        content = '使用微信支付'
        break;
      case 'merge':
        if( bag_total > 0 ){
          title = '合并付款'
          content = "余额已抵扣：¥"+bag_total+"\n微信需要支付"
        }else{
          title = '微信付款'
          content = '使用微信支付'
        }
        break;
    }

    content += "：¥" + pay_info.pay_money

    // 支付确认提示信息
    that.setData({
        payModalHide: false,
        payTips: {
          method: pay_info.pay_method,
          title: title,
          content: content
        }
    })
  },

  // 点击确认支付按钮
  confirmPayModal: function(){
    var pay_info = this.data.order.order.pay_info;
    this.setData({
        payModalHide: true
    })
    switch(pay_info.pay_method) {
      // 钱包付款
      case 'bag':
        // 弹出层
        this.setData({
            popBagLayerClass: 'show'
        })
        break;
      // 微信支付
      case 'third':
        this.wxPay()
        break;
      // 合并付款
      case 'merge':
        this.mergePay()
        break;
    }
  },

  // 如果钱包余额不足，会弹出支付选择：1.直接 2.先充后付
  repayOrderBySelect: function () {
    // 显示弹出层
    this.setData({
        paySelectLayerClass: 'show'
    })

  },

  // 选择支付方式去支付
  paymentSelect: function (e) {
    var that = this,
        user = this.data.user,
        pay_info = this.data.order.order.pay_info,
        bag_total = this.data.order.order.bag_total,
        // 支付方式
        paySelect = e.detail.value.select

        console.log(paySelect)

    if (paySelect == 'recharge') {
        // 先充后付
        this.rechargePay()  
    }else {
        // 直接支付
        if (bag_total > 0)
            // 合并支付
            this.mergePay()
        else 
            // 微信支付
            this.wxPay()
    }
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
                  console.log(res)
                  if(res.data.code == 0){
                      wx.redirectTo({
                          url: '/pages/o2o/complete?order_sn='+order_sn+'&order_id='+order_id+'&method=weixin'
                      })
                  }else if(res.data.code == "-2"){
                      wx.showModal({
                        title: '提示',
                        content: res.data.msg,
                        showCancel: false,
                        success:function(rs){
                            if(rs.confirm){
                                wx.redirectTo({
                                    url: '/pages/o2o/complete?order_sn='+order_sn+'&order_id='+order_id+'&method=weixin'
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
              },'index.php/?c=Payment&a=payO2OrderByBag')

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
                        url: '/pages/o2o/complete?order_sn='+order_sn+'&order_id='+order_id+'&method=weixin'
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

      },'index.php?c=Payment&a=payO2OrderByWeixin')
  },

  // 合并付款方式
  mergePay:function(){
      var that = this,
          order_sn = this.data.newOrder.order_sn,
          order_id = this.data.newOrder.order_id

      wx.showToast({
        title: '请稍候',
        icon: 'loading',
        duration: 10000,
        mask:true
      })

      // 请求支付
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
                        url: '/pages/o2o/complete?order_sn='+order_sn+'&order_id='+order_id+'&method=weixin'
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

      },'index.php?c=Payment&a=payO2OrderByBagAndWeixin')
  },

  // 先充后付
  rechargePay: function(){
      var that = this,
          order_sn = this.data.newOrder.order_sn,
          order_id = this.data.newOrder.order_id

      wx.showToast({
        title: '请稍候',
        icon: 'loading',
        duration: 10000,
        mask:true
      })

      // 请求支付
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
                        url: '/pages/o2o/complete?order_sn='+order_sn+'&order_id='+order_id+'&method=weixin'
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

      },'index.php?c=Payment&a=payO2OrderAfterRecharge')
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
                                that.getO2Order()
                            }
                          })
                      }else{
                        wx.showModal({
                          title: '提示',
                          content: res.data.msg,
                          showCancel: false
                        })
                      }
                  },'index.php?c=O2order&a=cancelO2Order')
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
                                that.getO2Order()
                            }
                          })
                      }else{
                          wx.showModal({
                            title: '提示',
                            content: res.data.msg,
                            showCancel: false
                          })
                      }
                  },'index.php?c=O2order&a=refundO2Order')
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
                                that.getO2Order()
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
          order_id = this.data.order_id,
          reorder_id = this.data.reorder_id
      if(shipCom == '' || shipCom == undefined || shipNo == '' || shipNo == undefined){
          wx.showModal({
            title: '输入错误',
            content: '请填写物流公司和快递单号',
            showCancel: false
          })
      }else{
          wx.showToast({
            title: '请稍候',
            icon: 'loading',
            duration: 10000,
            mask:true
          })
          app.API.postDATA({reorder_id:reorder_id,shipCom:shipCom,shipNo:shipNo,sessionId:that.data.user.sessionId},function(res){
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
                        that.getO2Order()
                    }
                  })
              }else{
                  wx.showModal({
                    title: '提示',
                    content: res.data.msg,
                    showCancel: false
                  })
              }
          },'index.php?c=O2order&a=shipReturn')

      }
  },


  // 申请退换货
  returnOrder:function(e){
      var id = e.currentTarget.dataset.id
      var order = this.data.order.order
      // 非代发订单不允许线下退换
      if (order.order_type != 'issuing') {
        wx.showModal({
          title: '提示',
          content: '此订单并非代发订单，请回到原购买的门店进行退换货操作，谢谢！',
          showCancel: false
        })
        return false
      }
      // 是否允许进行退换
      if (order.is_returned == 0) {
        wx.showModal({
          title: '提示',
          content: '此订单不允许进行退换！',
          showCancel: false
        })
        return false
      }
      wx.redirectTo({
        url: '/pages/users/o2oOrderReturn?id='+id
      })
  },

  // 查看退货
  checkReorder:function(e){
    var id = e.currentTarget.dataset.id
    wx.navigateTo({
      url: '/pages/users/o2oReorder?reorder_id='+id
    });
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
                  that.getO2Order();
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
  cancelModal: function(e){
      this.setData({
          modalHide: true,
          payModalHide: true
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
      this.getO2Order()
  }
  
})