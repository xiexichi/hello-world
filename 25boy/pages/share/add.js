var app = getApp();

Page({
  data:{
      user:[],
      showUpbtn:true,
      photos:[]
  },

  onLoad:function(options){
      var that = this

      // 检查登录
      app.checkLogin(function(res){
          if(res.sessionId == undefined || res.sessionId == ''){
              wx.redirectTo({
                url:'/pages/public/login?gourl='+escape('/pages/share/add')
              })
              return false
          }else{
              that.setData({
                user:res
              })
          }
      })

      // 记录来源
      app.markFromSource(options,'pages/share/add');
  },

  // 添加图片
  addImg:function(){
      var that = this;
      var photos = that.data.photos;

      if(photos.length >= 5){
          wx.showModal({
            title: '提示',
            content: '最多允许上传5张图片。',
            showCancel:false
          })
      }else{

          wx.chooseImage({
              count:5,
              sizeType:['compressed'],
              sourceType:['album','camera'],
              success: function(res) {
                  var tempFilePaths = res.tempFilePaths
                  if(tempFilePaths.length > 5 || (photos.length+tempFilePaths.length)>5){
                      wx.showModal({
                        title: '提示',
                        content: '最多允许上传5张图片。',
                        showCancel:false
                      })
                      return false
                  }

                  that.syncUpload(tempFilePaths);

                  /*for (var i = 0; i < tempFilePaths.length; i++) {
                    that.syncUpload(tempFilePaths[i],i,tempFilePaths.length);
                    // 停顿3秒
                    app.UTIL.sleep(1000);
                  }*/
              }
          })
      }
  },


  syncUpload:function(tempFilePaths){
      var that = this;
      var photos = that.data.photos;

      if(tempFilePaths.length == 0){
          wx.hideToast()
          return false;
      }

      wx.showToast({
          title: '上传中',
          icon: 'loading',
          duration: 10000,
          mask:true
      })

      wx.uploadFile({
          url: app.API.BASE_URL+'index.php?c=Upload&a=upyun',
          filePath: tempFilePaths[0],
          name: 'images',
          formData:{
              sessionId:that.data.user.sessionId,
              tree_id:'535',
              connport:'weapp'
          },
          success: function(res){
            var json = JSON.parse(res.data)
            if(json.code == 0){
                for (var j = 0; j < json.rs.length; j++) {
                  var images = {
                      url:json.rs[j].url,
                      file_id:json.rs[j].file_id
                  }
                  photos.push(images)
                }
                var showUpbtn = false
                if(photos.length < 5){
                    showUpbtn = true
                }
                that.setData({
                    showUpbtn:showUpbtn,
                    photos:photos
                })
                tempFilePaths.splice(0,1)
                that.syncUpload(tempFilePaths);
            }else{
                wx.showModal({
                    title:'提示',
                    content:json.msg,
                    showCancel:false
                })
            }
          },
          complete:function(){
              /*console.log((index+1) +'=='+ total)
              if((index+1) == total){
                wx.hideToast()
              }*/
          }
      })
  },


  // 移除图片
  delImg:function(e){
      var that = this,
          idx = e.currentTarget.dataset.idx,
          images = new Array()

      for (var i = 0; i < that.data.photos.length; i++) {
          if(i == idx){
              app.API.postDATA({file_id:that.data.photos[i].file_id,tree_id:535,sessionId:that.data.user.sessionId},function(res){
                  // code
              },'index.php?c=Upload&a=delFileByFileId')
          }else{
              images.push(that.data.photos[i])
          }
      }

      var showUpbtn = false
      if(images.length < 5){
          showUpbtn = true
      }
      that.setData({
          showUpbtn:showUpbtn,
          photos:images
      })
  },


  // 发布晒图
  submitForm:function(e){
      var that = this,
          content = e.detail.value.content


      if(content == undefined || content == ''){
          wx.showModal({
              title:'提示',
              content:'请输入分享内容。',
              showCancel:false
          })
          return false
      }

      var photos = new Array()
      for (var i in that.data.photos) {
          photos.push(that.data.photos[i].url)
      }

      if(photos.length == 0 || photos == ''){
          wx.showModal({
              title:'提示',
              content:'请上传图片，最多5张。',
              showCancel:false
          })
          return false
      }


      wx.showToast({
          title: '提交中',
          icon: 'loading',
          duration: 10000,
          mask:true
      })
      app.API.postDATA({content:content,photos:photos.join(','),sessionId:that.data.user.sessionId},function(res){
          wx.hideToast()
          console.log(res)
          if(res.data.code == 0){
              wx.showToast({
                  title: '成功',
                  icon: 'success',
                  duration: 1500,
                  complete:function(rs){
                      wx.redirectTo({
                        url: 'detail?id='+res.data.share_id
                      })
                  }
              })
          }else{
              wx.showModal({
                  title:'提示',
                  content:res.data.msg,
                  showCancel:false
              })
          }
      },'index.php?c=Share&a=createShare')
  },

  // 关闭当前页面，返回上一页面或多级页面
  back:function(){
      wx.navigateBack()
  }

})