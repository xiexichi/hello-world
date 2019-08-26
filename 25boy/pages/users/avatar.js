var app = getApp();

Page({
  data:{
      user:[],
      windowWidth:375
  },

  onLoad:function(options){
      var that = this

      // 检查登录
      app.checkLogin(function(res){
          if(res.sessionId == undefined || res.sessionId == ''){
              wx.redirectTo({
                url:'/pages/public/login?gourl='+escape('/pages/users/avatar')
              })
              return false
          }else{
              that.setData({
                user:res
              })
          }
      })

      // 修改scroll-view高度
      that.setData({
          windowWidth:app.systemInfo.windowWidth
      });
  },


  // 上传图片
  uploadAvatar:function(e){
      var that = this,
          user = this.data.user

      // 从本地相册选择图片或使用相机拍照
      wx.chooseImage({
        count:1,
        sizeType:'compressed',
        sourceType:['album','camera'],
        success: function(rs) {
          console.log(rs)
          var tempFilePaths = rs.tempFilePaths
          if(tempFilePaths.length > 1){
              wx.showModal({
                title: '提示',
                content: '最多允许上传1张图片。',
                showCancel:false
              })
              return false
          }
          
          wx.showToast({
              title: '上传中',
              icon: 'loading',
              duration: 10000
          })

          // 上传接口
          wx.uploadFile({
            url: app.API.BASE_URL+'index.php?c=Upload&a=upyun',
            filePath: tempFilePaths[0],
            name: 'images',
            formData:{
              sessionId:user.sessionId,
              tree_id:'3',
              connport:'weapp'
            },
            success: function(res){
              var json = JSON.parse(res.data)
              console.log(json)
              if(json.code == 0){
                  // 更新本地登录缓存数据
                  app.updateUserInfo(user.sessionId)
                  user['image_url'] = json.rs[0].url
                  app.API.postDATA({image_url:user.image_url,sessionId:user.sessionId},function(rs){
                      wx.hideToast()
                      if(rs.data.code == 0){
                          that.setData({
                              user:user
                          })
                      }else{
                          wx.showModal({
                              title:'提示',
                              content:rs.data.msg,
                              showCancel:false
                          })
                      }
                  },'index.php?c=Member&a=editInfo')
              }else{
                  wx.hideToast()
                  wx.showModal({
                      title:'提示',
                      content:json.msg,
                      showCancel:false
                  })
              }
            },
            fail:function(res){
                console.log(res)
            }
          })
        }
      })
  },

  // 关闭当前页面，返回上一页面或多级页面
  back:function(){
    // 返回上一个小程序
    var extraData = app.globalData.extraData;
    if(app.UTIL.isNull(extraData.thirdapp) == false){
        wx.navigateBackMiniProgram();
    }else{
        wx.navigateBack();
    }
  }

})