var app = getApp();

Page({
  data:{
    user:[],
    popLayerClass: 'hide',
    genderarr:['男','女'],
    genderKeyArr:['male','female'],
    txtType:'',
    txtValue:'',
    txtPaceholder:'请输入真实姓名',
    version: app.version
  },

  onLoad:function(options){
		var that = this

		// 检查登录
		app.checkLogin(function(res){
		  if(res.sessionId == undefined || res.sessionId == ''){
		      wx.redirectTo({
		        url:'/pages/public/login?gourl='+escape('/pages/users/setting')
		      })
		      return false
		  }else{
		      that.setData({
		        user:res
		      })
    	  }
      })
  },

  // 弹出层
  showLayer: function(e){
  	var type = e.currentTarget.dataset.type;
    var txtValue = e.currentTarget.dataset.val;
    var txtPaceholder = this.data.txtPaceholder;

    switch(type){
        case 'email':
            txtPaceholder = '请输入电子邮箱';
            break;
        case 'realname':
            txtPaceholder = '请输入真实姓名';
            break;
        default:
            break;
    }
    this.setData({
    		txtType:type,
        txtValue:txtValue,
        popLayerClass: 'show',
        txtPaceholder:txtPaceholder
    })
  },
  hideLayer: function(){
    this.setData({
        popLayerClass: 'hide'
    })
  },


  // pop修改文本
  changePopText:function(e){
  		var value = e.detail.value.txt,
  				field = this.data.txtType

      this.apiEditUserInfo(field,value)
  },


  // 修改性别
  changeGender:function(e){
      var idx = e.detail.value
      var gender = this.data.genderKeyArr[idx]

      if(gender != undefined && gender != ''){
          this.apiEditUserInfo('gender',gender)
      }
  },

  // 修改生日
  changeBirthday:function(e){
      var val = e.detail.value
      if(val != undefined && val != ''){
          this.apiEditUserInfo('birthday',val)
      }
  },


  // 统一修改资料
  apiEditUserInfo:function(field,value){
      var that = this,
          user = this.data.user

      app.API.postDATA({txtType:field,val:value,sessionId:user.sessionId},function(res){
          if(res.data.code == 0){
              that.hideLayer()
              // 更新本地登录缓存数据
              app.updateUserInfo(user.sessionId)
              user[field] = value
              if (field == 'nickname')
                user['is_rename'] = res.data.is_rename
              that.setData({
                  user:user
              })
              wx.showToast({
                  title:'修改成功',
                  icon:'success',
                  duration:2000
              })
          }else{
              wx.showModal({
                  title:'提示',
                  content:res.data.msg,
                  showCancel:false
              })
          }
      },'index.php?c=Member&a=editInfo')
  },

  // 退出登录
  logout:function(){

      try {
          var sessionKey = wx.getStorageSync('sessionKey');
          // 清理本地数据缓存
          app.globalData.userInfo = [];
          wx.clearStorageSync();
          app.setNavBarStatus();

          wx.showToast({
            title: '注销登录',
            icon: 'loading',
            duration: 10000,
            mask:true
          });
          app.API.getJSON({type:'weapp',sessionId:this.data.user.sessionId,sessionKey:sessionKey},function(res){
              wx.hideToast();
              // 返回首页
              wx.switchTab({
                  url:'/pages/index/index'
              })
          },'index.php?c=User&a=logout');

      } catch(e) {
          wx.showModal({
              title: '提示',
              content: '发生异常，请重新尝试。',
              success: function(res) {
                if (res.confirm) {
                    wx.switchTab({
                        url:'/pages/index/index'
                    })
                }
              }
          })
      }
  },

  // 打开系统设置
  openSetting:function(){
    if(wx.openSetting){
        wx.openSetting({
            success:function(res){
                console.log(res)
            }
        });
    }
  },


  // 下拉刷新
  onPullDownRefresh: function() {
      var that = this,
          user = this.data.user

      // 更新本地登录缓存数据
      app.updateUserInfo(user.sessionId,function(res){
          that.setData({
              user:res
          })
          wx.stopPullDownRefresh()
      })
  }


})