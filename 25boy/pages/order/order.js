var app = getApp();

/**
* 注意：
* 从2018-09-25起，使用新的使用券功能
* 旧的coupons参数弃用，改用voucher
*/
Page({
  data:{
    cart_ids:'',
    combomeal_id: '',
    combomeal: {},
    cartList: [],
    user: [],
    addressLayerClass:'hide',
    userDefaultAddress:[],
    arrow:{
      payment:'arrow',
      shipping:'arrow',
      coupon:'arrow'
    },
    display:{
      payment:'display-block',
      shipping:'display-none',
      coupon:'display-none'
    },
    text:{
      payment:'微信支付',
      shipping:'默认圆通',
      coupon:'有可用代金券'
    },
    payment:{
        weixin:'微信支付',
        bag:'钱包付款',
        merge:'合并付款'
    },
    radioChecked:{
        weixin:true,
        bag:false,
        merge:false
    },
    deliverys:[],
    coupons:[], // 旧的优惠券，已经弃用
    voucher:[], // 新的优惠券
    subtotal:{
      goodstotal:0,
      cuttotal:0,
      paytotal:0,
      ship_fee:0,
      coupon_price:0
    },
    product_ids:[],
    // 用于提交订单的参数
    params:{
      payment:'weixin'
    },
    popLayerClass:'hide',
    // 创建新订单的数据
    newOrder:[],
    // 帐户余额
    user_balance:0,
    // 合并付款需要的金额
    merge_price:0,
    // 第一次加载为0
    onshowNum:0
  },

  onLoad:function(options){
    var that = this
    var cart_ids = options.cart_ids ? options.cart_ids : ''
    var combomeal_id = options.combomeal_id ? options.combomeal_id : ''
    var combomeal = options.combomeal ? options.combomeal : ''

    // 检查登录
    app.checkLogin(function(res){
        if(res.sessionId == undefined || res.sessionId == ''){
            wx.redirectTo({
              url:'/pages/public/login?gourl='+escape('/pages/cart/index')
            })
            return false
        }else{
            that.setData({
              user:res,
              cart_ids:cart_ids,
              combomeal_id: combomeal_id,
              combomeal: combomeal
            })
            // 加载购物车商品
            if (combomeal_id != undefined && combomeal_id != '')
              that.getCombomealOrderParams();
            else
              that.getOrderParams();
        }
    })
  },

  // 监测页面显示事件
  onShow:function(){
      // 选择收货地址
      var oldAddress = this.data.userDefaultAddress
      var onshowNum = this.data.onshowNum
      var userDefaultAddress = wx.getStorageSync('userDefaultAddress')

      this.setData({
          userDefaultAddress:userDefaultAddress,
          onshowNum:onshowNum+1
      })

      // 重新计算运费
      if(onshowNum>0 && oldAddress.id != userDefaultAddress.id){
          this.getShipFee(this.data.params.shipping)
      }
  },


  // api获取购物车
  getOrderParams:function(){
      var that = this,
          userDefaultAddress = this.data.userDefaultAddress,
          address_id = userDefaultAddress.id ? userDefaultAddress.id : '',
          params = this.data.params,
          cart_ids = this.data.cart_ids

      if(cart_ids == undefined || cart_ids == ''){
          wx.showModal({
            title: '参数错误',
            content: '请返回购物车选择需要下单的商品',
            showCancel: false,
            success: function(res) {
              if (res.confirm) {
                wx.navigateBack()
              }
            }
          })
          return false
      }

      // loading
      wx.showToast({
        title: '加载中',
        icon: 'loading',
        duration: 10000,
        mask:true
      })

      app.API.getJSON({c:'Order',a:'orderParams',cart_ids:cart_ids,address_id:address_id,sessionId:that.data.user.sessionId},function(res){
          wx.hideToast();
          wx.stopPullDownRefresh();
          if(res.data.code==0 && res.data.rs.carts.length > 0){
            if(userDefaultAddress==0 || userDefaultAddress.length == 0){
                userDefaultAddress = res.data.rs.userDefaultAddress
            }
            // 价格
            var subtotal = {
              goodstotal:res.data.rs.goodstotal,
              cuttotal:res.data.rs.cuttotal,
              paytotal:res.data.rs.paytotal.toFixed(2),
              ship_fee:res.data.rs.ship_fee,
              coupon_price:res.data.rs.coupon_price
            }
            // 参数
            params.shipping = res.data.rs.delivery_id
            // 代金券金额
            var coupons = res.data.rs.coupons
            for (var i = 0; i < coupons.length; i++) {
                // 计算代金券
                coupons[i].total = that.coupon_price(coupons[i],subtotal)
            }
            // 合并付款金额
            var merge_price = (parseFloat(subtotal.paytotal)-parseFloat(res.data.rs.user_balance)).toFixed(2)

            // 有商户标识，允许配送方式为自提
            var deliverys = res.data.rs.deliverys;
            /*var fromMark = app.getFromMark();
            if(fromMark.fr=='business' && app.UTIL.isNull(fromMark.ch) == false){
                deliverys.push({
                    delivery_id: 'self',
                    delivery_name: '自提',
                    delivery_code: 'self',
                    delivery_desc: '线下自提',
                    is_default: 0
                });
            }*/
            that.setData({
                cart_ids:cart_ids,
                cartList: res.data.rs.carts,
                userDefaultAddress: userDefaultAddress,
                deliverys: deliverys,
                coupons: coupons,
                subtotal: subtotal,
                product_ids: res.data.rs.product_ids,
                params: params,
                user_balance: res.data.rs.user_balance,
                merge_price: merge_price
            })
            that.checkCartStock()

            that.getAvailableVoucher();
          }else{
            wx.showModal({
              title: '提示',
              content: res.data.msg,
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

  // api获取套餐数据
  getCombomealOrderParams:function(){
      var that = this,
          userDefaultAddress = this.data.userDefaultAddress,
          address_id = userDefaultAddress.id ? userDefaultAddress.id : '',
          params = this.data.params,
          combomeal_id = this.data.combomeal_id,
          combomeal = this.data.combomeal

      if(combomeal == undefined || combomeal == ''){
          wx.showModal({
            title: '参数错误',
            content: '请返回套餐页面选择需要的商品规格',
            showCancel: false,
            success: function(res) {
              if (res.confirm) {
                wx.navigateBack()
              }
            }
          })
          return false
      }

      // loading
      wx.showToast({
        title: '加载中',
        icon: 'loading',
        duration: 10000,
        mask:true
      })

      app.API.getJSON({c:'Order',a:'combomealParams',combomeal:combomeal,combomeal_id:combomeal_id,address_id:address_id,sessionId:that.data.user.sessionId},function(res){
          wx.hideToast();
          wx.stopPullDownRefresh();
          if(res.data.code==0 && res.data.rs.carts.length > 0){
            if(userDefaultAddress==0 || userDefaultAddress.length == 0){
                userDefaultAddress = res.data.rs.userDefaultAddress
            }
            // 价格
            var subtotal = {
              goodstotal:res.data.rs.goodstotal,
              cuttotal:res.data.rs.cuttotal,
              paytotal:res.data.rs.paytotal.toFixed(2),
              ship_fee:res.data.rs.ship_fee,
              coupon_price:res.data.rs.coupon_price
            }
            // 参数
            params.shipping = res.data.rs.delivery_id
            // 代金券金额
            var coupons = res.data.rs.coupons
            for (var i = 0; i < coupons.length; i++) {
                // 计算代金券
                coupons[i].total = that.coupon_price(coupons[i],subtotal)
            }
            // 合并付款金额
            var merge_price = (parseFloat(subtotal.paytotal)-parseFloat(res.data.rs.user_balance)).toFixed(2)
            that.setData({
                // cart_ids:cart_ids,
                cartList: res.data.rs.carts,
                userDefaultAddress: userDefaultAddress,
                deliverys: res.data.rs.deliverys,
                coupons: coupons,
                subtotal: subtotal,
                product_ids: res.data.rs.product_ids,
                params: params,
                user_balance: res.data.rs.user_balance,
                merge_price: merge_price
            })
            that.checkCartStock()
          }else{
            wx.showModal({
              title: '提示',
              content: res.data.msg,
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

  // 检查购物车库存
  checkCartStock:function(){
      var cartList = this.data.cartList
      var no_quantity = ''

      for (var i = 0; i < cartList.length; i++) {
          if(cartList[i].stock == 0 || parseInt(cartList[i].quantity) > parseInt(cartList[i].siglequantity)){
              no_quantity += no_quantity ? ";\r\n" : '';
              no_quantity += cartList[i].product_name+' '+cartList[i].color_prop+cartList[i].size_prop+' 库存不足';
          }
      }

      if(no_quantity != ''){
          wx.showModal({
            title: '缺货提示',
            content: no_quantity,
            showCancel: false,
            success: function(res) {
              if (res.confirm) {
                wx.navigateBack()
              }
            }
          })
          return false
      }
  },


  // 弹出层
  showAddress: function(e){
    this.setData({
        addressLayerClass: 'show'
    })
  },
  hideAddress: function(){
    this.setData({
        addressLayerClass: 'hide'
    })
  },

  // rowBlockTap
  rowBlockTap:function(e){
      var type = e.currentTarget.dataset.type,
          arrow = this.data.arrow,
          display = this.data.display,
          obj = this.data.arrow[type]

      if(obj == 'arrow'){
          arrow[type] = 'arrow2'
          display[type] = 'display-block'
      }else{
          arrow[type] = 'arrow'
          display[type] = 'display-none'
      }

      this.setData({
        arrow:arrow,
        display:display
      })
  },

  // 设置参数
  setParams:function(e){
      var that = this,
          key = e.currentTarget.dataset.key,
          val = e.detail.value,
          params = this.data.params,
          text = this.data.text,
          subtotal = this.data.subtotal,
          radioChecked = this.data.radioChecked

      if(key && val){
          switch(key){
              case 'payment':
                  // 合并付款须知提示
                  if(val == 'merge'){
                      wx.showModal({
                        title:'合并付款须知',
                        content:'您正在使用合并付款，若该订单需要退换货，退款将退回帐户余额，不能提现与转赠。',
                        cancelText:'取消',
                        confirmText:'同意使用',
                        success: function(res){
                            if (res.confirm) {
                                // 设置选中值
                                params[key] = val
                                text[key] = that.data.payment[val]
                                that.setData({
                                    params:params,
                                    text:text
                                })
                            }else{
                                radioChecked = {
                                    weixin:false,
                                    bag:false,
                                    merge:false
                                }
                                radioChecked[params['payment']] = true
                                that.setData({
                                    radioChecked:radioChecked
                                })
                            }
                        }
                      })
                      return false
                  }else{
                    // 设置选中值
                    params[key] = val
                    text[key] = that.data.payment[val]
                  }
                  break;

              case 'shipping':
                  // 设置选中值
                  params[key] = val

                  for (var i = 0; i < that.data.deliverys.length; i++) {
                    if(that.data.deliverys[i].delivery_id == val){
                      text[key] = that.data.deliverys[i].delivery_name
                      // 计算运费
                      that.getShipFee(val)
                      that.setData({
                          params:params,
                          text:text
                      })
                    }
                  }
                  return false
                  break;

              case 'voucher':
                  for (var i = 0; i < that.data.voucher.length; i++) {
                    if(that.data.voucher[i].voucher_id == val){
                      text[key] = that.data.voucher[i].title
                      // 计算代金券[or 上不封顶]
                      subtotal.coupon_price = that.data.voucher[i].discount_total;
                    }
                  }
                  // 设置选中值
                  params[key] = val
                  break;
          }
          

          // 计算支付金额（商品总价+运费+活动优惠-优惠券）
          subtotal.paytotal = (parseFloat(subtotal.goodstotal)+parseFloat(subtotal.ship_fee)-parseFloat(subtotal.cuttotal)-parseFloat(subtotal.coupon_price)).toFixed(2)
          var merge_price = (parseFloat(subtotal.paytotal)-parseFloat(that.data.user_balance)).toFixed(2)
          that.setData({
              params:params,
              text:text,
              subtotal:subtotal,
              merge_price:merge_price
          })
      }
  },

  // 计算运费
  getShipFee:function(delivery_id){
      var that = this,
          subtotal = this.data.subtotal,
          product_ids = this.data.product_ids.join()

      wx.showToast({
        title: '计算运费',
        icon: 'loading',
        duration: 10000,
        mask:true
      })
      app.API.getJSON({c:'Order',a:'getShipFee',sessionId:that.data.user.sessionId,product_ids:product_ids,address_id:that.data.userDefaultAddress.id,price:subtotal.paytotal,delivery_id:delivery_id},
        function(res){
            wx.hideToast()
            if(res.data.code == 0){
                subtotal.ship_fee = res.data.rs
                subtotal.paytotal = (parseFloat(subtotal.goodstotal)+parseFloat(subtotal.ship_fee)-parseFloat(subtotal.cuttotal)-parseFloat(subtotal.coupon_price)).toFixed(2)
                var merge_price = (parseFloat(subtotal.paytotal)-parseFloat(that.data.user_balance)).toFixed(2)
                that.setData({
                    subtotal:subtotal,
                    merge_price:merge_price
                })
            }
        })
  },

  // 计算代金券优惠金额
  coupon_price:function(coupon,subtotal){
      var price = 0
      if(subtotal == undefined || subtotal == ''){
        var subtotal = this.data.subtotal
      }
      if(coupon.not_top == 1){
          var coupon_price = parseFloat(coupon.coupon_price)
          var price_limit = parseFloat(coupon.price_limit)
          var goodstotal = parseFloat(subtotal.goodstotal)-parseFloat(subtotal.cuttotal)
          price = coupon_price*parseInt(goodstotal/price_limit)
      }else{
          price = parseFloat(coupon.coupon_price)
      }

      return price
  },


  // 弹出层
  showLayer: function(){
    this.setData({
        popLayerClass: 'show'
    })
  },

  // 隐藏层取消支付
  hideLayer: function(){
    var order_sn = this.data.newOrder.order_sn,
        order_id = this.data.newOrder.order_id

    wx.redirectTo({
        url: '../order/complete?order_sn='+order_sn+'&order_id='+order_id+'&method=weixin'
    })
  },

  // 提交订单
  submitOrder: function(e){
    var that = this,
        url = '',
        params = this.data.params,
        combomeal_id = this.data.combomeal_id,
        combomeal = this.data.combomeal

    // 取来源标识
    var fromMark = app.getFromMark();

    // 保存模板消息表单凭证
    app.saveTemplateSign(e.detail.formId, this.data.user.sessionId, 'order');

    var data = {
        sessionId:that.data.user.sessionId,
        cart_ids:that.data.cart_ids,
        payment:params.payment,
        address_id:that.data.userDefaultAddress.id,
        delivery_id:params.shipping,
        voucher_id:params.voucher ? params.voucher :'',
        buyer_note:e.detail.value.buyer_note,
        fr:fromMark.fr,
        ch:fromMark.ch,
        combomeal_id: combomeal_id,
        combomeal: combomeal
    }
    // loading
    wx.showToast({
      title: '提交中',
      icon: 'loading',
      duration: 10000,
      mask:true
    })

    if (combomeal_id != undefined && combomeal_id != '')
        url = 'index.php?c=Order&a=createCombomealOrder'
    else
        url = 'index.php?c=Order&a=createOrder'

    // api生成订单
    app.API.postDATA(data,function(res){
        wx.hideToast()
        if(res.data.code == 0){
            // 0元自动改为钱包支付
            if(res.data.recharge == 0){
              res.data.method = 'bag'
            }
            // 成功后跳转
            var newOrder = {
                order_sn: res.data.order_sn,
                method: res.data.method,
                order_id: res.data.order_id,
                timeStamp: res.data.timeStamp,
                salt: res.data.salt
              }

            // 保存新订单数据
            that.setData({
                newOrder:newOrder
            })

            if(newOrder.method == 'weixin'){
                // 微信付款
                that.wxPay()

            }else if(newOrder.method == 'bag'){
                // 钱包付款
                that.showLayer()

            }else if(newOrder.method == 'merge'){
                // 合并付款
                that.mergePay()

            }else{
                wx.showModal({
                  title: '创建订单成功',
                  content: '您没有选择支付方式，稍后可以进入我的订单重新付款。',
                  showCancel: false,
                  success:function(res){
                      if (res.confirm) {
                          wx.redirectTo({
                              url: '../order/complete?order_sn='+newOrder.order_sn+'&order_id='+newOrder.order_id+'&method=weixin'
                          })
                      }
                  }
                })
            }

        }else{
            wx.showModal({
              title: '提示',
              content: res.data.msg,
              showCancel: false
            })
        }
    }, url)
  	
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
                    // console.log('------success------')
                 },
                 fail:function(rs){
                    // console.log('------fail------')
                    // console.log(rs)
                 },
                 complete:function(rs){
                    // console.log('------complete------')
                    // console.log(rs)
                    wx.redirectTo({
                        url: '../order/complete?order_sn='+order_sn+'&order_id='+order_id+'&method=weixin'
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


  // 钱包支付方式
  checkBagSubmit:function(e){
      var that = this,
          order_sn = this.data.newOrder.order_sn,
          order_id = this.data.newOrder.order_id,
          timeStamp = this.data.newOrder.timeStamp,
          salt = this.data.newOrder.salt,
          password = e.detail.value.password

      if(password == undefined || password == ""){
          wx.showModal({
            title: '请输入密码',
            content: '支付密码与登录密码一样，忘记密码可退出帐号，在登录界面发起找回密码。',
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
      app.API.postDATA({sn:order_sn,password:password,timeStamp:timeStamp,salt:salt,sessionId:that.data.user.sessionId},function(res){
          wx.hideToast()
          if(res.data.code == 0){
              // 更新本地登录缓存数据
              app.updateUserInfo(that.data.user.sessionId,function(){
                  wx.redirectTo({
                      url: '../order/complete?order_sn='+order_sn+'&order_id='+order_id+'&method=weixin'
                  })
              })
          }else if(res.data.code == "-2"){
              wx.showModal({
                title: '提示',
                content: res.data.msg,
                showCancel: false,
                success:function(rs){
                    if(rs.confirm){
                        wx.redirectTo({
                            url: '../order/complete?order_sn='+order_sn+'&order_id='+order_id+'&method=weixin'
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

  },


  // 微信支付方式
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
                 success:function(rs){
                    // console.log('------success------')
                    // console.log(rs)
                 },
                 fail:function(rs){
                    // console.log('------fail------')
                    // console.log(rs)
                 },
                 complete:function(rs){
                    // console.log('------complete------')
                    // console.log(rs)
                    wx.redirectTo({
                        url: '../order/complete?order_sn='+order_sn+'&order_id='+order_id+'&method=weixin'
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


  // 2018-09-25 获取可用优惠券（新）
  getAvailableVoucher(){
    var that = this;
    var arrow_coupon = this.data.arrow.coupon;
    var display_coupon = this.data.display.coupon;
    var pay_total = this.data.subtotal.paytotal;
    // 1. 请求支付
    app.API.postDATA({pay_total:pay_total,sessionId:this.data.user.sessionId},function(res){
      if(res.data.code == 0){
        if( res.data.rs.length > 0 ){
          arrow_coupon = 'arrow2'
          display_coupon = 'display-block'
        }
        that.setData({
          voucher: res.data.rs,
          ['arrow.coupon']: arrow_coupon,
          ['display.coupon']: display_coupon,
        });
      }
    },'index.php?c=voucher&a=getAvailableVoucherGroup')
  },


  // 下拉刷新
  onPullDownRefresh: function() {
    var that = this,
        combomeal_id = this.data.combomeal_id,
        combomeal = this.data.combomeal,
        options = {}

    if(combomeal_id != undefined && combomeal_id != '')
        options = {
            combomeal_id: combomeal_id,
            combomeal: combomeal
        }
    else
        options = {
          cart_ids: this.data.cart_ids
        }
    this.onLoad(options)
  }
  
})