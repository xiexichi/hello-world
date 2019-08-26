var app = getApp();

Page({
    data:{
        btndoing:false,
        gourl:'/pages/public/login',
        account:'',
        codeDisabled:false,
        codeTxt:'发送验证码',
        navbarClassPhone: 'weui-bar__item_on',
        navbarClassEmail: '',
        findTitle:'通过手机找回密码',
        type:'phone',
        second:60
    },

    onLoad:function(options){
        if(options.gourl != undefined && options.gourl != ''){
            this.setData({
                gourl:unescape(options.gourl)
            })
        }
    },
    

    // 设置手机号，用于获取短信验证码
    setAccount:function(e){
        this.setData({
            account:e.detail.value
        })
    },

    // 获取短信验证码
    getCode:function(e){
        var that = this,
            account = this.data.account,
            type = this.data.type

        /*if(type=='phone' && app.UTIL.checkPhone(account) == false){
            wx.showModal({
              title: '提示',
              content: '手机号码格式不正确',
              showCancel: false
            })
            return false
        }

        if(type=='email' && app.UTIL.checkEmail(account) == false){
            wx.showModal({
              title: '提示',
              content: '邮箱地址格式不正确',
              showCancel: false
            })
            return false
        }*/

        that.setData({
            codeDisabled:true,
            second:60
        })
        var timestamp = new Date().getTime();
        // 发送验证码
        app.API.postDATA({account:account,timestamp:timestamp},function(res){
            if(res.data.code == 0){
                // that.setData({
                //     codeTxt:'已发送'
                // })
                that.regetCodeTime()
            }else{
                wx.showModal({
                  title: '提示',
                  content: res.data.msg,
                  showCancel: false
                })
                that.setData({
                    codeDisabled:false
                })
            }
        },'index.php?c=User&a=findpass')
    },

    // 下一步
    nextSubmit:function(e){
        var that = this
        var account = e.detail.value.account,
            msgcode = e.detail.value.msgcode,
            type = this.data.type,
            error = ''

        if(msgcode == '' || msgcode == undefined){
            error = '请输入验证码'
        }
        /*if(type=='phone' && app.UTIL.checkPhone(account) == false){
            error = '手机号码格式不正确'
        }
        if(type=='email' && app.UTIL.checkEmail(account) == false){
            error = '邮箱地址格式不正确'
        }*/

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
        app.API.postDATA({account:account,code:msgcode},function(res){
            if(res.data.code==0){
                wx.redirectTo({
                    url:'/pages/public/findpass2?key='+res.data.tempKey
                })
            }else{
                wx.showModal({
                  title: '提示',
                  content: res.data.msg,
                  showCancel: false
                })
                that.setData({
                    btndoing:false
                })
            }
        },'index.php?c=User&a=findpassStep2')
    },


    // navbar高亮
    switchNavbar: function(e){
        var type = e.currentTarget.dataset.type

        switch(type){
            case 'email':
              this.setData({
                  type: 'email',
                  findTitle: '通过邮箱找回密码',
                  navbarClassPhone: '',
                  navbarClassEmail: 'weui-bar__item_on'
              })
              break

            default:
              this.setData({
                  type:'phone',
                  findTitle: '通过手机找回密码',
                  navbarClassPhone: 'weui-bar__item_on',
                  navbarClassEmail: ''
              })
              break
        }
    },


    // 返回上页
    goback:function(){
        var that = this
        wx.navigateBack();
    },


    // 重新获取验证码倒计时
    regetCodeTime: function(){
        var that = this,
            second = that.data.second

        if (second == 0) {
            that.setData({
                codeTxt: "发送验证码",
                codeDisabled: false
            });
            return ;
        }
        var time = setTimeout(function(){
            that.setData({
                second: second-1,
                codeTxt: second+'秒后重发'
            });
            that.regetCodeTime();
        },1000)
    }

})