var app = getApp();

Page({

    /**
    * 页面的初始数据
    */
    data: {
        user: {},
        giftCoupon:[],
        recharge_price: 0,
        note: '',
        after_price: 0,
        giftQuantity: 0,
        popLayerClass:'hide',
        banner: []
    },

    /**
    * 生命周期函数--监听页面加载
    */
    onLoad: function (options) {
        this.getBanner();
        // 记录来源
        app.markFromSource(options,'pages/activity/recharge');
    },

    /**
    * 生命周期函数--监听页面显示
    */
    onShow: function () {
        var that = this;

        wx.showToast({
            title: '检查登录',
            icon: 'loading',
            duration: 10000
        });

        // 检查登录
        app.checkLogin(function(res){
            wx.hideToast();
            if( app.UTIL.isNull(res.sessionId) == true ){
                wx.navigateTo({
                    url:'/pages/public/login?gourl=close'
                })
            }else{
                that.setData({
                    user:res
                })
                // that.getRechargeInfo()
            }
        });  
    },

    // 获取广告图片
    getBanner(){
        var that = this;
        app.API.getJSON({c:'Picshow',a:'getlist',posid:81,rowNo:3},function(res){
            wx.hideToast();
            if(res.data.code == 0){
                that.setData({
                    banner:res.data.rs.data
                })
            }
        });
    },

    // api获取充值活动
    getRechargeInfo:function(e){
      var that = this,
          recharge_price = parseFloat(e.detail.value);

      if(!recharge_price){
          return false;
      }

      // 取来源标识
      var fromMark = app.getFromMark();
      var business_code = '';
      if(fromMark.fr == 'business'){
        business_code = fromMark.ch;
      }

      wx.showToast({
          title: '加载中',
          icon: 'loading',
          duration: 10000,
          mask: true
      });
      app.API.request({
          business_code: business_code,
          token:this.data.user.sessionId,
          money: recharge_price
        },function(json){
          wx.hideToast();
          if(json.data.code == 0){
              var after_price = parseFloat(json.data.data.money) + parseFloat(json.data.data.plus_price);
              var gift_coupon = json.data.data.gift_coupon;
              var giftQuantity = 0;
              for(var i in gift_coupon){
                giftQuantity += gift_coupon[i].gift_quantity;
              }
              that.setData({
                note: json.data.data.note,
                giftQuantity: giftQuantity,
                after_price: after_price,
                giftCoupon: gift_coupon
              })
          }
      },'user/recharge/getRechargeInfo')
    },

    // 提交充值表单
    submitRecharge: function(e){
        var money = e.detail.value.money;
        var formId = e.detail.formId;
        var sessionId = this.data.user.sessionId;
        if(money < 200){
            wx.showToast({
                title: '充值金额200起',
                icon: 'none',
                duration: 1500,
                mask: true
            });
            return false;
        }

        // 保存模板消息表单凭证
        app.saveTemplateSign(formId, sessionId, 'recharge');

        // 取来源标识
        var fromMark = app.getFromMark();
        var business_code = '';
        if(fromMark.fr == 'business'){
          business_code = fromMark.ch;
        }

        wx.showToast({
            title: '正在提交',
            icon: 'loading',
            duration: 10000,
            mask: true
        });
        app.API.request({
            client: 'weapp',
            business_code: business_code,
            money: money,
            profession: 'shop',
            form_id: formId,
            token: sessionId
          },function(json){
            wx.hideToast();
            if(json.data.code == 0){
                var wxorder = json.data.data;
                // 保存模板消息表单凭证
                var arr = wxorder.package.split('=');
                app.saveTemplateSign(arr[1], sessionId, 'pay');
                // 发起付款
                wx.requestPayment({
                  'timeStamp':wxorder.timeStamp,
                  'nonceStr':wxorder.nonceStr,
                  'package':wxorder.package,
                  'signType':wxorder.signType,
                  'paySign':wxorder.paySign,
                  complete:function(){
                    wx.redirectTo({
                      url: '/pages/activity/rechargeComplete?bag_id='+wxorder.bag.bag_id
                    })
                  }
                })
            }else{
                wx.showToast({
                    title: json.data.msg,
                    icon: 'none',
                    duration: 1500,
                    mask: true
                });
            }
        },'wx/pay/recharge')
    },

    // 显示充值规则
    showRechargeRule: function(){
        this.setData({
            popLayerClass: 'show'
        })
    },

    // 隐藏充值规则
    hideRechargeRule: function(){
        this.setData({
            popLayerClass: 'hide'
        })
    },

    /**
    * 用户点击右上角分享
    */
    onShareAppMessage: function () {
      var path = '/pages/activity/recharge';

      if(this.data.user.promote_id != undefined && this.data.user.promote_id != ""){
          path = path+'?pid='+this.data.user.promote_id;
      }

      return {
        title: '充值送券活动',
        desc: '潮牌新国货，原创设计品牌。',
        path: path
      }
    }
})