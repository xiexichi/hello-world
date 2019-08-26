var app = getApp();

Page({

  /**
   * 页面的初始数据
   */
  data: {
    code: '',
    sessionKey: '',
    displayLayer: 'hide',
    windowHeight: 600
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var that = this
    wx.showLoading({
      mask: true,
      title: '加载中'
    })
    // 检查登录
    app.checkLogin(function(res){
      console.log(res)
      var sessionKey = ''
      if( app.UTIL.isNull(res.sessionId) == true ){
        sessionKey = res.rs.sessionKey
      }else{
        sessionKey = res.sessionKey
      }
      that.setData({
        windowHeight: app.systemInfo.windowHeight,
        sessionKey: sessionKey
      })
      that.findCode()
    })

    // 记录来源
    app.markFromSource(options,'pages/activity/jinli');
  },

  // 获取抽奖码
  getCode: function () {
    var that = this
    app.API.postDATA({session_key: this.data.sessionKey}, function(res){
      if(res.data.code == 0){
        var info  = res.data.info
        var code = info.code || ''
        that.setData({
          code: code
        })
      }
    },'index.php?c=lotter&a=get_lotter_code')
  },

  // 查询抽奖码
  findCode: function() {
    var that = this
    app.API.postDATA({session_key: this.data.sessionKey}, function(res){
      if(res.data.code == 0){
        var info  = res.data.info
        var code = info.code || ''
        that.setData({
          code: code
        })
      }
      wx.hideLoading()
    },'index.php?c=lotter&a=check_lotter_info')
  },

  // 保存图片到相册
  savePosterImage: function() {
    var that = this
    wx.showLoading({
      mask: true,
      title: '加载中'
    })
    wx.getImageInfo({
      src: 'https://api.25boy.cn/Public/images/jinli-share.jpg',
      success (res) {
        wx.saveImageToPhotosAlbum({
          filePath: res.path,
          success() {
            wx.hideLoading()
            that.hideLayer()
            wx.showModal({
              showCancel: false,
              title: '保存成功',
              content: '专属图片已经保存到手机相册，把图片发到朋友圈就可以了哦~'
            })
          }
        })
      }
    })
  },

  // 显示层
  markPosterImage: function(){
    this.setData({
      displayLayer: 'show'
    })
  },
  
  // 隐藏层
  hideLayer: function(){
    this.setData({
      displayLayer: 'hide'
    })
  },
  
  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {

  }
})