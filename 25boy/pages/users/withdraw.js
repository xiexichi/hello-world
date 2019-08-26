var app = getApp();

Page({

  /**
   * 页面的初始数据
   */
  data: {
      user:[],
      promote:[],
      is_withdrawal:true,
      withdrawList:[],
      withdrawIds:[],
      pickerIndex:0
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
      var that = this;

      // 检查登录
      app.checkLogin(function(res){
          if(res.sessionId == undefined || res.sessionId == ''){
              wx.redirectTo({
                url:'/pages/public/login?gourl='+escape('/pages/users/withdraw')
              });
              return false;
          }else{
              that.setData({
                user:res
              });
              // 我的收益
              that.earnings();
          }
      });

      // 记录来源
      app.markFromSource(options,'pages/users/withdraw');
  },

  onShow: function(){
      this.earnings();
  },

  // 我的收益
  earnings: function(){
      var that = this;

      // loading
      wx.showToast({
          title: '加载中',
          icon: 'loading',
          duration: 10000,
          mask:true
      });
      app.API.getJSON({sessionId:that.data.user.sessionId},function(res){
          wx.hideToast();
          if(res.data.code == 0){
              var withdrawList = new Array();
              var withdrawIds = new Array();
              var third = res.data.rs.all_withdrawal.third;
              if(third.length > 0){
                  for (var i = 0; i < third.length; i++) {
                    if(third[i].withdrawal_type == 'alipay'){
                        var getTo = '支付宝：' + third[i].withdrawal_account;
                    }
                    withdrawList.push(getTo);
                    withdrawIds.push(third[i].pwithdrawal_id);
                  }
              }
              that.setData({
                  promote: res.data.rs.promote,
                  is_withdrawal: !parseFloat(res.data.rs.withdrawal.is_withdrawal),
                  withdrawList: withdrawList,
                  withdrawIds: withdrawIds
              });
              if(that.data.is_withdrawal){
                  wx.showModal({
                      title: '不能提现',
                      content: res.data.rs.withdrawal.withdrawal_notice,
                      showCancel: false,
                      success: function(){
                          wx.navigateBack();
                      }
                  });
              }
          }
      },'index.php?c=promote&a=earnings');
  },


  // 选择提现方式
  bindPickerChange: function(e){
      var pickerIndex = e.detail.value;
      this.setData({
          pickerIndex: pickerIndex
      });
  },

  formSubmit: function(e){
      var that = this,
          withdrawIds = that.data.withdrawIds,
          idx = e.detail.value.idx,
          password = e.detail.value.password;

      var pwithdrawal_id = withdrawIds[idx];
      if(app.UTIL.isNull(pwithdrawal_id) == true){
          wx.showModal({
              title: '提示',
              content: '请选择提现帐户',
              showCancel: false
          });
          return false;
      }
      if(app.UTIL.isNull(password) == true){
          wx.showModal({
              title: '提示',
              content: '请输入帐户密码',
              showCancel: false
          });
          return false;
      }

      wx.showToast({
          title: '处理中',
          icon: 'loading',
          duration: 10000,
          mask:true
      });
      app.API.postDATA({pwithdrawal_id:pwithdrawal_id, password:password, sessionId:that.data.user.sessionId}, function(res){
          wx.hideToast();
          if(res.data.code == 0){
              wx.showModal({
                  title: '申请成功',
                  content: res.data.msg,
                  showCancel: false,
                  success: function(res){
                      // 不再提示提现弹层
                      wx.setStorage({
                          key:"not_again",
                          data:"promoteTips"
                      });
                      wx.navigateBack();
                  }
              });
          }else{
              wx.showModal({
                  title: '提示',
                  content: res.data.msg,
                  showCancel: false
              });
          }
      },'index.php?c=promote&a=applyWithdraw');
  }


})