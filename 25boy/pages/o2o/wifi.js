var app = getApp();

Page({

  /**
   * 页面的初始数据
   */
  data: {
      SSID: 'he752',
      BSSID: '24:69:68:0B:66:7A',
      password: 'H@rdlyEvers2015',
      wifiImg:''
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.getWifiInfo();
  },


  getWifiInfo: function(){
      var that = this;
      var path = encodeURI('pages/o2o/wifi');
      app.API.getJSON({type:'',path:path},function(res){
          if(res.data.code == 0){
              var img = app.API.BASE_URL + res.data.rs;
              that.setData({
                  wifiImg: img
              });
          }
      },'index.php?c=Qrcode&a=weapp');
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  connectWifi: function () {
    var that = this;
    wx.startWifi({
      success: function(res) {
        wx.connectWifi({
          SSID: that.data.SSID,
          BSSID: that.data.BSSID,
          password: that.data.password,
          success: function(res) {
            wx.showToast({
              title: '连接成功！',
              icon: 'success',
              duration: 3000
            })
          },
          complete: function(res) {
            wx.showToast({
              title: '连接失败',
              icon: 'none',
              duration: 3000
            })
          }
        })
      },
      complete: function(res) {
        console.log(res)
      }
    })
  }
})