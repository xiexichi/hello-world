var app = getApp();

Page({
  data:{
      user:[],
      rechargeSet:[],
      rechargeBox:[],
      recharge_price:'',
      plus_price:0,
      business_name:'',
      emptyRecharge:true,
      showHomeBtn:true,
      footerData:[]
  },

  onLoad:function(options){
      // 取来源标识
      var that = this,
          fromMark = app.getFromMark();

      if(fromMark.fr == 'business'){
          app.API.getJSON({code: fromMark.ch},function(res){
              if(res.data.code == 0 && app.UTIL.isNull(res.data.rs.business_name) == false){
                  that.setData({
                      business_name : res.data.rs.business_name
                  });    
              }
          },'index.php?c=Origin&a=getBusiness');
      }

      // 记录来源
      app.markFromSource(options,'pages/users/recharge')
  },


  onShow: function(){
      var that = this;

      // 检查登录
      app.checkLogin(function(res){
          if(res.sessionId == undefined || res.sessionId == ''){
              wx.redirectTo({
                url:'/pages/public/login?gourl='+escape('/pages/users/recharge')
              })
              return false
          }else{
              that.setData({
                user:res,
                footerData:app.footerData
              })
              that.getRechargeSet()
          }
      });
  },

  // api获取充值活动
  getRechargeSet:function(){
      var that = this,
          emptyRecharge = this.data.emptyRecharge,
          rechargeBox = new Array();

      // 取来源标识
      var fromMark = app.getFromMark();

      wx.showToast({
          title: '加载中',
          icon: 'loading',
          duration: 10000,
          mask: true
      });
      app.API.getJSON({fr:fromMark.fr,ch:fromMark.ch,sessionId:that.data.user.sessionId},function(res){
          wx.hideToast();
          wx.stopPullDownRefresh();
          if(res.data.code == 0){
              if(app.UTIL.isNull(res.data.rs.recharge_value)){
                  emptyRecharge = true;
              }else{
                  for (var i = 0; i <= res.data.rs.recharge_value.length; i++) {
                      rechargeBox.push('rechargeBox-'+i)
                  }
                  emptyRecharge = false;
              }
              that.setData({
                  rechargeSet:res.data.rs,
                  rechargeBox:rechargeBox,
                  emptyRecharge:emptyRecharge
              })
          }
      },'index.php?c=Bag&a=rechargeSet')
  },

  // 选择充值金额
  selectRecharge:function(e){
      var rechargeBox = new Array(),
          id = e.currentTarget.dataset.idx,
          price = e.currentTarget.dataset.price,
          plus = e.currentTarget.dataset.plus

      for (var i = 0; i <= this.data.rechargeBox.length; i++) {
          if(i == id){
              rechargeBox[i] = 'recharge-item-selected'
          }else{
              rechargeBox[i] = 'rechargeBox'
          }
      }
      this.setData({
          rechargeBox:rechargeBox,
          recharge_price:price,
          plus_price:plus
      })
  },


  // 输入充值金额
  changeRecharge:function(e){
      var total_fee = parseFloat(e.detail.value),
          rechargeSet = this.data.rechargeSet,
          plus_price = 0

      // 显示赠送金额
      if(rechargeSet.recharge_id>0){
          if(rechargeSet.not_top==1){
              // 上不封顶，递增计算
              var minValue = parseFloat(rechargeSet.recharge_value[0])
              var minPrice = parseFloat(rechargeSet.recharge_price[0])
              // 如果没超过最小值，没优惠
              if (total_fee >= minValue) 
                  plus_price = parseInt((total_fee - minValue) / rechargeSet.step_value) * rechargeSet.step_price + minPrice;
          }else{
              //分层优惠
              var layer = rechargeSet.recharge_value.length
              var recharge_value = rechargeSet.recharge_value
              var recharge_price = rechargeSet.recharge_price
              var total = new Array() //优惠
              var current_price = total_fee //充值金额
              for (var i=0; i < layer; i++) { 
                  var value = recharge_value[i];
                  var price = recharge_price[i];

                  if(parseInt(value) == 0 || parseInt(price )== 0)
                      continue;

                  if(current_price >= value)
                      total[i] = price;
              }

              // 返回最大优惠值
              plus_price = Math.max(total);
          }
      }

      this.setData({
          recharge_price:total_fee,
          plus_price: plus_price
      })
  },

  // 提交充值
  submitRecharge:function(e){
      var that = this,
          formId = e.detail.formId,
          sessionId = this.data.user.sessionId,
          money = parseFloat(this.data.recharge_price);

      // 取来源标识
      var fromMark = app.getFromMark();

      if(isNaN(money)){
          wx.showModal({
              title:'输入错误',
              content:'充值金额只能输入数字',
              showCancel:false
          })
      }else{

          // 保存模板消息表单凭证
          app.saveTemplateSign(formId, sessionId, 'recharge');

          wx.showToast({
            title: '请稍候',
            icon: 'loading',
            duration: 10000
          })
          app.API.postDATA({recharge:money,method:'weixin',sessionId:sessionId,fr:fromMark.fr,ch:fromMark.ch},function(res){
              if(res.data.code == 0){
                  that.wxPay(res.data.order_sn,res.data.bag_id)
              }else{
                  wx.showModal({
                      title:'提示',
                      content:res.data.msg,
                      showCancel:false
                  })
              }
          },'index.php?c=Bag&a=recharge')

      }
  },

  // 发起充值支付
  wxPay:function(order_sn,bag_id){
      var that = this;
      var sessionId = this.data.user.sessionId;
      var extraData = app.globalData.extraData;

      // 1. 请求支付
      app.API.postDATA({sn:order_sn,sessionId:sessionId},function(res){
          wx.hideToast()
          if(res.data.code == 0){
              var wxorder = JSON.parse(res.data.rs)

              // 保存模板消息表单凭证
              var arr = wxorder.package.split('=');
              app.saveTemplateSign(arr[1], sessionId, 'pay');

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
                        if(app.UTIL.isNull(extraData.thirdapp) == false){
                          // 统一支付， 返回上一个小程序
                          wx.navigateBackMiniProgram({
                            extraData: {
                              type: 'recharge',
                              payStatus: 'complete'
                            }
                          })
                        }else{
                          // 更新本地登录缓存数据
                          app.updateUserInfo(that.data.user.sessionId,function(res){
                              wx.redirectTo({
                                  url: '/pages/users/wallet?type=prepaid'
                              })
                          })
                        }
                      }
                    })
                 },
                 fail:function(rs){
                    wx.showModal({
                        title:'付款失败',
                        content:'有疑问请联系在线客服：he75he(微信号)，3001188639(QQ)',
                        showCancel:false,
                        complete:function(){
                          if(app.UTIL.isNull(extraData.thirdapp) == false){
                            // 统一支付， 返回上一个小程序
                            wx.navigateBackMiniProgram({
                              extraData: {
                                type: 'recharge',
                                payStatus: 'fail'
                              }
                            })
                          }else{
                            wx.redirectTo({
                              url: '/pages/users/wallet?type=prepaid'
                            })
                          }
                        }
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
    this.getRechargeSet()
  },

  // 进入首页按钮拖动事件
  homeBtnTouchMove:function(e){
    this.setData({
        footerData : app.homeBtnTouchMoveFun(e)
    })
  },
  // 拖动结束
  homeBtnTouchMoveEnd:function(e){
    this.setData({
        footerData : app.homeBtnTouchMoveEndFun(e)
    })
  },
  // 点击事件
  homeBtnClick:function(){
      wx.switchTab({
        url:'/pages/index/index'
      })
  }


})