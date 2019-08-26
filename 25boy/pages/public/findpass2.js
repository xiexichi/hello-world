var app = getApp();

Page({
    data:{
        tempKey:'',
        btndoing: false
    },

    onLoad:function(options){
        if(options.key != undefined && options.key != ''){
            this.setData({
                tempKey:options.key
            })
        }else{
            wx.showModal({
              title: '错误提示',
              content: '参数错误，请重新发起找回密码。',
              showCancel: false,
              success: function(res) {
                if (res.confirm) {
                    wx.redirectTo({
                        url:'/pages/public/findpass'
                    })
                }
              }
            })
        }
    },


    // 提交修改密码
    formSubmit:function(e){
        var that = this
        var password = e.detail.value.password,
            password2 = e.detail.value.password2,
            tempKey = this.data.tempKey,
            error = ''

        if(password != password2){
            error = '两次输入密码不一致，请重新输入'
        }
        if(app.UTIL.checkPassword(password) == false || app.UTIL.checkPassword(password2) == false){
            error = '密码6-16位，只能输入数字、字母，横杠和下划线'
        }

        if(error != ''){
            wx.showModal({
              title: '提示',
              content: error,
              showCancel: false
            })
            return false
        }


        that.setData({
            btndoing:true
        })
        app.API.postDATA({password:password,tempKey:tempKey},function(res){
            if(res.data.code==0){
                wx.showModal({
                      title: '成功',
                      content: '您已成功修改密码，请使用新密码登录。',
                      showCancel: false,
                      success:function(rs){
                        if (rs.confirm) {
                           wx.navigateBack()
                        }
                      }
                })
            }else{
                wx.showModal({
                  title: '失败',
                  content: res.data.msg,
                  showCancel: false
                })
                that.setData({
                    btndoing:false
                })
            }
        },'index.php?c=User&a=findpassStep3')

    }

})