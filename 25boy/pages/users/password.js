var app = getApp();

Page({
  data:{
    user:[]
  },

  onLoad:function(options){
  		var that = this
	    // 检查登录
      app.checkLogin(function(res){
          if(res.sessionId == undefined || res.sessionId == ''){
              wx.redirectTo({
                url:'/pages/public/login?gourl='+escape('/pages/users/password')
              })
              return false
          }else{
              that.setData({
                user:res
              })
          }
      })
  },

  // 修改密码
  formSubmit:function(e){
  		var that = this,
  			oldpassword = e.detail.value.old_password,
  			newpassword = e.detail.value.new_password,
  			newpassword2 = e.detail.value.new_password2

  		if(oldpassword == ''){
  			wx.showModal({
				  showCancel: false,
				  title: '提示',
				  content: '请输入旧密码，如果忘记请联系客服。'
				})
				return false
		}

  		if(app.UTIL.checkPassword(newpassword) == false){
			wx.showModal({
			  showCancel: false,
			  title: '提示',
			  content: '新密码6-16位，只能输入数字、字母，横杠和下划线'
			})
			return false
		}

		if(newpassword !== newpassword2){
  			wx.showModal({
			  showCancel: false,
			  title: '提示',
			  content: '新密码两次输入不一样，请重新输入。'
			})
			return false
		}

  		app.API.postDATA({oldPassword:oldpassword,password:newpassword,sessionId:that.data.user.sessionId},function(res){
  			if(res.data.code == 0){
            	wx.showToast({
	                title:'修改成功',
	                icon:'success',
	                duration:2000,
	                success:function(){
	                	app.updateUserInfo(that.data.user.sessionId,function(){
		                    wx.navigateBack()
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
  		},'index.php?c=Member&a=editPassword')
  }

})