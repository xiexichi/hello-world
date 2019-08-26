var app = getApp();

Page({

  /**
   * 页面的初始数据
   */
  data: {
      user: [],
      withdrawList:[],
      popLayerClass: 'hide',
      withdrawType: ['支付宝'],
      pickerIndex: 0,
      editData: [],
      formLoading: false
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
                url:'/pages/public/login?gourl='+escape('/pages/users/withdrawModify')
              });
              return false;
          }else{
              that.setData({
                user:res
              });
              // 我的收益
              that.withdrawal();
          }
      });

      // 记录来源
      app.markFromSource(options,'pages/users/withdrawModify');
  },

  // 提现方式列表
  withdrawal: function(){
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
              that.setData({
                  withdrawList: res.data.rs
              });
          }
      },'index.php?c=promote&a=withdrawal');
  },


  // 弹出层
  showLayer: function(e){
      var that = this,
          type = e.currentTarget.dataset.type,
          id = e.currentTarget.dataset.id,
          withdrawList = that.data.withdrawList,
          editData = [];

      if(type == 'edit'){
          for (var i = 0; i < withdrawList.length; i++) {
              editData = withdrawList[i];
          }
      }

      that.setData({
          editData: editData,
          popLayerClass: 'show'
      })
  },
  hideLayer: function(){
      this.setData({
          popLayerClass: 'hide'
      });
  },

  // 选择提现方式
  bindPickerChange: function(e){
      var pickerIndex = e.detail.value;
      this.setData({
          pickerIndex: pickerIndex
      });
  },

  // 提交修改
  formSubmit: function(e){
      var that = this,
          params = e.detail.value,
          apiUrl = '';
      if(app.UTIL.isNull(params.pwithdrawal_id) == true){
          // 添加
          apiUrl = 'index.php?c=promote&a=newWithdrawal';
      }else{
          // 修改
          apiUrl = 'index.php?c=promote&a=updateWithdrawal';
      }
      // 暂时只支持支付宝提现
      params.withdrawal_type = 'alipay';
      params.sessionId = this.data.user.sessionId;

      if(app.UTIL.isNull(params.withdrawal_account) == true){
          wx.showModal({
              title: '提示',
              content: '提现帐户不能为空',
              showCancel: false
          });
          return false;
      }
      if(app.UTIL.isNull(params.withdrawal_name) == true){
          wx.showModal({
              title: '提示',
              content: '真实姓名不能为空',
              showCancel: false
          });
          return false;
      }

      that.setData({
          formLoading: true
      });
      app.API.postDATA(params, function(res){
          that.setData({
              formLoading: false
          });
          if(res.data.code == 0){
              wx.showToast({
                  title: '成功',
                  icon: 'success',
                  duration: 3000,
                  success: function(){
                      that.withdrawal();
                      that.hideLayer();
                  }
              });
          }else{
              wx.showModal({
                  title: '提示',
                  content: res.data.msg,
                  showCancel: false
              });
          }
      },apiUrl);
  },


  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {
  
  }

})